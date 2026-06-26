<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $fillable = [
        'couple', 'initials', 'location', 'badge_label', 'badge_icon', 'badge_heart', 'quote',
    ];

    protected $casts = [
        'badge_heart' => 'boolean',
    ];
}
