<?php

namespace App\Console\Commands;

use App\Models\KycField;
use App\Services\KycTranslator;
use Illuminate\Console\Command;

class TranslateKycField extends Command
{
    protected $signature = 'kyc:translate-field {id : identifiant du champ} {--all : traduire tous les champs personnalisés}';
    protected $description = 'Traduit automatiquement les champs personnalisés du formulaire de vérification d’identité dans toutes les langues du site';

    public function handle(): int
    {
        @set_time_limit(0);

        $fields = $this->option('all') ? KycField::where('builtin', false)->get() : KycField::where('id', $this->argument('id'))->get();
        foreach ($fields as $f) {
            KycTranslator::fillField($f);
            $this->info('Champ ' . $f->id . ' (' . $f->label . ') traduit.');
        }

        return self::SUCCESS;
    }
}
