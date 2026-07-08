<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListMyVenueController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $activities = QueryBuilder::for(auth()->user()->managedVenues())
            ->withCount('tournaments')
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts([
                'name',
            ])
            ->defaultSort('name')
            ->with('category', 'resources')
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());

        return VenueResource::collection($activities)->response();
    }
}
