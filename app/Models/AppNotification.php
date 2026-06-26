<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'actor_id', 'type', 'body', 'url', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /** Crée une notification (ignore l'auto-notification). */
    public static function record(int $userId, ?int $actorId, string $type, string $body, ?string $url = null): void
    {
        if ($actorId && $actorId === $userId) {
            return;
        }

        static::create([
            'user_id'  => $userId,
            'actor_id' => $actorId,
            'type'     => $type,
            'body'     => $body,
            'url'      => $url,
        ]);
    }
}
