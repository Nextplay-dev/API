<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Resource;
use Carbon\Carbon;

class SlotService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BookingValidatorService $bookingValidatorService
    ) {}

    public function computeAvailableSlots(Activity $activity, Carbon $from, Carbon $to): array
    {
        $resources = $activity->resources()->get();
        $slots = [];

        foreach ($resources as $resource) {
            foreach ($this->computeAvailableSlotsForResource($resource, $activity, $from, $to) as $slot) {
                $key = $slot['start_at']->timestamp.'_'.$slot['end_at']->timestamp;

                if (! isset($slots[$key])) {
                    $slots[$key] = $slot;

                    continue;
                }

                $slots[$key]['resources_id'] = array_merge($slots[$key]['resources_id'], $slot['resources_id']);
            }
        }

        array_multisort(array_column($slots, 'start_at'), SORT_ASC, $slots);

        return array_values($slots);
    }

    public function computeAvailableSlotsForResource(Resource $resource, Activity $activity, Carbon $from, Carbon $to): array
    {
        $ranges = $this->availabilityService->getFreeRanges($resource, $from, $to);

        $slots = [];

        foreach ($ranges as $range) {

            $cursor = $range['start']->copy();

            while ($cursor->copy()->addMinutes($activity->duration_minutes) <= $range['end']) {

                $start = $cursor->copy();
                $end = $start->copy()->addMinutes($activity->duration_minutes);

                if ($this->bookingValidatorService->canBook($resource, $activity, $start, $end)) {
                    $slots[] = [
                        'resources_id' => [$resource->id],
                        'start_at' => $start,
                        'end_at' => $end,
                    ];
                }

                $cursor->addMinutes($activity->slot_interval_minutes);
            }
        }

        return $slots;
    }
}
