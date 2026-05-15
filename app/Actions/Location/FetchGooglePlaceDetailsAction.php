<?php

namespace App\Actions\Location;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FetchGooglePlaceDetailsAction
{
    public function handle(string $placeId): array
    {
        $apiKey = config('services.google_maps.key');

        if (! $apiKey) {
            throw new RuntimeException('Google Maps API key is not configured.');
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields' => 'geometry,name,formatted_address',
                'language' => 'fr',
                'key' => $apiKey,
            ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Failed to reach Google Maps.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch place details.');
        }

        $data = $response->json();

        if (($data['status'] ?? null) !== 'OK') {
            throw new RuntimeException($data['error_message'] ?? 'Failed to fetch place details.');
        }

        $geometry = data_get($data, 'result.geometry.location');

        if (! is_array($geometry) || ! array_key_exists('lat', $geometry) || ! array_key_exists('lng', $geometry)) {
            throw new RuntimeException('Place coordinates not available.');
        }

        return [
            'latitude' => $geometry['lat'],
            'longitude' => $geometry['lng'],
            'label' => data_get($data, 'result.formatted_address') ?? data_get($data, 'result.name') ?? 'Selected location',
        ];
    }
}