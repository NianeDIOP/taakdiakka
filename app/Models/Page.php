<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'body'];

    /** Pages légales gérées (slug => titre par défaut). Ordre = ordre d'affichage admin. */
    public const LEGAL = [
        'conditions'      => "Conditions d'utilisation",
        'confidentialite' => 'Politique de confidentialité (RGPD)',
        'mentions-legales' => 'Mentions légales',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
