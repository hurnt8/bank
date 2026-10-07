<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->string('reason', 500)->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        // Les IBAN générés sont désormais allemands : code banque (BLZ) à 8 chiffres.
        DB::statement('ALTER TABLE site_contacts MODIFY iban_bank_code VARCHAR(8) NULL');
        DB::table('site_contacts')->update(['iban_bank_code' => null]);
    }

    public function down(): void
    {
        Schema::dropIfExists('card_requests');
    }
};
