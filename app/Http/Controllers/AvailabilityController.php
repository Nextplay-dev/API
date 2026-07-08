<?php

namespace App\Http\Controllers;

use App\Actions\Availability\DeleteAvailabilityAction;
use App\Actions\Availability\StoreAvailabilityAction;
use App\Actions\Availability\UpdateAvailabilityAction;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AvailabilityController extends Controller
{
    public function index(Resource $resource): JsonResponse
    {
        Gate::authorize('view', $resource->venue);

        return AvailabilityResource::collection($resource->availabilities)->response();
    }

    public function store(StoreAvailabilityRequest $request, Resource $resource, StoreAvailabilityAction $action): JsonResponse
    {
        Gate::authorize('update', $resource->venue);

        $availability = $action->handle($resource, $request->validated());

        return AvailabilityResource::make($availability)->response()->setStatusCode(201);
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability, UpdateAvailabilityAction $action): JsonResponse
    {
        Gate::authorize('update', $availability->resource->venue);

        $availability = $action->handle($availability, $request->validated());

        return AvailabilityResource::make($availability)->response();
    }

    public function destroy(Availability $availability, DeleteAvailabilityAction $action): JsonResponse
    {
        Gate::authorize('update', $availability->resource->venue);

        $action->handle($availability);

        return response()->json(null, 204);
    }
}
