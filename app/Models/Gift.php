<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = ['name', 'emoji', 'coins_cost', 'category', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public const CATALOG = [
        ['name' => 'Rose',        'emoji' => '🌹', 'coins_cost' => 5,   'category' => 'classique'],
        ['name' => 'Bouquet',     'emoji' => '💐', 'coins_cost' => 15,  'category' => 'classique'],
        ['name' => 'Chocolat',    'emoji' => '🍫', 'coins_cost' => 10,  'category' => 'classique'],
        ['name' => 'Étoile',      'emoji' => '⭐', 'coins_cost' => 8,   'category' => 'classique'],
        ['name' => 'Croissant',   'emoji' => '🌙', 'coins_cost' => 12,  'category' => 'spirituel'],
        ['name' => 'Prière',      'emoji' => '🤲', 'coins_cost' => 10,  'category' => 'spirituel'],
        ['name' => 'Bague',       'emoji' => '💍', 'coins_cost' => 50,  'category' => 'premium'],
        ['name' => 'Cœur d\'or',  'emoji' => '💛', 'coins_cost' => 30,  'category' => 'premium'],
        ['name' => 'Couronne',    'emoji' => '👑', 'coins_cost' => 80,  'category' => 'premium'],
        ['name' => 'Diamant',     'emoji' => '💎', 'coins_cost' => 100, 'category' => 'exclusif'],
    ];
}
