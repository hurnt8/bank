<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupportAiService
{
    private string $apiUrl;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiUrl = config('services.groq.url');
        $this->apiKey = config('services.groq.key');
        $this->model  = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Generate an AI support reply in the client's language.
     * Returns null silently on any failure.
     */
    public function generateReply(User $client, Collection $history): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $messages = [['role' => 'system', 'content' => $this->buildSystemPrompt($client)]];

        // Pass last 8 messages as conversation context (excluding current one)
        foreach ($history->slice(-8) as $msg) {
            $role    = $msg->sender_type === 'client' ? 'user' : 'assistant';
            $content = $msg->body
                ?: ($msg->file_type === 'image' ? '[Image partagée]' : '[Fichier]');

            $messages[] = ['role' => $role, 'content' => $content];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post($this->apiUrl, [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'max_tokens'  => 400,
                    'temperature' => 0.65,
                ]);

            if (! $response->successful()) {
                Log::warning('SupportAI: Groq error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $text = $response->json('choices.0.message.content');
            return is_string($text) && strlen(trim($text)) > 0 ? trim($text) : null;

        } catch (\Throwable $e) {
            Log::warning('SupportAI: exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private const LOCALE_NAMES = [
        'fr' => 'français', 'en' => 'anglais',    'pl' => 'polonais', 'es' => 'espagnol',
        'bg' => 'bulgare',  'hu' => 'hongrois',    'it' => 'italien',  'de' => 'allemand',
        'lt' => 'lituanien','ro' => 'roumain',     'lv' => 'letton',   'nl' => 'néerlandais',
        'pt' => 'portugais','hr' => 'croate',
    ];

    private function buildSystemPrompt(User $client): string
    {
        $name     = $client->name;
        $locale   = $client->locale ?? app()->getLocale();
        $language = self::LOCALE_NAMES[$locale] ?? null;
        $siteName = site_name();

        $languageRule = $language
            ? "Réponds IMPÉRATIVEMENT en {$language}, quelle que soit la langue utilisée par le client dans son message et quelle que soit la langue de ce prompt système"
            : "Détecte automatiquement la langue du client et réponds TOUJOURS dans la même langue";

        return <<<PROMPT
Tu es l'assistant IA de support de {$siteName}, une plateforme bancaire en ligne : comptes, cartes, virements et services financiers.

MISSION :
- Accusé de réception du message du client de façon chaleureuse
- Répondre de façon utile aux questions courantes : vérification d'identité, cartes, RIB, transferts, factures
- Informer que le conseiller humain prendra le relais prochainement pour les demandes personnalisées
- NE JAMAIS inventer de données spécifiques (montants, dates, numéros de dossier) que tu ne connais pas

RÈGLES IMPÉRATIVES :
- {$languageRule}
- Sois concis : 3 à 5 phrases maximum — pas de listes longues
- Ton professionnel, rassurant et empathique
- Termine TOUJOURS par : "— Assistant {$siteName}"

Nom du client : {$name}
PROMPT;
    }
}
