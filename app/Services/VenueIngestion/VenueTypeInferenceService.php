<?php

namespace App\Services\VenueIngestion;

class VenueTypeInferenceService
{
    private const BLACKLIST_NAME_PATTERNS = [
        'basic fit',
        'keep cool',
        'fitness park',
        'fitnesspark',
        'elancia',
        'l\'orange bleue',
        'orange bleue',
        'curves',
        'moving',
        'waou',
        'électric',
        'one fitness',
        'gymnase',
        'complexe sportif',
        'city stade',
        'city-stade',
        'plateau sportif',
        'espace sportif',
        'stade municipal',
        'salle polyvalente',
        'salle omnisports',
        'boulodrome',
        'municipal',
        'mairie',
    ];

    private const GOOGLE_TYPE_HINTS = [
        'bowling_alley' => ['category' => 'Bowling', 'activities' => ['Bowling']],
        'golf_course' => ['category' => 'Golf', 'activities' => ['Golf']],
        'ice_rink' => ['category' => 'Hockey', 'activities' => ['Ice Skating']],
        'swimming_pool' => ['category' => 'Swimming', 'activities' => ['Swimming']],
        'spa' => ['category' => 'Wellness', 'activities' => ['Spa', 'Wellness']],
        'stadium' => ['category' => 'Football', 'activities' => ['Football']],
        'amusement_park' => ['category' => 'Team', 'activities' => ['Arcade']],
    ];

    private const NAME_KEYWORDS = [
        'laser game' => ['category' => 'Laser Game', 'activities' => ['Laser Game']],
        'laser' => ['category' => 'Laser Game', 'activities' => ['Laser Game']],
        'karting' => ['category' => 'Karting', 'activities' => ['Karting']],
        'kart' => ['category' => 'Karting', 'activities' => ['Karting']],
        'bowling' => ['category' => 'Bowling', 'activities' => ['Bowling']],
        'escape game' => ['category' => 'Team', 'activities' => ['Escape Game']],
        'escape' => ['category' => 'Team', 'activities' => ['Escape Game']],
        'piscine' => ['category' => 'Swimming', 'activities' => ['Swimming']],
        'patinoire' => ['category' => 'Hockey', 'activities' => ['Ice Skating']],
        'mini-golf' => ['category' => 'Golf', 'activities' => ['Mini Golf']],
        'mini golf' => ['category' => 'Golf', 'activities' => ['Mini Golf']],
        'minigolf' => ['category' => 'Golf', 'activities' => ['Mini Golf']],
        'golf' => ['category' => 'Golf', 'activities' => ['Golf']],
        'padel' => ['category' => 'Padel', 'activities' => ['Padel']],
        'tennis' => ['category' => 'Tennis', 'activities' => ['Tennis']],
        'escalade' => ['category' => 'Climbing', 'activities' => ['Escalade', 'Climbing']],
        'climbing' => ['category' => 'Climbing', 'activities' => ['Climbing']],
        'trampoline' => ['category' => 'Fitness', 'activities' => ['Trampoline']],
        'squash' => ['category' => 'Badminton', 'activities' => ['Squash']],
        'badminton' => ['category' => 'Badminton', 'activities' => ['Badminton']],
        'football' => ['category' => 'Football', 'activities' => ['Football']],
        'foot' => ['category' => 'Football', 'activities' => ['Football']],
        'basket' => ['category' => 'Basketball', 'activities' => ['Basketball']],
        'basketball' => ['category' => 'Basketball', 'activities' => ['Basketball']],
        'volley' => ['category' => 'Volleyball', 'activities' => ['Volleyball']],
        'yoga' => ['category' => 'Yoga', 'activities' => ['Yoga']],
        'boxe' => ['category' => 'Boxing', 'activities' => ['Boxing']],
        'boxing' => ['category' => 'Boxing', 'activities' => ['Boxing']],
        'spa' => ['category' => 'Wellness', 'activities' => ['Spa', 'Wellness']],
    ];

    public function isBlacklisted(array $candidate): bool
    {
        $name = strtolower($candidate['name'] ?? '');
        foreach (self::BLACKLIST_NAME_PATTERNS as $pattern) {
            if (str_contains($name, $pattern)) {
                return true;
            }
        }
        return false;
    }

    public function infer(array $candidate): array
    {
        if ($this->isBlacklisted($candidate)) {
            return ['suitable' => false, 'reject_reason' => 'blacklisted_name'];
        }

        $googleTypes = $candidate['google_types'] ?? [];
        foreach ($googleTypes as $type) {
            if (isset(self::GOOGLE_TYPE_HINTS[$type])) {
                return array_merge(self::GOOGLE_TYPE_HINTS[$type], ['suitable' => true]);
            }
        }

        $name = strtolower($candidate['name'] ?? '');
        $sortedKeywords = self::NAME_KEYWORDS;
        uksort($sortedKeywords, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        foreach ($sortedKeywords as $keyword => $hint) {
            if (str_contains($name, $keyword)) {
                return array_merge($hint, ['suitable' => true]);
            }
        }

        return ['suitable' => true];
    }

    public function mergeClassification(array $candidate, array $classification): array
    {
        $hint = $this->infer($candidate);

        if (!($hint['suitable'] ?? false) && isset($hint['reject_reason'])) {
            $classification['suitable'] = false;
            $classification['reject_reason'] = $hint['reject_reason'];
            return $classification;
        }

        if (empty($classification['description'])) {
            $classification['description'] = $candidate['name'] ?? '';
        }

        $classification['category'] = $this->resolveCategoryName($candidate, $classification);
        $classification['activities'] = $this->resolveActivities($candidate, $classification);

        return $classification;
    }

    public function resolveCategoryName(array $candidate, array $classification): string
    {
        $hint = $this->infer($candidate);
        $aiCategory = $classification['category'] ?? 'Team';

        if (($hint['category'] ?? 'Team') !== 'Team') {
            return $hint['category'];
        }

        if ($aiCategory !== 'Team' && $aiCategory !== '') {
            return $aiCategory;
        }

        return $hint['category'] ?? 'Team';
    }

    public function resolveActivities(array $candidate, array $classification): array
    {
        $hint = $this->infer($candidate);
        $activities = array_values(array_filter($classification['activities'] ?? []));

        if (!empty($activities)) {
            return $activities;
        }

        if (!empty($hint['activities'])) {
            return $hint['activities'];
        }

        $category = $this->resolveCategoryName($candidate, $classification);

        return $category !== 'Team' ? [$category] : [];
    }
}
