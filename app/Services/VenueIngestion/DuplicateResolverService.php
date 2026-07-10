<?php

namespace App\Services\VenueIngestion;

use App\Models\Venue;

class DuplicateResolverService
{
    public function findDuplicate(array $venueData): ?Venue
    {
        if (!empty($venueData['osm_id'])) {
            $duplicate = Venue::where('osm_id', $venueData['osm_id'])->first();
            if ($duplicate) {
                return $duplicate;
            }
        }

        if (!empty($venueData['website'])) {
            $canonicalWebsite = $this->canonicalizeUrl($venueData['website']);
            $allVenues = Venue::whereNotNull('website')->get();

            foreach ($allVenues as $existing) {
                if ($this->canonicalizeUrl($existing->website) === $canonicalWebsite) {
                    return $existing;
                }
            }
        }

        $allVenues = Venue::whereNotNull('latitude')->whereNotNull('longitude')->get();
        foreach ($allVenues as $existing) {
            $distance = $this->haversineDistance(
                $venueData['latitude'],
                $venueData['longitude'],
                $existing->latitude,
                $existing->longitude
            );

            if ($distance < 200) {
                $existingName = strtolower(trim($existing->name));
                $newName = strtolower(trim($venueData['name']));

                if ($existingName === $newName || levenshtein($existingName, $newName) <= 3 || str_contains($existingName, $newName) || str_contains($newName, $existingName)) {
                    return $existing;
                }
            }
        }

        return null;
    }

    protected function canonicalizeUrl(string $url): string
    {
        $url = strtolower(trim($url));
        $url = preg_replace('#^https?://#', '', $url);
        $url = preg_replace('#^www\.#', '', $url);
        return rtrim($url, '/');
    }

    protected function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
