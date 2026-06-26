<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    public const REASONS = [
        'spam' => 'Spam ou publicité',
        'inapproprie' => 'Contenu inapproprié',
        'harcelement' => 'Harcèlement ou propos haineux',
        'faux_profil' => 'Faux profil ou identité usurpée',
        'autre' => 'Autre',
    ];

    public const STATUS_LABELS = [
        'pending' => 'En attente',
        'resolved' => 'Résolu',
        'dismissed' => 'Rejeté',
    ];

    protected $fillable = ['reporter_id', 'reportable_id', 'reportable_type', 'reason', 'status'];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
