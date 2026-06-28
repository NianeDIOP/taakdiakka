<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /** Liste des membres avec recherche et filtres. */
    public function index(Request $request)
    {
        $query = User::query()->with(['profile', 'subscriptions.plan'])->whereNull('role');

        if ($q = trim((string) $request->input('q'))) {
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%"));
        }

        if ($gender = $request->input('gender')) {
            $query->whereHas('profile', fn ($p) => $p->where('gender', $gender));
        }

        if ($verif = $request->input('verif')) {
            $query->whereHas('profile', fn ($p) => $p->where('verification_level', $verif));
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($request->input('premium') === '1') {
            $query->whereHas('subscriptions', fn ($s) => $s->where('status', 'active')
                ->where(fn ($q2) => $q2->whereNull('ends_at')->orWhere('ends_at', '>', now())));
        } elseif ($request->input('premium') === '0') {
            $query->whereDoesntHave('subscriptions', fn ($s) => $s->where('status', 'active')
                ->where(fn ($q2) => $q2->whereNull('ends_at')->orWhere('ends_at', '>', now())));
        }

        if ($region = $request->input('region')) {
            $query->whereHas('profile', fn ($p) => $p->where('region', $region));
        }

        if ($ageMin = $request->input('age_min')) {
            $query->whereHas('profile', fn ($p) => $p->where('birthdate', '<=', now()->subYears((int) $ageMin)));
        }
        if ($ageMax = $request->input('age_max')) {
            $query->whereHas('profile', fn ($p) => $p->where('birthdate', '>=', now()->subYears((int) $ageMax + 1)));
        }

        if ($device = $request->input('device')) {
            $query->where('last_device', $device);
        }

        $sort = $request->input('sort', 'recent');
        match ($sort) {
            'name' => $query->orderBy('name'),
            'oldest' => $query->oldest(),
            'last_seen' => $query->orderByDesc('last_seen_at'),
            default => $query->latest(),
        };

        $users = $query->paginate(20)->withQueryString();

        $regions = DB::table('profiles')->whereNotNull('region')
            ->distinct()->pluck('region')->sort()->values();

        $counts = [
            'total'   => User::whereNull('role')->count(),
            'premium' => User::whereNull('role')->whereHas('subscriptions', fn ($s) => $s->where('status', 'active')
                ->where(fn ($q2) => $q2->whereNull('ends_at')->orWhere('ends_at', '>', now())))->count(),
            'online'  => User::whereNull('role')->online()->count(),
        ];

        return view('admin.users.index', [
            'users'   => $users,
            'filters' => $request->only(['q', 'gender', 'verif', 'status', 'sort', 'premium', 'region', 'age_min', 'age_max', 'device']),
            'regions' => $regions,
            'counts'  => $counts,
        ]);
    }

    /** Export CSV des membres. */
    public function export(Request $request)
    {
        $query = User::query()->with('profile')->whereNull('role');

        if ($gender = $request->input('gender')) {
            $query->whereHas('profile', fn ($p) => $p->where('gender', $gender));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($region = $request->input('region')) {
            $query->whereHas('profile', fn ($p) => $p->where('region', $region));
        }

        $users = $query->latest()->get();

        $csv = "Nom,Email,Genre,Region,Age,Verification,Statut,Appareil,Inscrit,Derniere activite\n";
        foreach ($users as $u) {
            $p = $u->profile;
            $age = $p && $p->birthdate ? \Illuminate\Support\Carbon::parse($p->birthdate)->age : '';
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $u->name) . '"',
                $u->email,
                $p?->gender ?? '',
                '"' . str_replace('"', '""', $p?->region ?? '') . '"',
                $age,
                $p?->verification_level ?? 'Bronze',
                $u->status ?? 'active',
                $u->last_device ?? '',
                $u->created_at->format('d/m/Y'),
                $u->last_seen_at?->format('d/m/Y H:i') ?? '',
            ]) . "\n";
        }

        AdminLog::record($request->user()->id, 'export_users', null, null, $users->count() . ' membres exportés');

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="membres-taakdiakka-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /** Fiche détaillée d'un membre. */
    public function show(User $user)
    {
        abort_if($user->isAdminUser(), 404);

        $user->load(['profile', 'photos', 'demandes']);

        $reportsAgainst = \App\Models\Report::whereHasMorph(
            'reportable',
            [\App\Models\Post::class, \App\Models\Comment::class],
            function ($q, $type) use ($user) {
                $q->where('user_id', $user->id);
            }
        )->count();

        $reportsAsUser = \App\Models\Report::where('reportable_type', User::class)
            ->where('reportable_id', $user->id)
            ->count();

        $subscriptions = \App\Models\Subscription::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->take(10)
            ->get();

        $blocksBy = \Illuminate\Support\Facades\DB::table('blocks')
            ->join('users', 'users.id', '=', 'blocks.blocked_id')
            ->where('blocks.blocker_id', $user->id)
            ->select('users.id', 'users.name', 'blocks.created_at')
            ->get();

        $blockedBy = \Illuminate\Support\Facades\DB::table('blocks')
            ->join('users', 'users.id', '=', 'blocks.blocker_id')
            ->where('blocks.blocked_id', $user->id)
            ->select('users.id', 'users.name', 'blocks.created_at')
            ->get();

        $reportsReceived = \App\Models\Report::with('reporter')
            ->where('reportable_type', User::class)
            ->where('reportable_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact(
            'user', 'reportsAgainst', 'reportsAsUser', 'subscriptions',
            'blocksBy', 'blockedBy', 'reportsReceived'
        ));
    }

    /** Suspendre un membre (avec motif et durée optionnelle). */
    public function suspend(Request $request, User $user)
    {
        abort_if($user->isAdminUser(), 403);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'days'   => ['nullable', 'integer', 'min:1', 'max:3650'],
        ]);

        $user->update([
            'status'          => 'suspended',
            'status_reason'   => $data['reason'] ?? null,
            'suspended_until' => isset($data['days']) ? now()->addDays((int) $data['days']) : null,
        ]);

        $this->log($request, 'suspend', $user, $data['reason'] ?? null);

        return back()->with('status', "Le compte de {$user->name} a été suspendu.");
    }

    /** Bannir définitivement un membre. */
    public function ban(Request $request, User $user)
    {
        abort_if($user->isAdminUser(), 403);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        $user->update([
            'status'          => 'banned',
            'status_reason'   => $data['reason'] ?? null,
            'suspended_until' => null,
        ]);

        $this->log($request, 'ban', $user, $data['reason'] ?? null);

        return back()->with('status', "Le compte de {$user->name} a été banni.");
    }

    /** Réactiver un membre. */
    public function reactivate(Request $request, User $user)
    {
        $user->update(['status' => 'active', 'status_reason' => null, 'suspended_until' => null]);

        $this->log($request, 'reactivate', $user);

        return back()->with('status', "Le compte de {$user->name} a été réactivé.");
    }

    /** Forcer le niveau de vérification. */
    public function verify(Request $request, User $user)
    {
        $data = $request->validate([
            'level' => ['required', Rule::in(array_keys(Profile::VERIF_RANK))],
        ]);

        $profile = $user->profileOrNew();
        $profile->update(['verification_level' => $data['level']]);

        $this->log($request, 'verify', $user, $data['level']);

        return back()->with('status', "Niveau de vérification de {$user->name} : {$data['level']}.");
    }

    /** Réinitialiser le mot de passe et renvoyer un mot de passe temporaire. */
    public function resetPassword(Request $request, User $user)
    {
        abort_if($user->isAdminUser(), 403);

        $temp = Str::password(12);
        $user->update(['password' => Hash::make($temp)]);

        $this->log($request, 'reset_password', $user);

        return back()->with('status', "Mot de passe réinitialisé pour {$user->name}. Mot de passe temporaire : {$temp}");
    }

    /** Supprimer définitivement le compte (RGPD). */
    public function destroy(Request $request, User $user)
    {
        abort_if($user->isAdminUser(), 403);

        $name = $user->name;
        $this->log($request, 'delete', $user, 'Compte supprimé');
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "Le compte de {$name} a été supprimé.");
    }

    private function log(Request $request, string $action, User $target, ?string $details = null): void
    {
        AdminLog::record($request->user()->id, $action, User::class, $target->id, $details);
    }
}
