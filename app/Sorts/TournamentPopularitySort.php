<?php

namespace App\Sorts;

use App\Models\BookingGuest;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class TournamentPopularitySort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $guestsCountSubquery = BookingGuest::query()
            ->groupBy('booking_id')
            ->selectRaw('booking_id, COUNT(*) as guests_count');

        $direction = $descending ? 'ASC' : 'DESC';

        return $query
            ->leftJoinSub($guestsCountSubquery, 'tournament_guest_counts', function ($join) {
                $join->on('tournament_guest_counts.booking_id', '=', 'venue_tournaments.booking_id');
            })
            ->orderByRaw('COALESCE(tournament_guest_counts.guests_count, 0) ' . $direction);
    }
}
