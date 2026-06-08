<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'name', 'age', 'seeking', 'profession', 'region', 'quote',
        'tags', 'photo', 'is_discret', 'is_verified', 'verification_level', 'published_at',
    ];

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
