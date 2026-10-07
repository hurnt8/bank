<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Langues dont la traduction a été générée automatiquement (donc régénérable si le libellé change). */
    public function up(): void
    {
        Schema::table('kyc_fields', function (Blueprint $table) {
            $table->json('auto_locales')->nullable()->after('options_i18n');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_fields', fn (Blueprint $t) => $t->dropColumn('auto_locales'));
    }
};
