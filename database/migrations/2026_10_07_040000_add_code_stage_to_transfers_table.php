<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Nombre de codes déjà saisis par le client (0 à 3) : 1er → 70 %, 2e → 99 %, 3e → 100 %. */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedTinyInteger('code_stage')->default(0)->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('code_stage');
        });
    }
};
