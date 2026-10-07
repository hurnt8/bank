<?php

namespace Database\Seeders;

use App\Models\SiteContact;
use Illuminate\Database\Seeder;

class SiteContactSeeder extends Seeder
{
    /**
     * Identite du site (nom, email) affichee sur tout le front, les PDF et les emails.
     *
     * Sans ce seeder, la ligne site_contacts n'etait jamais initialisee volontairement :
     * la migration de creation inserait une ligne, puis add_name_and_logo lui appliquait
     * le defaut de colonne 'Solberg Grupo'. Toute installation neuve repartait donc sous
     * l'ancienne marque.
     *
     * Idempotent : n'ecrase que les valeurs vides ou les placeholders laisses par les
     * migrations. Ce qu'un administrateur a saisi est preserve, donc rejouable en prod.
     */
    private const DEFAULT_NAME = 'Santander Bank';

    /** Valeurs posees par les migrations : a considerer comme "non configure". */
    private const LEGACY_NAMES = ['Solberg Grupo'];

    private const LEGACY_EMAILS = ['contact@solberggrupo.site'];

    public function run(): void
    {
        $contact = SiteContact::first() ?? new SiteContact();

        if ($this->needsValue($contact->name, self::LEGACY_NAMES)) {
            $contact->name = self::DEFAULT_NAME;
        }

        if ($this->needsValue($contact->email, self::LEGACY_EMAILS)) {
            $fallback = config('mail.from.address');

            if ($fallback && ! in_array($fallback, self::LEGACY_EMAILS, true)) {
                $contact->email = $fallback;
            }
        }

        $contact->save();

        $this->command?->info("Identite du site : {$contact->name} <{$contact->email}>");
    }

    /**
     * Vrai si la valeur est vide ou correspond a un placeholder d'installation.
     */
    private function needsValue(?string $current, array $legacy): bool
    {
        return blank($current) || in_array($current, $legacy, true);
    }
}
