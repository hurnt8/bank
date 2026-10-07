<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // IBAN (et BIC) sur lesquels le client règle la facture
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_iban', 34)->nullable()->after('note');
            $table->string('payment_bic', 11)->nullable()->after('payment_iban');
            $table->string('payment_holder', 100)->nullable()->after('payment_bic');
        });

        // IBAN de règlement par défaut, pré-rempli dans les factures
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->string('payment_iban', 34)->nullable()->after('iban_bank_code');
            $table->string('payment_bic', 11)->nullable()->after('payment_iban');
        });

        // Plafond de dépenses de la carte, choisi par le client (1 000 à 5 000)
        Schema::table('cards', function (Blueprint $table) {
            $table->unsignedInteger('spending_limit')->default(1000)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn(['payment_iban', 'payment_bic', 'payment_holder']));
        Schema::table('site_contacts', fn (Blueprint $t) => $t->dropColumn(['payment_iban', 'payment_bic']));
        Schema::table('cards', fn (Blueprint $t) => $t->dropColumn('spending_limit'));
    }
};
