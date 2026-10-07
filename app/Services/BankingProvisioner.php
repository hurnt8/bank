<?php

namespace App\Services;

use App\Mail\BankingAssignedMail;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\ClientNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Génère automatiquement les coordonnées bancaires (IBAN + BIC) et la carte d'un client
 * lorsque son compte est approuvé. N'écrase jamais ce qu'un administrateur a déjà attribué.
 */
class BankingProvisioner
{
    /** Code banque à 5 chiffres par défaut, si aucun n'est configuré dans les coordonnées du site. */
    private const DEFAULT_BANK_CODE = '30002';

    /**
     * Crée l'IBAN et/ou la carte s'ils n'existent pas encore.
     *
     * @return array{bank: bool, card: bool} ce qui a été créé
     */
    public function provision(User $client, ?int $assignedBy = null): array
    {
        $created = ['bank' => false, 'card' => false];

        if (! $client->bankAccount()->exists()) {
            $client->bankAccount()->create([
                'assigned_by' => $assignedBy,
                'iban'        => $this->generateIban(),
                'bic'         => $this->defaultBic(),
                'status'      => BankAccount::STATUS_ACTIVE,
            ]);
            $created['bank'] = true;
        }

        if (! $client->card()->exists()) {
            $client->card()->create([
                'assigned_by' => $assignedBy,
                'holder_name' => $this->holderName($client),
                'last_four'   => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'network'     => 'visa',
                'expires_at'  => now()->addYears(4)->endOfMonth()->toDateString(),
                'status'      => Card::STATUS_ACTIVE,
            ]);
            $created['card'] = true;
        }

        return $created;
    }

    /** Notification (cloche + e-mail) « vos coordonnées bancaires sont disponibles ». */
    public function notifyAssigned(User $client): void
    {
        ClientNotification::notifyUser($client, 'system', 'banking.notif_assigned', 'banking.notif_assigned_body', [], []);

        try {
            Mail::to($client->email)->locale($client->locale ?? 'fr')->send(new BankingAssignedMail($client));
        } catch (\Throwable $e) {
            Log::error('BankingAssignedMail failed: ' . $e->getMessage());
        }
    }

    /** IBAN français valide (clé RIB et clé IBAN calculées, modulo 97), unique parmi les comptes existants. */
    public function generateIban(): string
    {
        $existing = BankAccount::all()->map(fn (BankAccount $a) => (string) $a->iban)->all();

        do {
            $branch  = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $account = '';
            for ($i = 0; $i < 11; $i++) {
                $account .= (string) random_int(0, 9);
            }

            // Clé RIB = 97 - ((89*banque + 15*guichet + 3*compte) mod 97)
            $bankCode = $this->bankCode();
            $ribKey = 97 - ((89 * (int) $bankCode + 15 * (int) $branch + 3 * (int) $account) % 97);
            $bban   = $bankCode . $branch . $account . str_pad((string) $ribKey, 2, '0', STR_PAD_LEFT);

            // Clé IBAN = 98 - (BBAN + "FR00" converti en chiffres) mod 97 ; F=15, R=27
            $check = 98 - $this->mod97($bban . '152700');
            $iban  = 'FR' . str_pad((string) $check, 2, '0', STR_PAD_LEFT) . $bban;
        } while (in_array($iban, $existing, true));

        return $iban;
    }

    /** Code banque des IBAN générés : celui configuré en administration, sinon la valeur par défaut. */
    public function bankCode(): string
    {
        $configured = (string) \App\Models\SiteContact::current()->iban_bank_code;

        return preg_match('/^\d{5}$/', $configured) ? $configured : self::DEFAULT_BANK_CODE;
    }

    /** BIC par défaut : celui configuré en administration, sinon dérivé du nom du site (4 lettres + FR + PP). */
    public function defaultBic(): string
    {
        $configured = strtoupper((string) \App\Models\SiteContact::current()->default_bic);
        if ($configured !== '') {
            return $configured;
        }

        $letters = strtoupper(preg_replace('/[^A-Za-z]/', '', Str::ascii(site_name())));

        return str_pad(substr($letters, 0, 4), 4, 'X') . 'FRPP';
    }

    private function holderName(User $client): string
    {
        return mb_substr(strtoupper(Str::ascii($client->name)), 0, 26);
    }

    /** Modulo 97 d'un grand nombre décimal, par tranches (sans dépendre de bcmath). */
    private function mod97(string $digits): int
    {
        $remainder = 0;
        foreach (str_split($digits, 7) as $chunk) {
            $remainder = (int) ($remainder . $chunk) % 97;
        }

        return $remainder;
    }
}
