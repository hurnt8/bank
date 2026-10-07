<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Les types de virement proposés sont « SEPA » et « en temps réel » : l'ancien « international » devient « instant ». */
    public function up(): void
    {
        DB::table('invoices')->where('payment_type', 'international')->update(['payment_type' => 'instant']);
        DB::table('site_contacts')->where('payment_type', 'international')->update(['payment_type' => 'instant']);
        DB::table('account_movements')->where('kind', 'international')->update(['kind' => 'instant']);
    }

    public function down(): void
    {
        DB::table('invoices')->where('payment_type', 'instant')->update(['payment_type' => 'international']);
        DB::table('site_contacts')->where('payment_type', 'instant')->update(['payment_type' => 'international']);
        DB::table('account_movements')->where('kind', 'instant')->update(['kind' => 'international']);
    }
};
