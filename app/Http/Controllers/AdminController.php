<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Comment;
use App\Models\Demande;
use App\Models\Post;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Support\FeatureGate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /** Tableau de bord : indicateurs clés de la plateforme. */
    public function dashboard()
    {
        $now = now();
        $startOfDay = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();

        $members = User::whereNull('role');

        // Abonnements encaissés (statut actif) — chiffre d'affaires
        $paid = Subscription::where('status', 'active');

        $kpis = [
            'members_total'   => (clone $members)->count(),
            'members_today'   => (clone $members)->where('created_at', '>=', $startOfDay)->count(),
            'members_week'    => (clone $members)->where('created_at', '>=', $startOfWeek)->count(),
            'members_month'   => (clone $members)->where('created_at', '>=', $startOfMonth)->count(),
            'online_now'      => User::query()->online()->count(),
            'posts_today'     => Post::where('published_at', '>=', $startOfDay)->count(),
            'posts_total'     => Post::count(),
            'reports_pending' => Report::where('status', 'pending')->count(),
            'demandes_active' => Demande::where('status', 'active')->count(),
            'suspended'       => User::whereIn('status', ['suspended', 'banned'])->count(),
            'revenue_month'   => (int) Subscription::where('status', 'active')->where('starts_at', '>=', $startOfMonth)->sum('amount'),
            'revenue_total'   => (int) Subscription::whereIn('status', ['active', 'expired'])->sum('amount'),
            'subs_active'     => (clone $paid)->count(),
            'subs_pending'    => Subscription::where('status', 'pending')->count(),
        ];

        // Revenus encaissés par jour (30 derniers jours)
        $rev = Subscription::whereIn('status', ['active', 'expired'])
            ->where('starts_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->whereNotNull('starts_at')
            ->get(['starts_at', 'amount'])
            ->groupBy(fn ($s) => $s->starts_at->format('Y-m-d'))
            ->map(fn ($g) => (int) $g->sum('amount'));
        $revenueSeries = collect(range(29, 0))->map(function ($d) use ($now, $rev) {
            $date = $now->copy()->subDays($d)->format('Y-m-d');

            return ['date' => $date, 'amount' => $rev[$date] ?? 0];
        });

        // Répartitions
        $byGender = (clone $members)->whereHas('profile')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->selectRaw('profiles.gender, COUNT(*) as c')->groupBy('profiles.gender')->pluck('c', 'gender');

        $byRegion = (clone $members)->whereHas('profile')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->selectRaw('profiles.region, COUNT(*) as c')->whereNotNull('profiles.region')
            ->groupBy('profiles.region')->orderByDesc('c')->limit(6)->pluck('c', 'region');

        $byVerif = (clone $members)->whereHas('profile')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->selectRaw('profiles.verification_level, COUNT(*) as c')
            ->groupBy('profiles.verification_level')->pluck('c', 'verification_level');

        // Inscriptions des 14 derniers jours
        $signups = (clone $members)->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn ($u) => $u->created_at->format('Y-m-d'))
            ->map->count();
        $signupSeries = collect(range(13, 0))->map(function ($d) use ($now, $signups) {
            $date = $now->copy()->subDays($d)->format('Y-m-d');

            return ['date' => $date, 'count' => $signups[$date] ?? 0];
        });

        $recentMembers = (clone $members)->with('profile')->latest()->take(6)->get();

        return view('admin.dashboard', compact('kpis', 'byGender', 'byRegion', 'byVerif', 'signupSeries', 'revenueSeries', 'recentMembers'));
    }

    /** Page Modules & règles premium. */
    public function modules()
    {
        $modules = collect(FeatureGate::MODULES)->map(fn ($m, $key) => [
            'key'     => $key,
            'label'   => $m[0],
            'enabled' => FeatureGate::moduleEnabled($key),
        ])->values();

        $rules = collect(FeatureGate::PREMIUM_RULES)->map(fn ($r, $key) => [
            'key'   => $key,
            'label' => $r[0],
            'type'  => $r[1],
            'value' => FeatureGate::rule($key),
        ])->values();

        $monetization = FeatureGate::monetizationEnabled();

        return view('admin.modules', compact('modules', 'rules', 'monetization'));
    }

    /** Enregistre l'état des modules et les règles premium. */
    public function saveModules(Request $request)
    {
        Setting::put('premium.enforced', $request->boolean('monetization'), 'bool', 'premium');

        foreach (array_keys(FeatureGate::MODULES) as $key) {
            Setting::put('module.' . $key, $request->boolean('module_' . $key), 'bool', 'modules');
        }

        foreach (FeatureGate::PREMIUM_RULES as $key => [$label, $type, $default]) {
            $input = $request->input('rule_' . $key);
            $value = $type === 'bool' ? $request->boolean('rule_' . $key) : (int) $input;
            Setting::put('premium.' . $key, $value, $type, 'premium');
        }

        AdminLog::record($request->user()->id, 'settings_modules', null, null, 'Modules & règles premium mis à jour');

        return back()->with('status', 'Modules et règles premium enregistrés.');
    }

    /** File de modération : signalements en attente regroupés par contenu. */
    public function moderation(Request $request)
    {
        $status = $request->input('status', 'pending');

        $reports = Report::with(['reporter', 'reportable'])
            ->when(in_array($status, ['pending', 'resolved', 'dismissed'], true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending'   => Report::where('status', 'pending')->count(),
            'resolved'  => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
        ];

        return view('admin.moderation', compact('reports', 'status', 'counts'));
    }

    /** Marque un signalement comme résolu ou rejeté (sans supprimer le contenu). */
    public function resolveReport(Request $request, Report $report)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['resolved', 'dismissed'])],
        ]);

        $report->update(['status' => $data['status']]);

        return back()->with('status', 'Signalement mis à jour.');
    }

    /** Supprime le contenu signalé (publication ou commentaire) et clôt les signalements associés. */
    public function deleteContent(Report $report)
    {
        $reportable = $report->reportable;

        if ($reportable instanceof Post || $reportable instanceof Comment) {
            $type = $reportable->getMorphClass();
            $id = $reportable->id;
            $reportable->delete();

            Report::where('reportable_type', $type)->where('reportable_id', $id)
                ->update(['status' => 'resolved']);
        } else {
            $report->update(['status' => 'resolved']);
        }

        return back()->with('status', 'Contenu supprimé et signalements clôturés.');
    }
}
