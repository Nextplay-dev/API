<?php

namespace App\Actions\Booking;

use App\Models\Activity;
use App\Models\Resource;
use App\Models\Slot;
use Carbon\Carbon;

class GenerateSlotsAction
{
    public function handle(Resource $resource, Activity $activity, string $date): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $availabilities = $resource->availabilities()->where('day_of_week', $dayOfWeek)->get();
        $exceptions = $resource->exceptions()
            ->whereDate('start_at', $date)
            ->get();

        $duration = $activity->duration_minutes;
        $interval = $activity->slot_interval_minutes;

        foreach ($availabilities as $availability) {
            $cursor = Carbon::parse($date . ' ' . $availability->start_time);
            $endTime = Carbon::parse($date . ' ' . $availability->end_time);

            while ($cursor->copy()->addMinutes($duration)->lte($endTime)) {
                $start = $cursor->copy();
                $end = $cursor->copy()->addMinutes($duration);

                if (!$this->isOverlappingWithExceptions($start, $end, $exceptions)) {
                    Slot::updateOrCreate(
                        [
                            'resource_id' => $resource->id,
                            'start_at' => $start,
                            'end_at' => $end,
                        ],
                        [
                            'capacity' => $resource->capacity,
                        ]
                    );
                }

                $cursor->addMinutes($interval);
            }
        }
    }

    protected function isOverlappingWithExceptions($start, $end, $exceptions): bool
    {
        foreach ($exceptions as $exception) {
            if ($start->lt($exception->end_at) && $end->gt($exception->start_at)) {
                return true;
            }
        }
        return false;
    }
}
