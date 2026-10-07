<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Type d'opération et informations saisies par l'administrateur lors d'un crédit / débit. */
    public function up(): void
    {
        Schema::table('account_movements', function (Blueprint $table) {
            $table->string('kind', 30)->nullable()->after('type');            // sepa, international, deposit, card_payment…
            $table->string('counterparty', 120)->nullable()->after('kind');   // expéditeur / bénéficiaire / commerçant
            $table->string('counterparty_iban', 34)->nullable()->after('counterparty');
            $table->string('reference', 60)->nullable()->after('counterparty_iban');
            $table->unsignedBigInteger('card_id')->nullable()->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('account_movements', function (Blueprint $table) {
            $table->dropColumn(['kind', 'counterparty', 'counterparty_iban', 'reference', 'card_id']);
        });
    }
};
