<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valide le format général d'un IBAN (2 lettres pays + 2 chiffres de clé +
 * jusqu'à 30 caractères alphanumériques) et sa clé de contrôle via
 * l'algorithme modulo 97 défini par l'ISO 7064.
 */
class ValidIban implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // \s couvre les espaces normaux/tabs/retours à la ligne ; \x{00A0} couvre
        // l'espace insécable que de nombreux sites bancaires/PDF utilisent pour
        // afficher un IBAN formaté, et qui survit à un simple str_replace(' ', '').
        $iban = strtoupper((string) preg_replace('/[\s\x{00A0}]+/u', '', (string) $value));

        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban)) {
            $fail('Le format de l\'IBAN est invalide.');
            return;
        }

        if (! $this->isChecksumValid($iban)) {
            $fail('La clé de contrôle de l\'IBAN est invalide.');
        }
    }

    private function isChecksumValid(string $iban): bool
    {
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        // bcmod requiert l'extension bcmath ; repli par découpage en blocs sinon.
        if (function_exists('bcmod')) {
            return bcmod($numeric, '97') === '1';
        }

        $remainder = $numeric;
        while (strlen($remainder) > 9) {
            $block = substr($remainder, 0, 9);
            $remainder = (string) ((int) $block % 97) . substr($remainder, 9);
        }

        return ((int) $remainder % 97) === 1;
    }
}
