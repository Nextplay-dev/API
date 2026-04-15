<?php

namespace App\Http\Controllers;

use App\Actions\Resource\DeleteResourceAction;
use App\Actions\Resource\StoreResourceAction;
use App\Actions\Resource\UpdateResourceAction;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Http\Resources\ResourceResource;
use App\Models\Resource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ResourceController extends Controller
{
    public function index(Venue $venue): JsonResponse
    {
        Gate::authorize("view", $venue);
        return ResourceResource::collection($venue->resources)->response();
    }

    public function show(Venue $venue, Resource $resource): JsonResponse
    {
        Gate::authorize("view", $venue);
        return ResourceResource::make($resource)->response();
    }

    public function store(StoreResourceRequest $request, Venue $venue, StoreResourceAction $action): JsonResponse
    {
        Gate::authorize('update', $venue);

        $resource = $action->handle($venue, $request->validated());

        return ResourceResource::make($resource)->response()->setStatusCode(201);
    }

    public function update(UpdateResourceRequest $request, Resource $resource, UpdateResourceAction $action): JsonResponse
    {
        Gate::authorize('update', $resource->venue);

        $resource = $action->handle($resource, $request->validated());

        return ResourceResource::make($resource)->response();
    }

    public function destroy(Resource $resource, DeleteResourceAction $action): JsonResponse
    {
        Gate::authorize('update', $resource->venue);

        $action->handle($resource);

        return response()->json(null, 204);
    }
}
