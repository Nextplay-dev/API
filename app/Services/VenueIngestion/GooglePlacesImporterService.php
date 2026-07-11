<?php

namespace App\Services\VenueIngestion;

use Illuminate\Support\Facades\Http;

class GooglePlacesImporterService
{
    private const SEARCH_GROUPS = [
        ['padel', 'tennis'],
        ['escalade', 'bowling'],
        ['karting', 'escape game', 'laser game'],
        ['badminton', 'mini golf', 'trampoline'],
        ['squash', 'boxe', 'yoga'],
        ['golf', 'foot 5', 'football'],
        ['basketball', 'volleyball'],
        ['patinoire', 'piscine'],
    ];

    private const EXCLUDED_TYPES = [
        'gym',
        'health',
        'doctor',
        'physiotherapist',
        'dentist',
        'supermarket',
        'grocery_store',
        'convenience_store',
        'pharmacy',
        'restaurant',
        'cafe',
        'bar',
        'bakery',
        'store',
        'shopping_mall',
        'clothing_store',
        'shoe_store',
        'department_store',
        'book_store',
        'hardware_store',
        'home_goods_store',
        'liquor_store',
        'pet_store',
        'school',
        'university',
        'church',
        'place_of_worship',
        'cemetery',
        'locality',
        'political',
        'neighborhood',
        'administrative_area_level_1',
        'administrative_area_level_2',
        'administrative_area_level_3',
        'country',
        'postal_code',
    ];

    public function fetchVenuesAround(float $lat, float $lon, int $radius): array
    {
        $apiKey = config('services.google_maps.key');

        if (!$apiKey) {
            return [];
        }

        $seenPlaceIds = [];
        $candidates = [];

        foreach (self::SEARCH_GROUPS as $group) {
            $results = $this->searchByKeywords($group, $lat, $lon, $radius, $apiKey);
            \Illuminate\Support\Facades\Log::info('[GooglePlacesImporter] Group search results', [
                'group' => $group,
                'count' => count($results)
            ]);

            foreach ($results as $result) {
                $placeId = $result['place_id'] ?? null;

                if (!$placeId || isset($seenPlaceIds[$placeId])) {
                    continue;
                }

                $types = $result['types'] ?? [];

                if ($this->isExcludedType($types)) {
                    continue;
                }

                $name = $result['name'] ?? '';
                if (empty($name)) {
                    continue;
                }

                $geometry = $result['geometry'] ?? [];
                $location = $geometry['location'] ?? [];
                $latitude = $location['lat'] ?? null;
                $longitude = $location['lng'] ?? null;

                if (!$latitude || !$longitude) {
                    continue;
                }

                if ($this->distance($lat, $lon, $latitude, $longitude) > $radius) {
                    continue;
                }

                $seenPlaceIds[$placeId] = true;

                $candidates[] = [
                    'name' => $name,
                    'website' => null,
                    'address' => $result['vicinity'] ?? $result['formatted_address'] ?? '',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'google_place_id' => $placeId,
                    'google_types' => $types,
                    'phone' => null,
                    'opening_hours' => null,
                    'photo_reference' => $this->extractPhotoReference($result),
                ];
            }
        }

        \Illuminate\Support\Facades\Log::info('[GooglePlacesImporter] Candidates before details enrichment', [
            'count' => count($candidates)
        ]);

        $candidates = $this->enrichWithPlaceDetails($candidates, $apiKey);

        $candidates = array_values(array_filter(
            $candidates,
            fn ($c) => $this->distance($lat, $lon, $c['latitude'], $c['longitude']) <= $radius
        ));

        \Illuminate\Support\Facades\Log::info('[GooglePlacesImporter] Candidates after distance check', [
            'count' => count($candidates)
        ]);

        $candidates = array_values(array_filter(
            $candidates,
            fn ($c) => !$this->isExcludedType($c['google_types'] ?? [])
        ));

        \Illuminate\Support\Facades\Log::info('[GooglePlacesImporter] Candidates after final type exclusion', [
            'count' => count($candidates)
        ]);

        return $candidates;
    }

