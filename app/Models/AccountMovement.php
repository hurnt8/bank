<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountMovement extends Model
{
    /** Types d'opération proposés à l'administrateur selon le sens (crédit / débit). */
    public const KINDS = [
        'credit' => ['sepa', 'instant', 'deposit', 'refund', 'adjustment'],
        'debit'  => ['sepa', 'instant', 'card', 'withdrawal', 'fee', 'adjustment'],
    ];

    /** Types qui concernent un virement (informations de la contrepartie : IBAN). */
    public const TRANSFER_KINDS = ['sepa', 'instant'];

    protected $fillable = [
        'user_id', 'admin_id', 'type', 'kind', 'counterparty', 'counterparty_iban', 'reference', 'card_id', 'amount', 'currency',
        'balance_before', 'balance_after', 'note',
    ];

    protected $casts = [
        'amount'         => 'float',
        'balance_before' => 'float',
        'balance_after'  => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** Libellé du type d'opération dans la langue courante (ou libellé générique si aucun type). */
    public function kindLabel(?string $locale = null): string
    {
        if ($this->kind) {
            return __('movement.kind_' . $this->kind, [], $locale);
        }

        return __($this->type === 'credit' ? 'app.mv_credit_label' : 'app.mv_debit_label', [], $locale);
    }

    /** Libellé affiché dans les listes du client : type + commerçant pour un paiement par carte. */
    public function displayLabel(): string
    {
        $label = $this->kindLabel();

        return ($this->kind === 'card' && $this->counterparty) ? $label . ' — ' . $this->counterparty : $label;
    }

    /** Détail secondaire : contrepartie, référence, motif. */
    public function displaySub(): string
    {
        $parts = array_filter([
            $this->kind === 'card' ? null : $this->counterparty,
            $this->reference,
            $this->note,
        ]);

        return $parts ? implode(' · ', $parts) : ($this->admin?->name ?? '');
    }
}
