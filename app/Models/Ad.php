<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'image', 'client_name', 'price', 'duration_days',
        'starts_at', 'expires_at', 'notes',
        'contact', 'cta_type', 'cta_label',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'starts_at'  => 'datetime',
            'expires_at' => 'datetime',
            'price'      => 'integer',
        ];
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderBy('sort_order');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function ctaHref(): string
    {
        $phone = preg_replace('/\D/', '', $this->contact);
        return $this->cta_type === 'call' ? "tel:+{$phone}" : "https://wa.me/{$phone}";
    }
}
