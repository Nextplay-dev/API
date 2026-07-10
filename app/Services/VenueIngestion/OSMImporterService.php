<?php

namespace App\Services\VenueIngestion;

use Illuminate\Support\Facades\Http;

class OSMImporterService
{
    protected string $overpassUrl = 'https://overpass-api.de/api/interpreter';

    public function fetchVenuesAround(float $lat = 50.629250, float $lon = 3.057256, int $radius = 15000): array
    {
        $latStr = number_format($lat, 6, '.', '');
        $lonStr = number_format($lon, 6, '.', '');

        $query = sprintf(
            '[out:json][timeout:30];(node(around:%d,%s,%s)["leisure"~"sports_centre|fitness_centre|bowling_alley|golf_course|swimming_pool|water_park|ice_rink|escape_game|laser_game|miniature_golf|stadium"]["website"];way(around:%d,%s,%s)["leisure"~"sports_centre|fitness_centre|bowling_alley|golf_course|swimming_pool|water_park|ice_rink|escape_game|laser_game|miniature_golf|stadium"]["website"];relation(around:%d,%s,%s)["leisure"~"sports_centre|fitness_centre|bowling_alley|golf_course|swimming_pool|water_park|ice_rink|escape_game|laser_game|miniature_golf|stadium"]["website"];);out center;',
            $radius, $latStr, $lonStr,
            $radius, $latStr, $lonStr,
            $radius, $latStr, $lonStr
        );

        $response = Http::withHeaders([
            'User-Agent' => 'NextPlayIngestionPipeline/1.0',
        ])->asForm()->post($this->overpassUrl, [
            'data' => $query
        ]);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        $elements = $data['elements'] ?? [];
        $venues = [];

        foreach ($elements as $element) {
            $tags = $element['tags'] ?? [];
            $name = $tags['name'] ?? null;

            if (!$name) {
                continue;
            }

            $website = $tags['website'] ?? $tags['contact:website'] ?? $tags['url'] ?? null;
            $latitude = $element['lat'] ?? $element['center']['lat'] ?? null;
            $longitude = $element['lon'] ?? $element['center']['lon'] ?? null;

            if (!$latitude || !$longitude) {
                continue;
            }

            $housenumber = $tags['addr:housenumber'] ?? '';
            $street = $tags['addr:street'] ?? '';
            $postcode = $tags['addr:postcode'] ?? '';
            $city = $tags['addr:city'] ?? 'Lille';

            $address = '';

            if (empty($street)) {
                try {
                    $geoResponse = Http::withHeaders(['User-Agent' => 'NextPlayIngestionPipeline/1.0'])
                        ->timeout(5)
                        ->get("https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}");
                    if ($geoResponse->successful()) {
                        $geoData = $geoResponse->json();
                        $geoAddress = $geoData['address'] ?? [];
                        $road = $geoAddress['road'] ?? $geoAddress['pedestrian'] ?? '';
                        $house = $geoAddress['house_number'] ?? '';
                        $postcode = $geoAddress['postcode'] ?? $postcode;
                        $city = $geoAddress['city'] ?? $geoAddress['town'] ?? $geoAddress['village'] ?? $city;

                        $addressParts = array_filter([
                            trim($house . ' ' . $road),
                            $postcode,
                            $city
                        ]);
                        $address = implode(', ', $addressParts);
                    }
                } catch (\Exception $e) {}
            }

            if (empty($address)) {
                $addressParts = array_filter([$housenumber, $street, $postcode, $city]);
                $address = !empty($addressParts) ? implode(' ', $addressParts) : "Lille, France";
            }

            $osmId = ($element['type'] ?? 'node') . '/' . $element['id'];

            $venues[] = [
                'name' => $name,
                'website' => $website,
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'osm_id' => $osmId,
            ];
        }

        return $venues;
    }
}
