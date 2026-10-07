<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Demande de carte : type (virtuelle/physique), nom, adresse de livraison, frais fixés par l'admin et facture associée. */
    public function up(): void
    {
        Schema::table('card_requests', function (Blueprint $table) {
            $table->string('card_type', 10)->default('virtual')->after('status');
            $table->string('holder_name', 100)->nullable()->after('card_type');
            $table->string('delivery_address', 255)->nullable()->after('holder_name');
            $table->string('delivery_zip', 20)->nullable()->after('delivery_address');
            $table->string('delivery_city', 100)->nullable()->after('delivery_zip');
            $table->string('delivery_country', 100)->nullable()->after('delivery_city');
            $table->decimal('fee_amount', 12, 2)->nullable()->after('delivery_country');
            $table->foreignId('invoice_id')->nullable()->after('fee_amount')->constrained('invoices')->nullOnDelete();
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->string('card_type', 10)->default('virtual')->after('network');
        });
    }

    public function down(): void
    {
        Schema::table('card_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropColumn(['card_type', 'holder_name', 'delivery_address', 'delivery_zip', 'delivery_city', 'delivery_country', 'fee_amount']);
        });
        Schema::table('cards', fn (Blueprint $t) => $t->dropColumn('card_type'));
    }
};
