<?php

namespace App\Sorts;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class VenuePopularitySort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $bookingsCountSubquery = Booking::query()
            ->join('resources', 'bookings.resource_id', '=', 'resources.id')
            ->where('bookings.status', 'confirmed')
            ->groupBy('resources.venue_id')
            ->selectRaw('resources.venue_id, COUNT(*) as bookings_count');

        $bookingsDirection = $descending ? 'ASC' : 'DESC';

        return $query
            ->leftJoinSub($bookingsCountSubquery, 'venue_booking_counts', function ($join) {
                $join->on('venue_booking_counts.venue_id', '=', 'venues.id');
            })
            ->orderByRaw('CASE WHEN COALESCE(venue_booking_counts.bookings_count, 0) = 0 THEN 1 ELSE 0 END ASC')
            ->orderByRaw('COALESCE(venue_booking_counts.bookings_count, 0) '.$bookingsDirection)
            ->orderByRaw('CASE WHEN COALESCE(venue_booking_counts.bookings_count, 0) = 0 THEN RANDOM() ELSE 0 END ASC');
    }
}
