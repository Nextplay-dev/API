<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Resource;
use Carbon\Carbon;

class BookingValidatorService
{
    public function canBook(Resource $resource, Activity $activity, Carbon $start, Carbon $end, int $units = 1): bool
    {
        $strategy = $activity->getRule('overlap_strategy', 'none');
        $buffer = $activity->getRule('buffer_minutes', 0);

        $checkStart = $start->copy();
        $checkEnd = $end->copy()->addMinutes($buffer);

        if ($end <= Carbon::now('UTC')) {
            return false;
        }

        $bookings = $resource->bookings()
            ->confirmed()
            ->overlapping($checkStart, $checkEnd)
            ->get();

        switch ($strategy) {

            case 'none':
                return $bookings->isEmpty();

            case 'capacity':
                $used = $bookings->sum('units');

                return ($used + $units) <= $resource->capacity;

            case 'buffered':
                return $bookings->isEmpty();

            case 'unlimited':
                return true;
        }

        return false;
    }
}
