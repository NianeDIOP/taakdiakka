<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boost extends Model
{
    protected $fillable = [
        'user_id', 'boost_pack_id', 'starts_at', 'ends_at', 'amount', 'payment_reference',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pack()
    {
        return $this->belongsTo(BoostPack::class, 'boost_pack_id');
    }

    public function scopeActive($query)
    {
        return $query->where('ends_at', '>', now());
    }
}
