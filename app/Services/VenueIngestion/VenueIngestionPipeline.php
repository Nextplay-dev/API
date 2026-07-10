<?php

namespace App\Services\VenueIngestion;

use App\Models\Category;
use App\Models\Venue;

class VenueIngestionPipeline
{
    public function __construct(
        protected OSMImporterService $osmImporter,
        protected WebsiteCrawlerService $crawler,
        protected BannerExtractorService $bannerExtractor,
        protected BookingUrlDetectorService $bookingDetector,
        protected AIClassifierService $classifier,
        protected DuplicateResolverService $duplicateResolver
    ) {}

    public function execute(float $lat = 50.6297, float $lon = 3.0573, int $radius = 15000): array
    {
        $osmVenues = $this->osmImporter->fetchVenuesAround($lat, $lon, $radius);
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($osmVenues as $item) {
            $existing = $this->duplicateResolver->findDuplicate($item);

            $banner = null;
            $bookingUrl = null;
            $categoryName = 'Team';
            $activities = [];

            if (empty($item['website'])) {
                continue;
            }

            $crawlResult = $this->crawler->crawl($item['website']);
            if (!$crawlResult) {
                continue;
            }

            $crawler = $crawlResult['crawler'];
            $banner = $this->bannerExtractor->extract($crawler, $item['website']);
            $bookingUrl = $this->bookingDetector->detect($crawler, $item['website']);

            if (empty($bookingUrl)) {
                continue;
            }

            $classification = $this->classifier->classify(
                $crawlResult['title'],
                $crawlResult['description'],
                $crawlResult['headings'],
                $crawlResult['body_text']
            );

            if (!($classification['suitable'] ?? false)) {
                continue;
            }

            $categoryName = $classification['category'] ?? 'Team';
            $activities = $classification['activities'] ?? [];
            $descriptionText = $classification['description'] ?? null;
            if ($descriptionText !== null) {
                $descriptionText = iconv('UTF-8', 'UTF-8//IGNORE', $descriptionText);
                $descriptionText = mb_substr($descriptionText, 0, 200);
            }

            $category = Category::where('name', $categoryName)->first() ?? Category::where('name', 'Team')->first();

            $venueData = [
                'name' => $item['name'],
                'address' => $item['address'],
                'description' => $descriptionText,
                'media' => $banner ?? 'https://images.unsplash.com/photo-1544216717-3bbf52512659?w=600',
                'website' => $item['website'],
                'osm_id' => $item['osm_id'],
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
}
