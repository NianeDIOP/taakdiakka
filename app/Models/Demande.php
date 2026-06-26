<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'user_id', 'name', 'age', 'seeking', 'profession', 'region', 'quote',
        'tags', 'photo', 'is_discret', 'is_verified', 'verification_level', 'status', 'published_at',
    ];

    /** Libellés des états d'une demande. */
    public const STATUS_LABELS = [
        'active'    => 'En recherche active',
        'suspended' => 'En pause',
        'engaged'   => 'En conversation sérieuse',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Crée (ou synchronise) la demande d'un membre à partir de son profil. */
    public static function activateFor(User $user): ?self
    {
        $p = $user->profile;
        if (! $p || ! $p->gender) {
            return null; // profil insuffisant : pas de genre
        }

        $core = [
            'name'               => \Illuminate\Support\Str::before($user->name, ' '),
            'age'                => $p->age,
            'seeking'            => $p->seeking,
            'profession'         => $p->profession,
            'region'             => $p->region === 'Diaspora' || ! $p->region ? ($p->region ?: 'Dakar') : $p->region . ', Sénégal',
            'quote'              => $p->bio ?: 'Profil en quête d\'une union sincère et bénie.',
            'photo'              => $p->photo,
            'verification_level' => $p->verification_level ?: 'Bronze',
        ];

        $demande = $user->demandes()->first();

        if ($demande) {
            $demande->update($core);

            return $demande;
        }

        return $user->demandes()->create($core + [
            'tags'               => [],
            'is_discret'         => false,
            'is_verified'        => true,
            'verification_level' => 'Bronze',
            'status'             => 'active',
            'published_at'       => now(),
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'En recherche active';
    }

    protected $casts = [
        'tags' => 'array',
        'is_discret' => 'boolean',
        'is_verified' => 'boolean',
        'published_at' => 'datetime',
    ];

    /** Titre affiché : « Awa, 28 ans » ou « Membre, 34 ans » si discret. */
    public function getDisplayNameAttribute(): string
    {
        $who = $this->is_discret || ! $this->name ? 'Membre' : $this->name;
        return "{$who}, {$this->age} ans";
    }

    /** Initiale pour le monogramme de repli (data-av). */
    public function getInitialAttribute(): string
    {
        return $this->name ? mb_strtoupper(mb_substr($this->name, 0, 1)) : '';
    }

    /** Date relative en français : « il y a 2 jours ». */
    public function getPostedAttribute(): string
    {
        return optional($this->published_at)->locale('fr')->diffForHumans() ?? '';
    }
}
