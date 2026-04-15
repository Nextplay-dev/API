<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\Activity;
use Carbon\Carbon;

class SlotService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BookingValidatorService $bookingValidatorService
    ) {}

    public function computeAvailableSlots(Resource $resource, Activity $activity, Carbon $from, Carbon $to): array
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
                        'resource_id' => $resource->id,
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