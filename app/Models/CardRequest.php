<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardRequest extends Model
{
    const STATUS_PENDING  = 'pending';
    /** Frais de carte facturés au client : en attente de son règlement avant l'émission. */
    const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id', 'status', 'card_type', 'holder_name', 'delivery_address', 'delivery_zip', 'delivery_city', 'delivery_country',
        'fee_amount', 'invoice_id', 'reason', 'handled_by', 'handled_at',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
        'fee_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isPhysical(): bool
    {
        return $this->card_type === 'physical';
    }

    /** Demande encore ouverte (à traiter ou en attente de paiement). */
    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_AWAITING_PAYMENT], true);
    }

    public function deliveryLine(): string
    {
        return trim($this->delivery_address . ', ' . trim($this->delivery_zip . ' ' . $this->delivery_city) . ', ' . $this->delivery_country, ', ');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
