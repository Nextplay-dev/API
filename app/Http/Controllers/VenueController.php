<?php

namespace App\Http\Controllers;

use App\Actions\Venue\DeleteVenueAction;
use App\Actions\Venue\ListVenuesAction;
use App\Actions\Venue\StoreVenueAction;
use App\Actions\Venue\UpdateVenueAction;
use App\Http\Requests\StoreVenueRequest;
use App\Http\Requests\UpdateVenueRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class VenueController extends Controller
{
    public function index(ListVenuesAction $action): JsonResponse {
        $venues = $action->handle();

        return VenueResource::collection($venues)->response();
    }

    public function show(Venue $venue): JsonResponse {
        Gate::authorize('view', $venue);
        $venue->load(['tournaments', 'category', 'managers', 'resources', 'activities']);

        return VenueResource::make($venue)->response();
    }

    public function store(StoreVenueRequest $request, StoreVenueAction $action): JsonResponse
    {
        Gate::authorize('create', Venue::class);
        $venue = $action->handle($request);

        return VenueResource::make($venue)->response()->setStatusCode(201);
    }

    public function update(UpdateVenueRequest $request, Venue $venue, UpdateVenueAction $action): JsonResponse
    {
        Gate::authorize('update', $venue);
        $venue = $action->handle($request, $venue);

        return VenueResource::make($venue)->response();
    }

    public function destroy(Venue $venue, DeleteVenueAction $action): JsonResponse
    {
        Gate::authorize('delete', $venue);
        $action->handle($venue);

        return response()->json(null, 204);
    }
}
