<?php

namespace App\Workflows\Activities;

use App\Services\VenueIngestion\GooglePlacesImporterService;
use Workflow\Activity;

class FetchSingleGooglePlaceActivity extends Activity
{
    public function execute(GooglePlacesImporterService $googlePlaces, string $placeId): ?array
    {
        return $googlePlaces->fetchSinglePlace($placeId);
    }
}
