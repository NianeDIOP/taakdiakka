<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MembersController extends Controller
{
    /** Accueil : membres compatibles avec mon profil, classés par affinité, + recherche rapide. */
    public function home(Request $request)
    {
        $me = $request->user();
        $profile = $me->profileOrNew();
        $hasDemande = $me->demandes()->exists();

        $stats = [
            'matchs'    => $me->matchedUsers()->count(),
            'visitors'  => $me->profileVisitors()->count(),
            'followers' => $me->followers()->count(),
        ];

        $query = $this->baseMembersQuery($me);

        // On ne propose JAMAIS le même genre : homme → femmes, femme → hommes
        $sought = $this->genderSought($profile);
        if ($sought) {
            $query->whereHas('profile', fn ($q) => $q->where('gender', $sought));
        }

        $term = trim((string) $request->input('q'));

        // Mode recherche : résultats à plat
        if ($term) {
            $query->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                    ->orWhereHas('profile', fn ($q) => $q->where('profession', 'like', "%{$term}%")
                        ->orWhere('region', 'like', "%{$term}%"));
            });
            $results = $query->get()
                ->sortByDesc(fn ($u) => ($u->boost_active ? 1_000_000 : 0) + $this->score($profile, $u->profile))->values();

            return view('membres.home', compact('profile', 'stats', 'term', 'sought', 'hasDemande') + [
                'isSearch'   => true,
                'results'    => $results,
                'needsInfo'  => ! $sought,
                'compatible' => collect(), 'recent' => collect(), 'visitors' => collect(),
            ]);
        }

        // Mode accueil : sections (profils boostés remontés en tête)
        $all = $query->get();
        $scored = $all->sortByDesc(fn ($u) => $this->score($profile, $u->profile))->values();
        $compatible = $all->sortByDesc(fn ($u) => ($u->boost_active ? 1_000_000 : 0) + $this->score($profile, $u->profile))->take(6)->values();
        $recent = $all->sortByDesc(fn ($u) => ($u->boost_active ? 1_000_000_000 : 0) + $u->id)->take(6)->values();
        $visitors = $me->profileVisitors()->with('profile')->take(6)->get();

        // Coup de cœur du jour : un profil très compatible, stable sur la journée, qui change chaque jour.
        $pool = $scored->take(12);
        $coup = $pool->isNotEmpty() ? $pool[(int) date('z') % $pool->count()] : null;
        $coupPct = $coup ? min(99, 55 + (int) round($this->score($profile, $coup->profile) / 90 * 44)) : null;

        return view('membres.home', compact('profile', 'stats', 'term', 'sought', 'compatible', 'recent', 'visitors', 'hasDemande', 'coup', 'coupPct') + [
            'isSearch'  => false,
            'results'   => collect(),
            'needsInfo' => ! $sought,
        ]);
    }

    /** Découvrir : tous les membres + filtres avancés. */
    public function discover(Request $request)
    {
        $me = $request->user();
        $profile = $me->profileOrNew();
        $query = $this->baseMembersQuery($me);

        // On ne propose JAMAIS le même genre : un homme ne voit que des femmes (et inversement)
        $sought = $this->genderSought($profile);
        if ($sought) {
            $query->whereHas('profile', fn ($q) => $q->where('gender', $sought));
        }

        $f = [
            'q'        => trim((string) $request->input('q')),
            'region'   => $request->input('region'),
            'religion' => $request->input('religion'),
            'practice' => $request->input('practice'),
            'age_min'  => $request->input('age_min'),
            'age_max'  => $request->input('age_max'),
        ];

        if ($f['q']) {
            $query->where(function ($w) use ($f) {
                $w->where('name', 'like', "%{$f['q']}%")
                    ->orWhereHas('profile', fn ($q) => $q->where('profession', 'like', "%{$f['q']}%"));
            });
        }
        foreach (['region', 'religion', 'practice'] as $col) {
            if ($f[$col]) {
                $query->whereHas('profile', fn ($q) => $q->where($col, $f[$col]));
            }
        }
        // Âge → birthdate
        if ($f['age_min']) {
            $query->whereHas('profile', fn ($q) => $q->whereDate('birthdate', '<=', Carbon::now()->subYears((int) $f['age_min'])));
        }
        if ($f['age_max']) {
            $query->whereHas('profile', fn ($q) => $q->whereDate('birthdate', '>=', Carbon::now()->subYears((int) $f['age_max'] + 1)));
        }

        $members = $query->orderByDesc('boost_active')->orderByDesc('id')->paginate(12)->withQueryString();

        return view('membres.discover', [
            'members'   => $members,
            'options'   => Profile::OPTIONS,
            'f'         => $f,
            'sought'    => $sought,
            'needsInfo' => ! $sought,
        ]);
    }

    /** Fiche détaillée d'un membre. */
    public function show(Request $request, User $user)
    {
        // Pas de profil, ou compte admin/modérateur, ou suspendu : introuvable.
        abort_if(! $user->profile || $user->role !== null || $user->status !== 'active', 404);

        $me = $request->user();

        // Enregistre la visite (hors soi-même)
        if ($user->id !== $me->id) {
            $me->recordViewOf($user);
        }

        $user->load('profile', 'photos', 'demandes');
        $demande = $user->demandes->first();

        $rel = [
            'isInterested' => $me->isInterestedIn($user),
            'isMatch'      => $me->isMatchedWith($user),
            'isFollowing'  => $me->isFollowing($user),
            'isSelf'       => $user->id === $me->id,
            'isFavorite'   => $demande && in_array($demande->id, $me->favorites()->pluck('demandes.id')->all()),
            'friendStatus' => $me->friendStatusWith($user),
        ];

        // Suggestions : autres membres du même genre (mêmes critères de recherche que moi)
        $similaires = $this->baseMembersQuery($me)
            ->whereKeyNot($user->id)
            ->whereHas('profile', fn ($q) => $q->where('gender', $user->profile->gender))
            ->take(4)->get();

        return view('membres.show', compact('user', 'demande', 'rel', 'similaires'));
    }

    /** Liste JSON des membres en ligne (widget flottant). */
    public function online(Request $request)
    {
        $users = User::query()->online()
            ->whereKeyNot($request->user()->id)
            ->whereHas('profile')
            ->with('profile')
            ->latest('last_seen_at')
            ->take(20)->get();

        return response()->json([
            'count' => $users->count(),
            'items' => $users->map(function ($u) {
                $p = $u->profile;
                $photo = $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name);

                return [
                    'name'  => \Illuminate\Support\Str::before($u->name, ' '),
                    'photo' => asset('img/' . $photo . '.webp'),
                    'url'   => route('members.show', $u),
                ];
            })->all(),
        ]);
    }

    /* ---------------- Helpers ---------------- */

    /** Base : membres réels (avec profil + genre), hors moi-même, hors demandes en pause. */
    private function baseMembersQuery(User $me)
    {
        return User::query()
            ->whereKeyNot($me->id)
            ->whereNull('role')        // exclut les comptes admin / modérateur
            ->where('status', 'active') // exclut les comptes suspendus / bannis
            ->whereNotIn('id', $me->blockRelatedIds()) // masque les membres bloqués (des deux côtés)
            ->whereHas('profile', fn ($q) => $q->whereNotNull('gender'))
            ->whereDoesntHave('demandes', fn ($q) => $q->where('status', 'suspended'))
            // Profils boostés (mise en avant payante encore active)
            ->withCount(['boosts as boost_active' => fn ($q) => $q->where('ends_at', '>', now())])
            ->with(['profile', 'demandes:id,user_id,status', 'subscriptions.plan']);
    }

    private function genderSought(Profile $profile): ?string
    {
        return match ($profile->gender) {
            'Homme' => 'Femme',
            'Femme' => 'Homme',
            default => null,
        };
    }

    /** Score d'affinité simple (région, religion, pratique, proximité d'âge). */
    private function score(Profile $me, ?Profile $other): int
    {
        if (! $other) {
            return 0;
        }
        $s = 0;
        if ($me->region && $me->region === $other->region) {
            $s += 30;
        }
        if ($me->religion && $me->religion === $other->religion) {
            $s += 25;
        }
        if ($me->practice && $me->practice === $other->practice) {
            $s += 15;
        }
        if ($me->age && $other->age) {
            $gap = abs($me->age - $other->age);
            $s += max(0, 20 - $gap * 2);
        }
        return $s;
    }
}
