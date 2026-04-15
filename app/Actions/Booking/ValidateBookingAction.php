<?php

namespace App\Actions\Booking;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Resource;
use Carbon\Carbon;

class ValidateBookingAction
{
    public function handle(Resource $resource, Activity $activity, string $startAt, string $endAt, int $units): bool
    {
        $start = Carbon::parse($startAt);
        $end = Carbon::parse($endAt);

        $rules = $activity->rules_json ?? [];
        $strategy = $rules['overlap_strategy'] ?? 'none';

        if ($strategy === 'unlimited') {
            return true;
        }

        $existingBookings = Booking::where('resource_id', $resource->id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_at', [$start, $end->copy()->subSecond()])
                    ->orWhereBetween('end_at', [$start->copy()->addSecond(), $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_at', '<=', $start)
                            ->where('end_at', '>=', $end);
                    });
            })
            ->get();

        switch ($strategy) {
            case 'none':
                return $existingBookings->isEmpty();

            case 'capacity':
                $used = $existingBookings->sum('units');
                return ($used + $units) <= $resource->capacity;

            case 'buffered':
                $buffer = $rules['buffer_minutes'] ?? 0;
                $expandedStart = $start->copy()->subMinutes($buffer);
                $expandedEnd = $end->copy()->addMinutes($buffer);

                // Check if any existing booking overlaps with the buffered range
                $overlapWithBuffer = Booking::where('resource_id', $resource->id)
                    ->where('status', 'confirmed')
                    ->where(function ($query) use ($expandedStart, $expandedEnd) {
                        $query->whereBetween('start_at', [$expandedStart, $expandedEnd->copy()->subSecond()])
                            ->orWhereBetween('end_at', [$expandedStart->copy()->addSecond(), $expandedEnd])
                            ->orWhere(function ($q) use ($expandedStart, $expandedEnd) {
                                $q->where('start_at', '<=', $expandedStart)
                                    ->where('end_at', '>=', $expandedEnd);
                            });
                    })
                    ->exists();

                return !$overlapWithBuffer;

            default:
                return false;
        }
    }
}