    public function fetchSinglePlace(string $placeId): ?array
    {
        $apiKey = config('services.google_maps.key');
        if (!$apiKey) {
            return null;
        }

        $fields = 'name,formatted_address,formatted_phone_number,website,opening_hours,types,geometry,photos,place_id';

        $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'fields' => $fields,
            'key' => $apiKey,
            'language' => 'fr',
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        $result = $data['result'] ?? [];

        if (empty($result)) {
            return null;
        }

        $geometry = $result['geometry'] ?? [];
        $location = $geometry['location'] ?? [];
        $latitude = $location['lat'] ?? null;
        $longitude = $location['lng'] ?? null;

        if (!$latitude || !$longitude) {
            return null;
        }

        return [
            'name' => $result['name'] ?? '',
            'website' => $result['website'] ?? null,
            'address' => $result['formatted_address'] ?? '',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'google_place_id' => $placeId,
            'google_types' => $result['types'] ?? [],
            'phone' => $result['formatted_phone_number'] ?? null,
            'opening_hours' => $this->parseGoogleHours($result['opening_hours'] ?? null),
            'photo_reference' => $this->extractPhotoReference($result),
        ];
    }

    private function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function searchByKeywords(array $keywords, float $lat, float $lon, int $radius, string $apiKey): array
    {
        $query = implode(' ', $keywords);
        $allResults = [];
        $params = [
            'keyword' => $query,
            'location' => "{$lat},{$lon}",
            'radius' => $radius,
            'type' => 'establishment',
            'key' => $apiKey,
            'language' => 'fr',
        ];

        $maxResultsPerGroup = 200;

        while (count($allResults) < $maxResultsPerGroup) {
            $response = Http::timeout(15)->get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', $params);

            if (!$response->successful()) {
                break;
            }

            $data = $response->json();
            $results = $data['results'] ?? [];
            $allResults = array_merge($allResults, $results);

            $nextToken = $data['next_page_token'] ?? null;
            if (!$nextToken || count($allResults) >= $maxResultsPerGroup) {
                break;
            }

            $params = [
                'pagetoken' => $nextToken,
                'key' => $apiKey,
            ];

            usleep(2000000);
        }

        return array_slice($allResults, 0, $maxResultsPerGroup);
    }

    private function enrichWithPlaceDetails(array $candidates, string $apiKey): array
    {
        foreach ($candidates as &$candidate) {
            $placeId = $candidate['google_place_id'];
            $details = $this->fetchSinglePlace($placeId);
            if ($details) {
                $candidate['website'] = $details['website'];
                $candidate['phone'] = $details['phone'];
                $candidate['address'] = $details['address'] ?: $candidate['address'];
                $candidate['google_types'] = $details['google_types'] ?: $candidate['google_types'];
                $candidate['opening_hours'] = $details['opening_hours'];
                $candidate['photo_reference'] = $details['photo_reference'] ?: $candidate['photo_reference'];
                $candidate['latitude'] = $details['latitude'];
                $candidate['longitude'] = $details['longitude'];
            }
        }

        return $candidates;
    }

    private function extractPhotoReference(array $result): ?string
    {
        $photos = $result['photos'] ?? [];

        if (empty($photos)) {
            return null;
        }

        return $photos[0]['photo_reference'] ?? null;
    }

    private function parseGoogleHours(?array $hoursData): array
    {
        if (!$hoursData || !isset($hoursData['periods'])) {
            return [];
        }

        $googleDayOffset = [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 0 => 6];

        $weeklyHours = [];
        foreach ($hoursData['periods'] as $period) {
            $open = $period['open'] ?? [];
            $close = $period['close'] ?? null;

            $googleOpenDay = $open['day'] ?? 0;
            $dayOfWeek = $googleDayOffset[$googleOpenDay] ?? $googleOpenDay;

            $opensAt = isset($open['time']) ? substr($open['time'], 0, 2) . ':' . substr($open['time'], 2, 2) . ':00' : null;

            if ($close) {
                $closesAt = substr($close['time'], 0, 2) . ':' . substr($close['time'], 2, 2) . ':00';
            } else {
                $closesAt = '23:59:00';
            }

            if ($opensAt) {
                $weeklyHours[] = [
                    'day_of_week' => $dayOfWeek,
                    'opens_at' => $opensAt,
                    'closes_at' => $closesAt,
                ];
            }
        }

        return $weeklyHours;
    }

    private function isExcludedType(array $types): bool
    {
        foreach ($types as $type) {
            if (in_array($type, self::EXCLUDED_TYPES, true)) {
                return true;
            }
        }

        return false;
    }
}
