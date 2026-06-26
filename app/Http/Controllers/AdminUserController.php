<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /** Liste des membres avec recherche et filtres. */
    public function index(Request $request)
    {
        $query = User::query()->with('profile')->whereNull('role');

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

        $sort = $request->input('sort', 'recent');
        match ($sort) {
            'name' => $query->orderBy('name'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users'   => $users,
            'filters' => $request->only(['q', 'gender', 'verif', 'status', 'sort']),
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

        return view('admin.users.show', compact('user', 'reportsAgainst'));
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
