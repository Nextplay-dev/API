<?php

namespace App\Services\VenueIngestion;

use App\Models\Category;
use Illuminate\Support\Facades\Http;

class AIClassifierService
{
    public function classify(string $title, string $description, array $headings, string $bodyText): array
    {
        $categories = Category::pluck('name')->toArray();
        $categoriesList = implode(', ', $categories);

        $headingsStr = implode(' | ', $headings);
        $prompt = <<<PROMPT
Tu es un assistant expert dans la catégorisation des établissements de loisirs et de sport.
Analyse les informations suivantes du site web :
Titre : {$title}
Description Méta : {$description}
En-têtes : {$headingsStr}
Extrait de texte de la page : {$bodyText}

Ta tâche consiste à classer cet établissement dans EXACTEMENT UNE catégorie principale, à fournir une liste des activités disponibles, une courte description (maximum 200 caractères) et à déterminer s'il s'agit d'un établissement de sport/loisirs/bien-être commercial et réservable.

La catégorie principale doit être EXACTEMENT l'une de ces valeurs : [{$categoriesList}]. Si aucune ne correspond parfaitement, utilise la correspondance la plus proche ou "Team".
Les activités doivent correspondre à une liste des sports ou des activités proposés dans cet établissement.

Tu DOIS répondre avec un objet JSON valide sous ce format :
{
  "category": "SelectedCategoryName",
  "activities": ["Activity1", "Activity2"],
  "description": "Courte description en 1 à 2 phrases de l'établissement.",
  "suitable": true
}
PROMPT;

        $apiKey = env('GEMINI_API_KEY');

        if ($apiKey) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json'
                        ]
                    ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $text = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $result = json_decode($text, true);

                    if (isset($result['category']) && in_array($result['category'], $categories)) {
                        return [
                            'category' => $result['category'],
                            'activities' => $result['activities'] ?? [],
                            'description' => $result['description'] ?? mb_substr($description, 0, 200),
                            'suitable' => (bool) ($result['suitable'] ?? true),
                        ];
                    }
                }
            } catch (\Exception) {}
        }

        return $this->fallbackClassify($title, $description, $headings, $bodyText, $categories);
    }

    protected function fallbackClassify(string $title, string $description, array $headings, string $bodyText, array $categories): array
    {
        $fullText = strtolower($title . ' ' . $description . ' ' . implode(' ', $headings) . ' ' . $bodyText);

        $matchedCategory = 'Team';
        $matchedActivities = [];
        $suitable = false;

        $keywords = [
            'padel' => ['category' => 'Padel', 'activities' => ['Padel']],
            'tennis' => ['category' => 'Tennis', 'activities' => ['Tennis']],
            'badminton' => ['category' => 'Badminton', 'activities' => ['Badminton']],
            'squash' => ['category' => 'Badminton', 'activities' => ['Squash']],
            'football' => ['category' => 'Football', 'activities' => ['Football', 'Foot 5']],
            'soccer' => ['category' => 'Football', 'activities' => ['Foot 5']],
            'basketball' => ['category' => 'Basketball', 'activities' => ['Basketball']],
            'volleyball' => ['category' => 'Volleyball', 'activities' => ['Volleyball']],
            'bowling' => ['category' => 'Bowling', 'activities' => ['Bowling']],
            'swimming' => ['category' => 'Swimming', 'activities' => ['Swimming']],
            'piscine' => ['category' => 'Swimming', 'activities' => ['Swimming']],
            'golf' => ['category' => 'Golf', 'activities' => ['Golf']],
            'cycling' => ['category' => 'Cycling', 'activities' => ['Cycling']],
            'velo' => ['category' => 'Cycling', 'activities' => ['Cycling']],
            'yoga' => ['category' => 'Yoga', 'activities' => ['Yoga']],
            'pilates' => ['category' => 'Yoga', 'activities' => ['Yoga']],
            'boxing' => ['category' => 'Boxing', 'activities' => ['Boxing']],
            'boxe' => ['category' => 'Boxing', 'activities' => ['Boxing']],
            'fitness' => ['category' => 'Fitness', 'activities' => ['Fitness', 'Gym']],
            'muscu' => ['category' => 'Fitness', 'activities' => ['Fitness']],
            'escalade' => ['category' => 'Climbing', 'activities' => ['Escalade', 'Climbing']],
            'climbing' => ['category' => 'Climbing', 'activities' => ['Climbing']],
            'kart' => ['category' => 'Karting', 'activities' => ['Karting']],
            'laser' => ['category' => 'Laser Game', 'activities' => ['Laser Game']],
            'spa' => ['category' => 'Wellness', 'activities' => ['Spa', 'Wellness']],
            'sauna' => ['category' => 'Wellness', 'activities' => ['Wellness']],
        ];

        foreach ($keywords as $key => $mapping) {
            if (str_contains($fullText, $key)) {
                $suitable = true;
                if (in_array($mapping['category'], $categories)) {
                    $matchedCategory = $mapping['category'];
                }
                foreach ($mapping['activities'] as $act) {
                    if (!in_array($act, $matchedActivities)) {
                        $matchedActivities[] = $act;
                    }
                }
            }
        }

        if (empty($matchedActivities)) {
            $matchedActivities[] = $matchedCategory;
        }

        $desc = !empty($description) ? $description : (!empty($title) ? $title : "Loisir et sport partenaire.");
        $desc = mb_substr($desc, 0, 200);

        return [
            'category' => $matchedCategory,
            'activities' => $matchedActivities,
            'description' => $desc,
            'suitable' => $suitable,
        ];
    }
}
