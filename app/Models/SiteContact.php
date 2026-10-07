<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContact extends Model
{
    protected $fillable = [
        'name',
        'logo_light_path',
        'logo_dark_path',
        'email_signature_path',
        'address_1',
        'address_2',
        'address_3',
        'phone_1',
        'phone_2',
        'email',
        'default_bic',
        'iban_bank_code',
        'payment_iban',
        'payment_bic',
        'whatsapp_number',
        'whatsapp_enabled',
    ];

    protected $casts = [
        'whatsapp_enabled' => 'boolean',
    ];

    /**
     * Enregistrement singleton des coordonnées du site.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }

    /**
     * Lien wa.me si l'assistant WhatsApp est configuré et actif, sinon null.
     */
    public function whatsappUrl(): ?string
    {
        if (! $this->whatsapp_enabled || ! $this->whatsapp_number) {
            return null;
        }

        $digits = preg_replace('/[^\d]/', '', $this->whatsapp_number);

        return $digits ? "https://wa.me/{$digits}" : null;
    }
}
