<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Progression d'un virement (0-100 %) fixée par l'administrateur, avec un éventuel palier
     * bloquant : à ce niveau le client doit saisir un code généré par son conseiller.
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('status');
            $table->boolean('code_required')->default(false)->after('progress');
            $table->text('unlock_code')->nullable()->after('code_required');        // chiffré (SafeEncrypted)
            $table->timestamp('code_generated_at')->nullable()->after('unlock_code');
            $table->timestamp('code_verified_at')->nullable()->after('code_generated_at');
            $table->unsignedTinyInteger('code_attempts')->default(0)->after('code_verified_at');
            $table->timestamp('code_locked_until')->nullable()->after('code_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['progress', 'code_required', 'unlock_code', 'code_generated_at', 'code_verified_at', 'code_attempts', 'code_locked_until']);
        });
    }
};
