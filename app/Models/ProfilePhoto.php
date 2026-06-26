<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePhoto extends Model
{
    protected $fillable = ['user_id', 'path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Nom de fichier sans extension (pour <picture> webp/jpg). */
    public function getBaseAttribute(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }
}
