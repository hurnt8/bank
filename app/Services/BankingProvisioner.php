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
 * Génère automatiquement les coordonnées bancaires (IBAN allemand + BIC) d'un client lorsque son compte
 * est approuvé. La carte, elle, est émise sur demande du client (voir issueCard()).
 * N'écrase jamais ce qu'un administrateur a déjà attribué.
 */
class BankingProvisioner
{
    /** Code banque (BLZ) à 8 chiffres par défaut, si aucun n'est configuré dans les coordonnées du site. */
    private const DEFAULT_BANK_CODE = '37040044';

    /**
     * Crée l'IBAN s'il n'existe pas encore (la carte n'est plus générée ici : le client la demande).
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

        return $created;
    }

    /** Émet la carte Visa d'un client (demande validée par un administrateur). Sans effet s'il en a déjà une. */
    public function issueCard(User $client, ?int $assignedBy = null): Card
    {
        return $client->card()->first() ?? $client->card()->create([
            'assigned_by' => $assignedBy,
            'holder_name' => $this->holderName($client),
            'last_four'   => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'network'     => 'visa',
            'expires_at'  => now()->addYears(4)->endOfMonth()->toDateString(),
            'status'      => Card::STATUS_ACTIVE,
        ]);
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

    /** IBAN allemand valide (DE + clé modulo 97 + BLZ 8 chiffres + compte 10 chiffres), unique parmi les comptes existants. */
    public function generateIban(): string
    {
        $existing = BankAccount::all()->map(fn (BankAccount $a) => (string) $a->iban)->all();

        do {
            $account = '';
            for ($i = 0; $i < 10; $i++) {
                $account .= (string) random_int(0, 9);
            }

            $bban = $this->bankCode() . $account;

            // Clé IBAN = 98 - (BBAN + "DE00" converti en chiffres) mod 97 ; D=13, E=14
            $check = 98 - $this->mod97($bban . '131400');
            $iban  = 'DE' . str_pad((string) $check, 2, '0', STR_PAD_LEFT) . $bban;
        } while (in_array($iban, $existing, true));

        return $iban;
    }

    /** Code banque des IBAN générés : celui configuré en administration, sinon la valeur par défaut. */
    public function bankCode(): string
    {
        $configured = (string) \App\Models\SiteContact::current()->iban_bank_code;

        return preg_match('/^\d{8}$/', $configured) ? $configured : self::DEFAULT_BANK_CODE;
    }

    /** BIC par défaut : celui configuré en administration, sinon dérivé du nom du site (4 lettres + DE + FF). */
    public function defaultBic(): string
    {
        $configured = strtoupper((string) \App\Models\SiteContact::current()->default_bic);
        if ($configured !== '') {
            return $configured;
        }

        $letters = strtoupper(preg_replace('/[^A-Za-z]/', '', Str::ascii(site_name())));

        return str_pad(substr($letters, 0, 4), 4, 'X') . 'DEFF';
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
