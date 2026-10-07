<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Champs du formulaire de vérification d'identité, configurables par l'administration
        Schema::create('kyc_fields', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();          // identifiant technique (slug)
            $table->string('label_key', 80)->nullable();  // champ natif : clé de traduction
            $table->string('label', 150)->nullable();     // champ personnalisé : libellé saisi par l'admin
            $table->string('type', 20);                   // text, textarea, date, country, doc_type, select, file, image
            $table->json('options')->nullable();          // choix d'une liste (select)
            $table->unsignedTinyInteger('step')->default(1);
            $table->boolean('required')->default(true);
            $table->boolean('enabled')->default(true);
            $table->boolean('builtin')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Réponses aux champs personnalisés
        Schema::create('kyc_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('field_key', 60);
            $table->text('value')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'field_key']);
        });

        // 1 ou 2 étapes de vérification
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->unsignedTinyInteger('kyc_steps')->default(2);
        });

        $now = now();
        $builtin = [
            // key, label_key, type, step, required
            ['birth_date',         'onboarding.f_birth_date',  'date',     1, true],
            ['country',            'onboarding.f_country',     'country',  1, true],
            ['address',            'onboarding.f_address',     'text',     1, true],
            ['id_type',            'onboarding.f_id_type',     'doc_type', 1, true],
            ['id_number',          'onboarding.f_id_number',   'text',     1, true],
            ['date_delivre',       'onboarding.f_date_delivre', 'date',    1, false],
            ['tax_number',         'onboarding.f_tax_number',  'text',     1, false],
            ['activity',           'onboarding.f_activity',    'text',     1, false],
            ['id_document_front',  'kyc.label_id_front',       'file',     2, true],
            ['id_document_back',   'kyc.label_id_back',        'file',     2, false],
            ['selfie',             'kyc.label_selfie',         'image',    2, true],
        ];
        foreach ($builtin as $i => [$key, $labelKey, $type, $step, $required]) {
            DB::table('kyc_fields')->insert([
                'key' => $key, 'label_key' => $labelKey, 'type' => $type, 'step' => $step, 'required' => $required,
                'enabled' => true, 'builtin' => true, 'sort' => ($i + 1) * 10, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('site_contacts', fn (Blueprint $t) => $t->dropColumn('kyc_steps'));
        Schema::dropIfExists('kyc_answers');
        Schema::dropIfExists('kyc_fields');
    }
};
