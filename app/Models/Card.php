<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = ['user_id', 'assigned_by', 'holder_name', 'last_four', 'network', 'expires_at', 'status'];

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

    public function maskedNumber(): string
    {
        return '•••• •••• •••• ' . $this->last_four;
    }
}
