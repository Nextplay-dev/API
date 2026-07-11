<?php

namespace App\Services\VenueIngestion;

use App\Models\Category;
use App\Models\Venue;

use App\Services\VenueIngestion\VenueMediaService;

class VenueIngestionPipeline
{
    public function __construct(
        protected GooglePlacesImporterService $googlePlaces,
        protected WebsiteCrawlerService $crawler,
        protected BannerExtractorService $bannerExtractor,
        protected BookingUrlDetectorService $bookingDetector,
        protected AIClassifierService $classifier,
        protected DuplicateResolverService $duplicateResolver,
        protected VenueTypeInferenceService $typeInference,
        protected VenueMediaService $mediaService,
    ) {}

    public function execute(float $lat = 50.6297, float $lon = 3.0573, int $radius = 15000): array
    {
        $candidates = $this->googlePlaces->fetchVenuesAround($lat, $lon, $radius);
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($candidates as $item) {
            $existing = $this->duplicateResolver->findDuplicate($item);

            if ($existing) {
                continue;
            }

            if (empty($item['website'])) {
                continue;
            }

            if ($this->typeInference->isBlacklisted($item)) {
                continue;
            }

            $crawlResult = $this->crawler->crawl($item['website']);
            if (!$crawlResult) {
                continue;
            }

            $crawlerObj = $crawlResult['crawler'];
            $apiKey = config('services.google_maps.key');
            $banner = null;
            if (!empty($item['photo_reference']) && $apiKey) {
                $banner = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photo_reference={$item['photo_reference']}&key={$apiKey}";
            }
            if (!$banner) {
                $banner = $this->bannerExtractor->extract($crawlerObj, $item['website']);
            }

            $classification = $this->classifier->classify(
                $item,
                $crawlResult['title'],
                $crawlResult['description'],
                $crawlResult['headings'],
                $crawlResult['body_text']
            );

            $classification = $this->typeInference->mergeClassification($item, $classification);

            if (!($classification['suitable'] ?? false)) {
                continue;
            }

            $bookingUrl = $this->bookingDetector->detect($crawlerObj, $item['website']) ?? $item['website'];
            $categoryName = $classification['category'] ?? 'Team';
            $activities = $classification['activities'] ?? [];
            $descriptionText = $classification['description'] ?? null;
            if ($descriptionText !== null) {
                $descriptionText = iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $descriptionText);
                $descriptionText = preg_replace('/[^\P{C}\n\r]+/u', '', $descriptionText);
                $descriptionText = mb_substr(trim($descriptionText), 0, 500);
            }

            $category = Category::where('name', $categoryName)->first() ?? Category::where('name', 'Team')->first();

            $venueData = [
                'name' => $item['name'],
                'address' => $item['address'],
                'description' => $descriptionText,
                'media' => $this->mediaService->processAndUpload($banner),
                'website' => $item['website'],
                'phone' => $item['phone'] ?? null,
                'google_place_id' => $item['google_place_id'],
                'google_photo_reference' => $item['photo_reference'] ?? null,
                'category_id' => $category?->id,
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
                'is_virtual' => !empty($bookingUrl),
                'external_booking_url' => $bookingUrl,
            ];

            if ($existing) {
                $existing->update(array_filter($venueData));
                $venue = $existing;
                $updatedCount++;
            } else {
                $venue = Venue::create($venueData);
                $importedCount++;
            }

            $this->saveOpeningHours($venue, $item['opening_hours'] ?? []);

            foreach ($activities as $activityName) {
                $venue->activities()->firstOrCreate(
                    ['name' => $activityName],
                    [
                        'duration_minutes' => 60,
                        'slot_interval_minutes' => 30,
                    ]
                );
            }
        }

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount,
        ];
    }

    private function saveOpeningHours(Venue $venue, array $weeklyHours): void
    {
        $venue->openingHours()->delete();

        if (empty($weeklyHours)) {
            return;
        }

        foreach ($weeklyHours as $slot) {
            $venue->openingHours()->create([
                'day_of_week' => $slot['day_of_week'],
                'opens_at' => $slot['opens_at'],
                'closes_at' => $slot['closes_at'],
            ]);
        }
    }
}