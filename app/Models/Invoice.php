<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'reference', 'admin_id', 'client_id', 'transfer_id', 'currency',
        'subtotal', 'tax_rate', 'tax_amount', 'total',
        'status', 'issue_date', 'due_date',
        'description', 'note', 'items',
        'payment_iban', 'payment_bic', 'payment_holder', 'payment_type',
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

    /**
     * Ligne « Frais de traitement (Virement émis TRF-… — Nom) » rédigée dans la langue demandée, pour une facture de frais
     * créée depuis un virement ; null pour les autres factures.
     */
    public function feeLine(?string $locale = null): ?string
    {
        $t    = $this->linkedTransfer;
        $item = ($this->items ?? [])[0] ?? [];
        if (($item['kind'] ?? null) === 'card_fee') {
            $cr = $this->cardRequest;

            return $cr ? __('cards.fee_label', [], $locale) . ' (' . __('cards.type_' . $cr->card_type, [], $locale) . ' — ' . $cr->holder_name . ')' : null;
        }
        if (! $t || ($item['kind'] ?? null) !== 'transfer_fee') {
            return null;
        }

        $base = trim((string) ($item['text'] ?? '')) ?: __('invoice.fee_label', [], $locale);

        return $base . ' (' . $t->typeLabel($locale) . ' ' . $t->reference . ($t->beneficiary_name ? ' — ' . $t->beneficiary_name : '') . ')';
    }

    /** Libellé d'une ligne : localisé pour une facture de frais de virement, tel que saisi sinon. */
    public function itemDescription(array $item, ?string $locale = null): string
    {
        if (in_array($item['kind'] ?? null, ['transfer_fee', 'card_fee'], true)) {
            return $this->feeLine($locale) ?? (string) ($item['description'] ?? '—');
        }

        return (string) ($item['name'] ?? $item['description'] ?? '—');
    }

    public function displayDescription(?string $locale = null): ?string
    {
        return $this->feeLine($locale) ?? $this->description;
    }

    /** Note : la note automatique « facture liée au virement » est rédigée dans la langue demandée. */
    public function displayNote(?string $locale = null): ?string
    {
        $t = $this->linkedTransfer;
        if ($t && $this->note && str_starts_with($this->note, 'Facture liée au virement')) {
            return __('invoice.linked_note', ['reference' => $t->reference, 'type' => $t->typeLabel($locale), 'name' => (string) $t->beneficiary_name], $locale);
        }

        return $this->note;
    }

    /** Demande de carte dont cette facture règle les frais. */
    public function cardRequest()
    {
        return $this->hasOne(CardRequest::class, 'invoice_id');
    }

    /** Virement concerné par cette facture (frais de traitement). */
    public function linkedTransfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
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

    /** Bénéficiaire (titulaire) de l'IBAN de règlement : saisi sur la facture, sinon réglage du site, sinon nom du site. */
    public function paymentHolder(): string
    {
        return $this->payment_holder ?: (SiteContact::current()->payment_holder ?: site_name());
    }

    /** Type de virement à exécuter pour régler la facture : sepa ou international. */
    public function paymentType(): string
    {
        $t = $this->payment_type ?: SiteContact::current()->payment_type;

        return in_array($t, ['sepa', 'international'], true) ? $t : 'sepa';
    }

    /** « Virement SEPA » / « Virement international » dans la langue demandée. */
    public function paymentTypeLabel(?string $locale = null): string
    {
        return __('movement.kind_' . $this->paymentType(), [], $locale);
    }

    /** Logo du site en data-URI (utilisable par le PDF) : logo téléversé, sinon logo par défaut ; null si rien d'exploitable. */
    public static function logoDataUri(): ?string
    {
        $contact = SiteContact::current();
        $disk    = \Illuminate\Support\Facades\Storage::disk('public');

        foreach (array_filter([$contact->logo_light_path, $contact->logo_dark_path]) as $rel) {
            if ($disk->exists($rel) && ! str_ends_with(strtolower($rel), '.svg')) {
                $mime = $disk->mimeType($rel) ?: 'image/png';

                return 'data:' . $mime . ';base64,' . base64_encode($disk->get($rel));
            }
        }

        foreach (['images/logo-transparent.png', 'images/logo.png'] as $default) {
            if (is_file(public_path($default))) {
                return 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($default)));
            }
        }

        return null;
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
