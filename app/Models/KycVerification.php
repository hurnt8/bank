<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KycVerification extends Model
{
    const STATUS_NON_SOUMIS = 'non_soumis';
    const STATUS_EN_ATTENTE = 'en_attente';
    const STATUS_APPROUVE   = 'approuve';
    const STATUS_REJETE     = 'rejete';

    protected $fillable = [
        'user_id', 'status', 'id_document_type',
        'id_document_front_path', 'id_document_back_path', 'selfie_path',
        'submitted_at', 'reviewed_at', 'reviewed_by', 'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(KycReview::class);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROUVE;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_NON_SOUMIS => 'Non soumis',
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_APPROUVE   => 'Approuvé',
            self::STATUS_REJETE     => 'Rejeté',
            default                 => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_NON_SOUMIS => 'gray',
            self::STATUS_EN_ATTENTE => 'orange',
            self::STATUS_APPROUVE   => 'green',
            self::STATUS_REJETE     => 'red',
            default                 => 'gray',
        };
    }
}
