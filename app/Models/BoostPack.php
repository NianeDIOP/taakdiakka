<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoostPack extends Model
{
    protected $fillable = ['slug', 'name', 'price', 'duration_days', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPriceLabelAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }
}
