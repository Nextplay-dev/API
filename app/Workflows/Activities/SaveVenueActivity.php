<?php

namespace App\Workflows\Activities;

use App\Models\Category;
use App\Models\Venue;
use App\Models\VenueOpeningHour;
use App\Services\VenueIngestion\DuplicateResolverService;
use Workflow\Activity;

use App\Services\VenueIngestion\VenueMediaService;

class SaveVenueActivity extends Activity
{
    private const DEFAULT_DURATION_MINUTES = 60;

    private const DEFAULT_SLOT_INTERVAL_MINUTES = 30;

    public function execute(DuplicateResolverService $duplicateResolver, VenueMediaService $mediaService, array $payload): array
    {
        $existing = $duplicateResolver->findDuplicate($payload);

        $category = Category::where('name', $payload['category_name'])->first() ?? Category::where('name', 'Team')->first();

        $venueData = [
            'name' => $payload['name'],
            'address' => $payload['address'],
            'description' => $payload['description'],
            'media' => $mediaService->processAndUpload($payload['media']),
            'website' => $payload['website'],
            'phone' => $payload['phone'] ?? null,
            'google_place_id' => $payload['google_place_id'],
            'category_id' => $category?->id,
            'latitude' => $payload['latitude'],
            'longitude' => $payload['longitude'],
            'is_virtual' => true,
            'external_booking_url' => $payload['external_booking_url'],
        ];

        if ($existing) {
            $existing->update(array_filter($venueData));
            $venue = $existing;
            $action = 'updated';
        } else {
            $venue = Venue::create($venueData);
            $action = 'imported';
        }

        $this->saveOpeningHours($venue, $payload['opening_hours'] ?? []);

        $activityNames = array_values(array_filter($payload['activities'] ?? []));
        if (empty($activityNames)) {
            $activityNames = [$payload['category_name'] ?? 'Sport'];
        }

        $activityIds = [];
        foreach ($activityNames as $activityName) {
            $activity = $venue->activities()->firstOrCreate(
                ['name' => $activityName],
                [
                    'duration_minutes' => self::DEFAULT_DURATION_MINUTES,
                    'slot_interval_minutes' => self::DEFAULT_SLOT_INTERVAL_MINUTES,
                ]
            );

            if ($activity->duration_minutes === null || $activity->slot_interval_minutes === null) {
                $activity->update([
                    'duration_minutes' => self::DEFAULT_DURATION_MINUTES,
                    'slot_interval_minutes' => self::DEFAULT_SLOT_INTERVAL_MINUTES,
                ]);
            }

            $activityIds[] = $activity->id;
        }

        $venue->activities()->whereNotIn('id', $activityIds)->delete();

        return [
            'action' => $action,
            'venue_id' => $venue->id,
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
