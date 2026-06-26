<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    protected $fillable = ['post_id', 'user_id', 'type'];

    /** Jeu de réactions disponibles (clé => emoji + libellé). */
    public const TYPES = [
        'like'    => ['👍', "J'aime"],
        'love'    => ['❤️', "J'adore"],
        'amine'   => ['🤲', 'Amine'],
        'support' => ['💪', 'Soutien'],
        'wow'     => ['✨', 'Bravo'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
