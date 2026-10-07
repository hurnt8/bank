<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Virements restés « frais requis » alors que leur facture de frais est déjà payée : ils repassent « en attente » (en cours de traitement). */
    public function up(): void
    {
        DB::table('transfers')
            ->where('status', 'fee_required')
            ->whereIn('invoice_id', DB::table('invoices')->where('status', 'paid')->select('id'))
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Pas de retour en arrière : l'ancien statut n'est pas conservé.
    }
};
