<?php

namespace App\Workflows\Activities;

use App\Services\VenueIngestion\GooglePlacesImporterService;
use Workflow\Activity;

class FetchGooglePlacesVenuesActivity extends Activity
{
    public function execute(GooglePlacesImporterService $googlePlaces, float $lat, float $lon, int $radius): array
    {
        return $googlePlaces->fetchVenuesAround($lat, $lon, $radius);
    }
}
