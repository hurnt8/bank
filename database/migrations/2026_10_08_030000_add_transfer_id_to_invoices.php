<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Virement concerné par la facture (frais de traitement). */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('transfer_id')->nullable()->after('client_id');
            $table->index('transfer_id');
        });

        // Factures de frais déjà créées depuis un virement
        DB::statement('UPDATE invoices i JOIN transfers t ON t.invoice_id = i.id SET i.transfer_id = t.id');
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn('transfer_id'));
    }
};
