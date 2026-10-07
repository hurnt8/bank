<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'reference', 'admin_id', 'client_id', 'currency',
        'subtotal', 'tax_rate', 'tax_amount', 'total',
        'status', 'issue_date', 'due_date',
        'description', 'note', 'items',
        'payment_iban', 'payment_bic', 'payment_holder',
        'sent_at', 'paid_at',
    ];

    protected $casts = [
        'items'      => 'array',
        'issue_date' => 'date',
        'due_date'   => 'date',
        'sent_at'    => 'datetime',
        'paid_at'    => 'datetime',
        'subtotal'   => 'float',
        'tax_rate'   => 'float',
        'tax_amount' => 'float',
        'total'      => 'float',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_SENT      = 'sent';
    const STATUS_PAID      = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'draft'     => 'Brouillon',
            'sent'      => 'Envoyée',
            'paid'      => 'Payée',
            'cancelled' => 'Annulée',
            default     => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'draft'     => 'gray',
            'sent'      => 'blue',
            'paid'      => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }

    public function isDraft(): bool     { return $this->status === self::STATUS_DRAFT; }
    public function isSent(): bool      { return $this->status === self::STATUS_SENT; }
    public function isPaid(): bool      { return $this->status === self::STATUS_PAID; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }

    /** IBAN de règlement : celui de la facture, sinon l'IBAN par défaut du site. */
    public function paymentIban(): string
    {
        return strtoupper(str_replace(' ', '', (string) ($this->payment_iban ?: SiteContact::current()->payment_iban)));
    }

    public function paymentBic(): string
    {
        return strtoupper((string) ($this->payment_bic ?: SiteContact::current()->payment_bic));
    }

    public function paymentHolder(): string
    {
        return $this->payment_holder ?: site_name();
    }

    /** IBAN affiché par groupes de 4 caractères. */
    public static function formatIban(string $iban): string
    {
        return trim(chunk_split(strtoupper(str_replace(' ', '', $iban)), 4, ' '));
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'INV-' . strtoupper(Str::random(3)) . '-' . now()->format('Ymd');
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }
}
