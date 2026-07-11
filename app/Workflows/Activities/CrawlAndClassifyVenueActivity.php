<?php

namespace App\Workflows\Activities;

use App\Services\VenueIngestion\WebsiteCrawlerService;
use App\Services\VenueIngestion\BannerExtractorService;
use App\Services\VenueIngestion\BookingUrlDetectorService;
use App\Services\VenueIngestion\AIClassifierService;
use App\Services\VenueIngestion\VenueTypeInferenceService;
use Workflow\Activity;

class CrawlAndClassifyVenueActivity extends Activity
{
    public function execute(
        WebsiteCrawlerService $crawler,
        BannerExtractorService $bannerExtractor,
        BookingUrlDetectorService $bookingDetector,
        AIClassifierService $classifier,
        VenueTypeInferenceService $typeInference,
        array $item
    ): array {
        if ($typeInference->isBlacklisted($item)) {
            return ['suitable' => false, 'reject_reason' => 'blacklisted_name', 'name' => $item['name'] ?? 'Unknown', 'google_place_id' => $item['google_place_id'] ?? null];
        }

        if (empty($item['website'])) {
            return ['suitable' => false, 'reject_reason' => 'no_website', 'name' => $item['name'] ?? 'Unknown', 'google_place_id' => $item['google_place_id'] ?? null];
        }

        $crawlResult = $crawler->crawl($item['website']);
        if (!$crawlResult) {
            $hint = $typeInference->infer($item);
            $hintHasMatch = isset($hint['category']) || isset($hint['activities']);
            if ($hint['suitable'] ?? false && $hintHasMatch) {
                return $this->buildEnrichedFromHint($item, $hint, 'website_unreachable');
            }

            return [
                'suitable' => false,
                'reject_reason' => $hint['reject_reason'] ?? 'crawl_failed',
                'name' => $item['name'] ?? 'Unknown',
                'google_place_id' => $item['google_place_id'] ?? null,
            ];
        }

        $crawlerObj = $crawlResult['crawler'];
        $apiKey = config('services.google_maps.key');
        $banner = null;
        if (!empty($item['photo_reference']) && $apiKey) {
            $banner = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photo_reference={$item['photo_reference']}&key={$apiKey}";
        }
        if (!$banner) {
            $banner = $bannerExtractor->extract($crawlerObj, $item['website']);
        }

        $classification = $classifier->classify(
            $item,
            $crawlResult['title'],
            $crawlResult['description'],
            $crawlResult['headings'],
            $crawlResult['body_text']
        );

        $classification = $typeInference->mergeClassification($item, $classification);

        if (!($classification['suitable'] ?? false)) {
            $hint = $typeInference->infer($item);
            $hintHasMatch = isset($hint['category']) || isset($hint['activities']);
            if ($hint['suitable'] ?? false && $hintHasMatch) {
                return $this->buildEnrichedFromHint($item, $hint, 'ai_rejected_but_hint_match');
            }

            return [
                'suitable' => false,
                'reject_reason' => $classification['reject_reason'] ?? 'not_suitable',
                'name' => $item['name'] ?? 'Unknown',
                'google_place_id' => $item['google_place_id'] ?? null,
            ];
        }

        $bookingUrl = $bookingDetector->detect($crawlerObj, $item['website']) ?? $item['website'];
        $categoryName = $classification['category'] ?? 'Team';
        $activities = $classification['activities'] ?? [];

        $descriptionText = $classification['description'] ?? null;
        if ($descriptionText !== null) {
            $descriptionText = iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $descriptionText);
            $descriptionText = preg_replace('/[^\P{C}\n\r]+/u', '', $descriptionText);
            $descriptionText = mb_substr(trim($descriptionText), 0, 500);
        }

        return [
            'name' => $item['name'],
            'address' => $item['address'],
            'media' => $banner,
            'website' => $item['website'],
            'phone' => $item['phone'] ?? null,
            'opening_hours' => $item['opening_hours'] ?? null,
            'google_place_id' => $item['google_place_id'],
            'latitude' => $item['latitude'],
            'longitude' => $item['longitude'],
            'external_booking_url' => $bookingUrl,
            'category_name' => $categoryName,
            'activities' => $activities,
            'description' => $descriptionText,
            'suitable' => true,
        ];
    }

    private function buildEnrichedFromHint(array $item, array $hint, string $bookingFallbackReason): array
    {
        $categoryName = $hint['category'] ?? 'Team';
        $activities = $hint['activities'] ?? ($categoryName !== 'Team' ? [$categoryName] : []);

        return [
            'name' => $item['name'],
            'address' => $item['address'],
            'media' => null,
            'website' => $item['website'],
            'phone' => $item['phone'] ?? null,
            'opening_hours' => $item['opening_hours'] ?? null,
            'google_place_id' => $item['google_place_id'],
            'latitude' => $item['latitude'],
            'longitude' => $item['longitude'],
            'external_booking_url' => $item['website'],
            'category_name' => $categoryName,
            'activities' => $activities,
            'description' => mb_substr($item['name'], 0, 200),
            'suitable' => true,
            'booking_fallback' => $bookingFallbackReason,
        ];
    }
}