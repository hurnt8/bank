<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_BLOCKED = 'blocked';
    /** Suspendue par le client lui-même (réversible par lui) ; « bloquée » est décidée par l'administration. */
    const STATUS_SUSPENDED = 'suspended';

    const LIMIT_MIN = 1000;
    const LIMIT_MAX = 5000;
    const LIMIT_STEP = 100;

    protected $fillable = ['user_id', 'assigned_by', 'holder_name', 'last_four', 'network', 'expires_at', 'status', 'spending_limit'];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isSuspended(): bool { return $this->status === self::STATUS_SUSPENDED; }

    public function maskedNumber(): string
    {
        return '•••• •••• •••• ' . $this->last_four;
    }
}
