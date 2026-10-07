<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Traductions des champs personnalisés : libellé et choix de liste, par langue. */
    public function up(): void
    {
        Schema::table('kyc_fields', function (Blueprint $table) {
            $table->json('labels')->nullable()->after('label');
            $table->json('options_i18n')->nullable()->after('options');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_fields', fn (Blueprint $t) => $t->dropColumn(['labels', 'options_i18n']));
    }
};
