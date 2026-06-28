<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'target_type', 'target_id', 'details', 'ip'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function record(int $adminId, string $action, ?string $targetType = null, ?int $targetId = null, ?string $details = null): self
    {
        return self::create([
            'admin_id'    => $adminId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'details'     => $details,
            'ip'          => request()->ip(),
        ]);
    }
}
