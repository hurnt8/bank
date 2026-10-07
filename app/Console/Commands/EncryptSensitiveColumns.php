<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptSensitiveColumns extends Command
{
    protected $signature = 'security:encrypt-sensitive-columns {--dry-run : Compte les lignes à chiffrer sans écrire}';

    protected $description = 'Chiffre en place (APP_KEY) les colonnes sensibles existantes en clair : bank_account, bic, id_number, tax_number, npi';

    private array $targets = [
        'users'         => ['bank_account', 'bic', 'id_number', 'tax_number'],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        foreach ($this->targets as $table => $columns) {
            $rows  = DB::table($table)->select(array_merge(['id'], $columns))->get();
            $count = 0;

            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if ($value === null || $value === '' || $this->looksEncrypted($value)) {
                        continue;
                    }

                    $updates[$column] = Crypt::encryptString($value);
                }

                if (!empty($updates)) {
                    $count++;
                    if (!$dryRun) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            }

            $this->info("{$table} : {$count} ligne(s) " . ($dryRun ? 'à chiffrer (dry-run, aucune écriture)' : 'chiffrée(s)'));
        }

        if ($dryRun) {
            $this->warn('Dry-run : relancez sans --dry-run pour écrire réellement en base.');
        }

        return self::SUCCESS;
    }

    // Détecte le format du cast "encrypted" de Laravel (JSON base64 avec iv/value/mac)
    // pour ne jamais re-chiffrer une valeur déjà chiffrée si la commande est relancée.
    private function looksEncrypted(string $value): bool
    {
        $decoded = json_decode($value, true);
        return is_array($decoded) && isset($decoded['iv'], $decoded['value'], $decoded['mac']);
    }
}
