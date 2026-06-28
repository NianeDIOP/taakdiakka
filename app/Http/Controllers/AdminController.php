<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Boost;
use App\Models\Comment;
use App\Models\Demande;
use App\Models\Post;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Support\FeatureGate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $membersTotal = (clone $members)->count();
        $subsActive = (clone $paid)->count();

        $kpis = [
            'members_total'   => $membersTotal,
            'members_today'   => (clone $members)->where('created_at', '>=', $startOfDay)->count(),
            'members_week'    => (clone $members)->where('created_at', '>=', $startOfWeek)->count(),
            'members_month'   => (clone $members)->where('created_at', '>=', $startOfMonth)->count(),
            'online_now'      => User::query()->online()->count(),
            'posts_today'     => Post::where('published_at', '>=', $startOfDay)->count(),
            'posts_total'     => Post::count(),
            'comments_total'  => Comment::count(),
            'reports_pending' => Report::where('status', 'pending')->count(),
            'demandes_active' => Demande::where('status', 'active')->count(),
            'suspended'       => User::whereIn('status', ['suspended', 'banned'])->count(),
            'revenue_month'   => (int) Subscription::where('status', 'active')->where('starts_at', '>=', $startOfMonth)->sum('amount'),
            'revenue_total'   => (int) Subscription::whereIn('status', ['active', 'expired'])->sum('amount'),
            'subs_active'     => $subsActive,
            'subs_pending'    => Subscription::where('status', 'pending')->count(),
            'conversion'      => $membersTotal > 0 ? round($subsActive / $membersTotal * 100, 1) : 0,
            'boosts_active'   => Boost::where('ends_at', '>', $now)->count(),
            'blocks_total'    => DB::table('blocks')->count(),
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

    /* ============================================================
     *  Communauté — gestion des publications & commentaires
     * ============================================================ */

    public function community(Request $request)
    {
        $tab = $request->input('tab', 'posts');
        $q = trim((string) $request->input('q'));

        if ($tab === 'comments') {
            $items = Comment::with(['user.profile', 'post'])
                ->when($q, fn ($query) => $query->where('body', 'like', "%$q%"))
                ->latest()
                ->paginate(20)
                ->withQueryString();
        } else {
            $items = Post::with('author.profile')
                ->when($q, fn ($query) => $query->where('body', 'like', "%$q%"))
                ->when($request->input('theme'), fn ($query, $theme) => $query->where('theme', $theme))
                ->latest('published_at')
                ->paginate(20)
                ->withQueryString();
        }

        $stats = [
            'posts'    => Post::count(),
            'comments' => Comment::count(),
            'today'    => Post::where('published_at', '>=', now()->startOfDay())->count(),
        ];

        return view('admin.community', compact('items', 'tab', 'stats', 'q'));
    }

    public function deletePost(Request $request, Post $post)
    {
        $body = \Illuminate\Support\Str::limit($post->body, 60);
        $post->delete();

        AdminLog::record($request->user()->id, 'post_delete', Post::class, $post->id, $body);

        return back()->with('status', 'Publication supprimée.');
    }

    public function deleteComment(Request $request, Comment $comment)
    {
        $body = \Illuminate\Support\Str::limit($comment->body, 60);
        $comment->delete();

        AdminLog::record($request->user()->id, 'comment_delete', Comment::class, $comment->id, $body);

        return back()->with('status', 'Commentaire supprimé.');
    }

    /* ============================================================
     *  Blocages entre membres
     * ============================================================ */

    public function blocks(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $blocks = DB::table('blocks')
            ->join('users as blocker', 'blocker.id', '=', 'blocks.blocker_id')
            ->join('users as blocked', 'blocked.id', '=', 'blocks.blocked_id')
            ->select('blocks.*', 'blocker.name as blocker_name', 'blocker.email as blocker_email', 'blocked.name as blocked_name', 'blocked.email as blocked_email')
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('blocker.name', 'like', "%$q%")->orWhere('blocked.name', 'like', "%$q%")))
            ->orderByDesc('blocks.created_at')
            ->paginate(20)
            ->withQueryString();

        $total = DB::table('blocks')->count();

        return view('admin.blocks', compact('blocks', 'total', 'q'));
    }

    /* ============================================================
     *  Audience & statistiques de connexion
     * ============================================================ */

    public function analytics()
    {
        $now = now();
        $members = User::whereNull('role');

        $online = User::query()->online()->get(['id', 'name', 'last_seen_at', 'last_device', 'last_browser', 'last_ip']);

        $deviceCounts = (clone $members)->whereNotNull('last_device')
            ->selectRaw('last_device, COUNT(*) as c')
            ->groupBy('last_device')
            ->pluck('c', 'last_device');

        $browserCounts = (clone $members)->whereNotNull('last_browser')
            ->selectRaw('last_browser, COUNT(*) as c')
            ->groupBy('last_browser')
            ->orderByDesc('c')
            ->pluck('c', 'last_browser');

        $activeToday = (clone $members)->where('last_seen_at', '>=', $now->copy()->startOfDay())->count();
        $activeWeek = (clone $members)->where('last_seen_at', '>=', $now->copy()->subDays(7))->count();
        $activeMonth = (clone $members)->where('last_seen_at', '>=', $now->copy()->subDays(30))->count();
        $neverSeen = (clone $members)->whereNull('last_seen_at')->count();

        $activitySeries = collect(range(13, 0))->map(function ($d) use ($now) {
            $date = $now->copy()->subDays($d);
            $count = User::whereNull('role')
                ->whereBetween('last_seen_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
                ->count();
            return ['date' => $date->format('Y-m-d'), 'count' => $count];
        });

        $stats = [
            'online'       => $online->count(),
            'active_today' => $activeToday,
            'active_week'  => $activeWeek,
            'active_month' => $activeMonth,
            'never_seen'   => $neverSeen,
            'total'        => (clone $members)->count(),
        ];

        return view('admin.analytics', compact('online', 'deviceCounts', 'browserCounts', 'stats', 'activitySeries'));
    }

    public function removeBlock(Request $request, int $blocker, int $blocked)
    {
        DB::table('blocks')->where('blocker_id', $blocker)->where('blocked_id', $blocked)->delete();

        $blockerUser = User::find($blocker);
        $blockedUser = User::find($blocked);
        AdminLog::record($request->user()->id, 'block_remove', null, null,
            ($blockerUser->name ?? '#'.$blocker) . ' → ' . ($blockedUser->name ?? '#'.$blocked));

        return back()->with('status', 'Blocage supprimé.');
    }
}
