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

class MyVenueController extends Controller
{
    public function index(): JsonResponse
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

    public function show(Venue $venue): JsonResponse
    {
        abort_unless($venue->managers()->where('users.id', auth()->id())->exists(), 403);

        $venue->load(['tournaments', 'category', 'managers']);

        return VenueResource::make($venue)->response();
    }

    public function update(UpdateVenueRequest $request, Venue $venue, UpdateVenueAction $action): JsonResponse
    {
        abort_unless($venue->managers()->where('users.id', auth()->id())->exists(), 403);

        $venue = $action->handle($request, $venue);

        return VenueResource::make($venue)->response();
    }
}
