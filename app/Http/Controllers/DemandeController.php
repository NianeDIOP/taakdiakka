<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;

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
}
