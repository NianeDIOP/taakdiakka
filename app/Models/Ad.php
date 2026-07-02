<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['image', 'contact', 'cta_type', 'cta_label', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function ctaHref(): string
    {
        $phone = preg_replace('/\D/', '', $this->contact);
        return $this->cta_type === 'call' ? "tel:+{$phone}" : "https://wa.me/{$phone}";
    }
}
