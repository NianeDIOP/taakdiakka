<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DemandeController extends Controller
{
    /** Liste complète, filtrable et paginée. */
    public function index(Request $request)
    {
        $query = Demande::query();

        // Recherche plein-texte (nom, profession, citation)
        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('profession', 'like', "%{$q}%")
                  ->orWhere('quote', 'like', "%{$q}%");
            });
        }

        // Je cherche (une épouse / un époux)
        if ($seeking = $request->input('seeking')) {
            $query->where('seeking', $seeking);
        }

        // Tranche d'âge
        if ($age = $request->input('age')) {
            if (str_ends_with($age, '+')) {
                $query->where('age', '>=', (int) $age);
            } elseif (str_contains($age, '-')) {
                [$min, $max] = array_map('intval', explode('-', $age));
                $query->whereBetween('age', [$min, $max]);
            }
        }

        // Région (le champ region contient « Ville, Pays »)
        if ($region = $request->input('region')) {
            $query->where('region', 'like', "%{$region}%");
        }

        // Pratique religieuse (stockée dans les tags)
        if ($pratique = $request->input('pratique')) {
            $query->where('tags', 'like', "%{$pratique}%");
        }

        $demandes = $query->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('demandes.index', compact('demandes'));
    }

    /** Fiche détaillée d'une demande + suggestions similaires. */
    public function show(Demande $demande)
    {
        // Enregistre la visite (qui a vu mon profil), hors propriétaire
        if (Auth::check() && $demande->user_id && $demande->user_id !== Auth::id()) {
            Auth::user()->recordViewOf($demande->user);
        }

        $similaires = Demande::where('id', '!=', $demande->id)
            ->where('region', 'like', '%' . explode(',', $demande->region)[0] . '%')
            ->latest('published_at')
            ->take(3)
            ->get();

        // Compléter avec d'autres si la même région n'en fournit pas assez
        if ($similaires->count() < 3) {
            $similaires = Demande::where('id', '!=', $demande->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return view('demandes.show', compact('demande', 'similaires'));
    }

    /** « Ma demande » : gestion de l'état de la demande du membre connecté. */
    public function mine()
    {
        $user = Auth::user();
        $profile = $user->profileOrNew();
        $demande = $user->demandes()->first();

        return view('espace.ma-demande', [
            'demande'     => $demande,
            'profile'     => $profile,
            'completion'  => $profile->completion,
            'profileOk'   => (bool) $profile->gender,
            'contactsCount' => $user->conversations()->count(),
        ]);
    }

    /** Active (publie) la demande à partir du profil — aucune saisie en double. */
    public function publish()
    {
        $user = Auth::user();

        if (! $user->profileOrNew()->gender) {
            return redirect()->route('profile.edit')
                ->with('status', 'Renseignez d\'abord votre profil (au minimum votre genre) pour activer votre demande.');
        }

        Demande::activateFor($user);

        return redirect()->route('demandes.mine')
            ->with('status', 'Votre demande de mariage est active. Qu\'Allah facilite votre union 🤲');
    }

    /** Change l'état de la demande : active | suspended | engaged. */
    public function status(Request $request)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended', 'engaged'])],
        ]);

        $demande = Auth::user()->demandes()->first();
        abort_unless($demande, 404);

        $demande->update(['status' => $data['status']]);

        $msg = match ($data['status']) {
            'suspended' => 'Votre demande est mise en pause — elle n\'apparaît plus dans les suggestions.',
            'engaged'   => 'Vous êtes marqué(e) « en conversation sérieuse ». Vos contacts en sont informés. 🤲',
            default     => 'Votre demande est de nouveau active. ✨',
        };

        return redirect()->route('demandes.mine')->with('status', $msg);
    }

    /** Annulation (suppression) de sa demande. */
    public function destroy(Demande $demande)
    {
        $this->authorizeOwner($demande);
        $demande->delete();

        return redirect()->route('demandes.mine')
            ->with('status', 'Votre demande a été annulée.');
    }

    private function authorizeOwner(Demande $demande): void
    {
        abort_unless($demande->user_id === Auth::id(), 403);
    }
}
