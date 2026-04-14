<?php

namespace App\Actions\Venue;

use App\Models\Venue;
use App\Sorts\NearestSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListVenuesAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(Venue::class)
            ->withCount('tournaments')
            ->allowedFilters([
                AllowedFilter::exact('category_id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts([
                AllowedSort::custom('nearest', new NearestSort),
                'name',
            ])
            ->defaultSort('name')
            ->with(['category', 'managers'])
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
