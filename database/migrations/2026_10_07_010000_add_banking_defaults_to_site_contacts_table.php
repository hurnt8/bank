<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BIC par défaut et code banque des IBAN, configurables depuis l'administration.
     * Utilisés pour générer automatiquement les coordonnées bancaires à l'approbation d'un compte.
     */
    public function up(): void
    {
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->string('default_bic', 11)->nullable()->after('email');
            $table->string('iban_bank_code', 5)->nullable()->after('default_bic');
        });
    }

    public function down(): void
    {
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->dropColumn(['default_bic', 'iban_bank_code']);
        });
    }
};
