<?php

namespace App\Actions\Location;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SearchGooglePlaceAutocompleteAction
{
    public function handle(string $query): array
    {
        $apiKey = config('services.google_maps.key');

        if (! $apiKey) {
            throw new RuntimeException('Google Maps API key is not configured.');
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                'input' => $query,
                'types' => 'establishment',
                'language' => 'fr',
                'components' => 'country:fr',
                'key' => $apiKey,
            ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Failed to reach Google Maps.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch place suggestions.');
        }

        $data = $response->json();

        if (($data['status'] ?? null) !== 'OK' && ($data['status'] ?? null) !== 'ZERO_RESULTS') {
            throw new RuntimeException($data['error_message'] ?? 'Failed to fetch place suggestions.');
        }

        return collect($data['predictions'] ?? [])
            ->map(fn (array $prediction) => [
                'placeId' => $prediction['place_id'] ?? '',
                'description' => $prediction['description'] ?? '',
                'mainText' => $prediction['structured_formatting']['main_text'] ?? ($prediction['description'] ?? ''),
                'secondaryText' => $prediction['structured_formatting']['secondary_text'] ?? '',
            ])
            ->filter(fn (array $prediction) => $prediction['placeId'] !== '')
            ->values()
            ->all();
    }
}
