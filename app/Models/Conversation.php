<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /** L'autre participant (vu depuis $user). */
    public function other(User $user): ?User
    {
        return $this->users->firstWhere('id', '!=', $user->id);
    }

    /** Trouve (ou crée) la conversation entre deux utilisateurs. */
    public static function findOrCreateBetween(int $a, int $b): self
    {
        $conv = self::whereHas('users', fn ($q) => $q->where('users.id', $a))
            ->whereHas('users', fn ($q) => $q->where('users.id', $b))
            ->first();

        if (! $conv) {
            $conv = self::create();
            $conv->users()->attach([$a, $b]);
        }

        return $conv;
    }
}
