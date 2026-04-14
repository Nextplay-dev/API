<?php

namespace App\Http\Controllers\My;

use App\Actions\Venue\UpdateVenueAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVenueRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
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
            ->with('category')
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());

        return VenueResource::collection($activities)->response();
    }
}
