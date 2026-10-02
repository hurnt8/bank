<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = ['user_id', 'assigned_by', 'iban', 'bic', 'status'];

    protected $casts = [
        'iban' => SafeEncrypted::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function maskedIban(): string
    {
        $iban = (string) $this->iban;
        if (strlen($iban) <= 4) {
            return $iban;
        }

        $country = substr($iban, 0, 4);
        $last    = substr($iban, -4);

        return $country . ' •••• •••• ' . $last;
    }
}
