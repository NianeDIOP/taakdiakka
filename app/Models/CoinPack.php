<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinPack extends Model
{
    protected $fillable = ['name', 'coins', 'bonus_coins', 'price', 'is_popular', 'sort_order', 'is_active'];

    protected $casts = ['is_popular' => 'boolean', 'is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function getTotalCoinsAttribute(): int
    {
        return $this->coins + $this->bonus_coins;
    }

    public function getUnitPriceAttribute(): float
    {
        return $this->total_coins > 0 ? round($this->price / $this->total_coins, 1) : 0;
    }
}
