<?php

namespace App\Services;

use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function getFreeRanges(Resource $resource, Carbon $from, Carbon $to): array
    {
        $ranges = $this->getAvailabilityRanges($resource, $from, $to);

        $ranges = $this->subtractExceptions($ranges, $resource, $from, $to);
        $ranges = $this->subtractBookings($ranges, $resource, $from, $to);

        return $ranges;
    }

    protected function getAvailabilityRanges(Resource $resource, Carbon $from, Carbon $to): array
    {
        $ranges = [];
        $current = $from->copy()->startOfDay();

        while ($current <= $to) {

            $availabilities = $resource->availabilities
                ->where('day_of_week', $current->dayOfWeek);

            foreach ($availabilities as $availability) {

                $start = $current->copy()->setTimeFromTimeString($availability->start_time);
                $end = $current->copy()->setTimeFromTimeString($availability->end_time);

                $start = $start->max($from);
                $end = $end->min($to);

                if ($start < $end) {
                    $ranges[] = [
                        'start' => $start,
                        'end' => $end,
                    ];
                }
            }

            $current->addDay();
        }

        return $ranges;
    }

    protected function subtractExceptions(array $ranges, Resource $resource, Carbon $from, Carbon $to): array
    {
        $exceptions = $resource->exceptions()
            ->where('start_at', '<', $to)
            ->where('end_at', '>', $from)
            ->get();

        return $this->subtractPeriods($ranges, $exceptions);
    }

    protected function subtractBookings(array $ranges, Resource $resource, Carbon $from, Carbon $to): array
    {
        $bookings = $resource->bookings()
            ->confirmed()
            ->where('start_at', '<', $to)
            ->where('end_at', '>', $from)
            ->get();

        return $this->subtractPeriods($ranges, $bookings);
    }

    protected function subtractPeriods(array $ranges, Collection $periods): array
    {
        foreach ($periods as $period) {

            $newRanges = [];

            foreach ($ranges as $range) {

                if ($period->end_at <= $range['start'] || $period->start_at >= $range['end']) {
                    $newRanges[] = $range;

                    continue;
                }

                if ($period->start_at > $range['start']) {
                    $newRanges[] = [
                        'start' => $range['start'],
                        'end' => $period->start_at,
                    ];
                }

                if ($period->end_at < $range['end']) {
                    $newRanges[] = [
                        'start' => $period->end_at,
                        'end' => $range['end'],
                    ];
                }
            }

            $ranges = $newRanges;
        }

        return $ranges;
    }
}
