<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Traduit automatiquement le libellé (et les choix) d'un champ de vérification d'identité vers les langues du site.
 * Utilise Groq (clé GROQ_API_KEY, qualité supérieure) si elle est configurée, sinon le service gratuit MyMemory.
 * Ne lève jamais d'exception : en cas d'échec, les langues concernées gardent le libellé par défaut.
 */
class KycTranslator
{
    /**
     * @param  string[]  $texts    textes sources, dans l'ordre : [libellé, choix 1, choix 2, …]
     * @param  string[]  $locales  codes de langue cibles
     * @return array<string, string[]>  [code => textes traduits dans le même ordre] (seulement les langues réussies)
     */
    public static function translate(array $texts, array $locales, string $source = 'fr'): array
    {
        $locales = array_values(array_diff($locales, [$source]));
        $texts   = array_values($texts);
        if (! $texts || ! $locales) {
            return [];
        }

        try {
            if (config('services.groq.key')) {
                $out = self::viaGroq($texts, $locales, $source);
                if ($out) {
                    return $out;
                }
            }

            return self::viaMyMemory($texts, $locales, $source);
        } catch (\Throwable $e) {
            Log::warning('KycTranslator: échec', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Complète les traductions manquantes d'un champ personnalisé (libellé et choix) pour toutes les langues du site,
     * sans écraser une traduction existante. À lancer en arrière-plan (commande kyc:translate-field).
     */
    public static function fillField(\App\Models\KycField $f): void
    {
        if ($f->builtin) {
            return;
        }

        $locales = \App\Models\Language::enabledCodes();
        $labels  = $f->labels ?? [];
        $opts    = $f->options_i18n ?? [];
        $auto    = $f->auto_locales ?? [];
        $options = $f->type === 'select' ? array_values($f->options ?? []) : null;

        $needs = [];
        foreach ($locales as $code) {
            $labelMissing = trim((string) ($labels[$code] ?? '')) === '';
            $optsMissing  = $options !== null && empty($opts[$code]);
            if ($labelMissing || $optsMissing) {
                $needs[$code] = [$labelMissing, $optsMissing];
            }
        }
        if (! $needs) {
            return;
        }

        $tr = self::translate(array_merge([$f->label], $options ?? []), array_keys($needs));
        foreach ($tr as $code => $row) {
            [$labelMissing, $optsMissing] = $needs[$code];
            if ($labelMissing) {
                $labels[$code] = $row[0];
                $auto[]        = $code;
            }
            if ($optsMissing && $options !== null && count($row) === count($options) + 1) {
                $opts[$code] = array_slice($row, 1);
            }
        }

        // Relecture : l'administrateur a pu corriger une traduction pendant ce temps — on ne l'écrase pas
        $fresh = \App\Models\KycField::find($f->id);
        if (! $fresh) {
            return;
        }
        $curLabels = $fresh->labels ?? [];
        $curOpts   = $fresh->options_i18n ?? [];
        foreach ($labels as $code => $text) {
            if (trim((string) ($curLabels[$code] ?? '')) === '') {
                $curLabels[$code] = $text;
            }
        }
        foreach ($opts as $code => $list) {
            if (empty($curOpts[$code])) {
                $curOpts[$code] = $list;
            }
        }
        $fresh->update([
            'labels'       => $curLabels ?: null,
            'options_i18n' => $curOpts ?: null,
            'auto_locales' => array_values(array_unique(array_merge($fresh->auto_locales ?? [], $auto))) ?: null,
        ]);
    }

    private static function viaGroq(array $texts, array $locales, string $source): array
    {
        $res = Http::withToken(config('services.groq.key'))->timeout(30)->post(config('services.groq.url'), [
            'model'           => config('services.groq.model'),
            'temperature'     => 0.1,
            'response_format' => ['type' => 'json_object'],
            'messages'        => [
                ['role' => 'system', 'content' => 'You translate short labels of a bank identity-verification form. Reply with JSON only: an object whose keys are the requested ISO language codes and whose values are arrays of translated strings, same order and same length as the input array. Keep them short and natural for a form field; do not add punctuation that is not in the source.'],
                ['role' => 'user', 'content' => json_encode(['source_language' => $source, 'languages' => $locales, 'texts' => $texts], JSON_UNESCAPED_UNICODE)],
            ],
        ]);

        if (! $res->successful()) {
            return [];
        }

        $data = json_decode((string) $res->json('choices.0.message.content'), true);
        $out  = [];
        foreach ($locales as $code) {
            $row = $data[$code] ?? null;
            if (is_array($row) && count($row) === count($texts) && ! in_array('', array_map(fn ($t) => trim((string) $t), $row), true)) {
                $out[$code] = array_map(fn ($t) => trim((string) $t), array_values($row));
            }
        }

        return $out;
    }

    private static function viaMyMemory(array $texts, array $locales, string $source): array
    {
        // Un appel par (langue, texte), par petits lots (le service gratuit limite le débit) et avec relance des échecs
        $pending = [];
        foreach ($locales as $code) {
            foreach ($texts as $i => $text) {
                $pending["$code|$i"] = [$code, $text];
            }
        }
        $done = [];

        for ($round = 0; $round < 3 && $pending; $round++) {
            foreach (array_chunk($pending, 6, true) as $chunk) {
                $responses = Http::pool(function ($pool) use ($chunk, $source) {
                    $calls = [];
                    foreach ($chunk as $key => [$code, $text]) {
                        $calls[] = $pool->as($key)->timeout(10)->get('https://api.mymemory.translated.net/get', ['q' => $text, 'langpair' => $source . '|' . $code]);
                    }

                    return $calls;
                });

                foreach ($chunk as $key => [$code, $text]) {
                    $r = $responses[$key] ?? null;
                    $t = ($r && ! ($r instanceof \Throwable) && $r->successful()) ? trim((string) $r->json('responseData.translatedText')) : '';
                    if ($t === '' || stripos($t, 'MYMEMORY WARNING') !== false || stripos($t, 'INVALID') !== false) {
                        continue;
                    }
                    $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5);
                    // pas de ponctuation finale absente du texte source
                    if (! preg_match('/[.,;:!?]$/', $text)) {
                        $t = rtrim($t, '.,;:');
                    }
                    $done[$key] = $t;
                    unset($pending[$key]);
                }
                usleep(250000);
            }
        }

        $out = [];
        foreach ($locales as $code) {
            $row = [];
            foreach ($texts as $i => $text) {
                if (! isset($done["$code|$i"])) {
                    continue 2;       // une traduction manquante : cette langue garde le libellé par défaut
                }
                $row[] = $done["$code|$i"];
            }
            $out[$code] = $row;
        }

        return $out;
    }
}
