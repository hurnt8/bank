<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Informations de la banque (nom, adresse, téléphone, e-mail) : le pied de page, les e-mails et les PDF les lisent dans
     * site_contacts. Appliquées à chaque installation (local, serveur) tant que l'administrateur n'a pas saisi autre chose :
     * seules les anciennes valeurs par défaut sont remplacées.
     */
    public function up(): void
    {
        $row = DB::table('site_contacts')->first();

        $legacyNames   = ['Solberg Grupo', 'Mellenthin Financial'];
        $legacyAddress = '24 Rue de la Bourse, 75002 Paris, France';

        if (! $row) {
            DB::table('site_contacts')->insert($this->values() + ['created_at' => now(), 'updated_at' => now()]);

            return;
        }

        if (in_array($row->name, $legacyNames, true) || $row->address_1 === $legacyAddress || blank($row->address_1)) {
            DB::table('site_contacts')->where('id', $row->id)->update($this->values() + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Données de configuration : pas de retour en arrière.
    }

    private function values(): array
    {
        return [
            'name'      => 'Santander Bank',
            'address_1' => 'First Citiz GmbH, Französische Straße 56, 10117 Berlin',
            'address_2' => null,
            'address_3' => null,
            'phone_1'   => '+49 1521 6646811',
            'phone_2'   => null,
            'email'     => 'info@mysatander.com',
        ];
    }
};
