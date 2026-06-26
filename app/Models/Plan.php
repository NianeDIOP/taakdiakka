<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'tagline', 'price', 'compare_at_price',
        'duration_days', 'features', 'is_premium', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'   => 'array',
        'is_premium' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->price <= 0;
    }

    /** Prix formaté en FCFA. */
    public function getPriceLabelAttribute(): string
    {
        return $this->is_free ? 'Gratuit' : number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    public function getCompareLabelAttribute(): ?string
    {
        return $this->compare_at_price
            ? number_format($this->compare_at_price, 0, ',', ' ') . ' FCFA'
            : null;
    }

    /** Libellé de période (/ mois, / an…). */
    public function getPeriodLabelAttribute(): string
    {
        return match (true) {
            $this->duration_days === null => '',
            $this->duration_days >= 360   => '/ an',
            $this->duration_days >= 28    => '/ mois',
            default                       => '/ ' . $this->duration_days . ' j',
        };
    }
}
