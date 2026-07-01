<?php

namespace App\Actions\Venue;

use App\Filters\WeightedVenueSearchFilter;
use App\Models\Venue;
use App\Sorts\NearestSort;
use App\Sorts\VenuePopularitySort;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListVenuesAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(Venue::class)
            ->withCount('tournaments', 'ongoingTournaments')
            ->allowedFilters([
                AllowedFilter::exact('category_id'),
                AllowedFilter::partial('name'),
                AllowedFilter::custom('search', new WeightedVenueSearchFilter()),
            ])
            ->allowedSorts([
                AllowedSort::custom('nearest', new NearestSort),
                AllowedSort::custom('venuePopularity', new VenuePopularitySort),
                'name',
            ])
            ->defaultSort('name')
            ->with(['category', 'managers'])
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
