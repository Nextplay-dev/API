<?php

namespace App\Services\VenueIngestion;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClassifierService
{
    public function classify(array $item, string $title, string $description, array $headings, string $bodyText): array
    {
        $categories = Category::pluck('name')->toArray();
        $categoriesList = implode(', ', $categories);

        $venueName = $item['name'] ?? '';
        $websiteUrl = $item['website'] ?? '';
        $address = $item['address'] ?? '';

        $safeName = preg_replace('/[^\P{C}\n\r]+/u', '', $venueName);
        $safeTitle = preg_replace('/[^\P{C}\n\r]+/u', '', $title);
        $safeDescription = preg_replace('/[^\P{C}\n\r]+/u', '', $description);
        
        $headingsList = implode(', ', array_map(fn($h) => preg_replace('/[^\P{C}\n\r]+/u', '', $h), $headings));
        $headingsList = mb_substr($headingsList, 0, 1000);
        
        $safeBodyText = preg_replace('/[^\P{C}\n\r]+/u', '', $bodyText);
        $bodyExcerpt = mb_substr($safeBodyText, 0, 3000);

        $prompt = <<<PROMPT
Tu es un assistant expert dans la catégorisation des établissements de loisirs et de sport.

Voici les informations sur l'établissement cible :
- Nom : {$safeName}
- Site web : {$websiteUrl}
- Adresse : {$address}

Voici le contenu textuel extrait de son site web par notre crawler (attention, ce contenu peut être incomplet, vide, ou contenir des erreurs comme 'JavaScript required' ou des bannières de cookies si le site utilise React/Vue ou des protections) :
- Titre de la page : {$safeTitle}
- Description Meta : {$safeDescription}
- En-têtes trouvées : {$headingsList}
- Extrait du contenu textuel :
{$bodyExcerpt}

Tâche :
Détermine la catégorie principale, les activités proposées, une description et si l'établissement est commercial/réservable.
IMPORTANT : Si le contenu extrait du site web est vide, inutile, ou s'il s'agit d'un message d'erreur/cookies (ex: site React/Vue non chargé), utilise tes connaissances générales sur cet établissement précis (grâce à son nom, adresse et site web) pour répondre au mieux.

Détermine si cet établissement est un établissement de sport, loisir ou bien-être COMMERCIAL et RÉSERVABLE par le public. Il doit s'agir d'un lieu physique où des clients peuvent réserver et pratiquer une activité (ex: club de padel, salle d'escalade, bowling, golf, karting, escape game, laser game, salle de sport privée, etc.).

Exclus les lieux suivants :
- Gymnases municipaux, complexes sportifs publics, stades municipaux, city-stades
- Salles polyvalentes ou omnisports municipales
- Tout lieu géré par une administration publique ou une association non-commerciale
- Magasins de détail (vêtements, équipement, alimentation, etc.)
- Centres aquatiques municipales

Extrais les informations suivantes :
1. La catégorie principale (exactement l'une des valeurs suivantes : [{$categoriesList}])
2. La liste des activités proposées
3. Une courte description (max 200 caractères)

Réponds avec un objet JSON valide sous ce format :
{
  "category": "SelectedCategoryName",
  "activities": ["Activity1", "Activity2"],
  "description": "Courte description en 1 à 2 phrases.",
  "suitable": true
}

Le champ "suitable" doit être true UNIQUEMENT si l'établissement est un lieu de sport/loisir commercial et réservable. Dans le doute, mets false.
PROMPT;

        $fallbackDescription = empty($safeDescription) ? $safeName : $safeDescription;

        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            Log::warning('[AIClassifier] No OpenAI API key configured');
            return ['suitable' => false, 'reject_reason' => 'no_api_key', 'description' => $fallbackDescription];
        }

        try {
            $payload = [
                'model' => 'gpt-4.1-nano',
                'input' => $prompt,
                'temperature' => 0.1,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post('https://api.openai.com/v1/responses', $payload);

            if (!$response->successful()) {
                Log::warning('[AIClassifier] OpenAI API error', [
                    'status' => $response->status(),
                    'body' => mb_substr($response->body(), 0, 500),
                ]);
                return ['suitable' => false, 'reject_reason' => 'api_error', 'description' => $fallbackDescription];
            }

            $resJson = $response->json();
            $text = '';
            foreach ($resJson['output'] ?? [] as $outputItem) {
                if (($outputItem['type'] ?? '') === 'message') {
                    foreach ($outputItem['content'] ?? [] as $content) {
                        if (($content['type'] ?? '') === 'output_text' && !empty($content['text'])) {
                            $text = $content['text'];
                            break 2;
                        }
                    }
                }
            }

            if (empty($text)) {
                Log::warning('[AIClassifier] Empty response - raw structure', [
                    'output_keys' => array_keys($resJson['output'] ?? []),
                    'output_types' => array_map(fn($o) => $o['type'] ?? 'unknown', $resJson['output'] ?? []),
                ]);
                return ['suitable' => false, 'reject_reason' => 'empty_response', 'description' => $fallbackDescription];
            }

            $result = json_decode($text, true);

            if (!is_array($result) || !isset($result['category'])) {
                // Try extracting JSON from markdown code block
                if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $m)) {
                    $result = json_decode($m[1], true);
                }
            }

            if (!is_array($result) || !isset($result['category'])) {
                Log::warning('[AIClassifier] Invalid JSON response from OpenAI', [
                    'raw' => mb_substr($text, 0, 300),
                ]);
                return ['suitable' => false, 'reject_reason' => 'invalid_response', 'description' => $fallbackDescription];
            }

            $category = in_array($result['category'], $categories, true)
                ? $result['category']
                : 'Team';

            Log::info('[AIClassifier] Success', [
                'category' => $category,
                'suitable' => $result['suitable'] ?? true,
                'venue_name' => $safeName,
            ]);

            return [
                'category' => $category,
                'activities' => $result['activities'] ?? [],
                'description' => $result['description'] ?? $fallbackDescription,
                'suitable' => (bool) ($result['suitable'] ?? true),
            ];
        } catch (\Exception $e) {
            Log::error('[AIClassifier] Exception', [
                'message' => $e->getMessage(),
            ]);
            return ['suitable' => false, 'reject_reason' => 'exception', 'description' => $fallbackDescription];
        }
    }
}
