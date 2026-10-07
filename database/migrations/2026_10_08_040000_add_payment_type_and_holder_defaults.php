<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Type de virement à exécuter pour régler une facture (sepa | international) et bénéficiaire par défaut de l'IBAN de règlement. */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_type', 20)->nullable()->after('payment_bic');
        });

        Schema::table('site_contacts', function (Blueprint $table) {
            $table->string('payment_holder', 100)->nullable()->after('payment_bic');
            $table->string('payment_type', 20)->nullable()->after('payment_holder');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn('payment_type'));
        Schema::table('site_contacts', fn (Blueprint $t) => $t->dropColumn(['payment_holder', 'payment_type']));
    }
};
