<?php

namespace App\Actions\VenueTournament;

use App\Models\VenueTournament;
use App\Sorts\TournamentNearestSort;
use App\Sorts\TournamentPopularitySort;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListTournamentsAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(VenueTournament::class)
            ->allowedFilters([
                AllowedFilter::exact('venue_id'),
                AllowedFilter::exact('activity_id'),
                AllowedFilter::partial('title'),
                AllowedFilter::callback('category_id', function ($query, $value) {
                    $query->whereHas('venue', function ($q) use ($value) {
                        $q->where('category_id', $value);
                    });
                }),
                AllowedFilter::callback('from_date', function ($query, $value) {
                    $query->whereHas('booking', function ($q) use ($value) {
                        $q->where('start_at', '>=', $value);
                    });
                })
            ])
            ->allowedSorts([
                AllowedSort::custom('nearest', new TournamentNearestSort),
                AllowedSort::custom('popularity', new TournamentPopularitySort),
                'title',
            ])
            ->defaultSort('title')
            ->with(['activity', 'venue', 'booking', 'booking.guests'])
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
