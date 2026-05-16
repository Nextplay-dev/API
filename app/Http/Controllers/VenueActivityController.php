<?php

namespace App\Http\Controllers;

use App\Actions\Activity\CreateActivityAction;
use App\Actions\Activity\DeleteActivityAction;
use App\Actions\Activity\UpdateActivityAction;
use App\DTOs\ActivityDTO;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class VenueActivityController extends Controller
{
    public function index(Venue $venue): JsonResource
    {
        Gate::authorize('viewAny', [Activity::class, $venue]);

        $activities = QueryBuilder::for($venue->activities())
            ->with('resources')
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'duration_minutes', 'slot_interval_minutes'])
            ->get();

        return ActivityResource::collection($activities);
    }

    public function show(Venue $venue, Activity $activity): JsonResource
    {
        Gate::authorize('view', $activity);

        return ActivityResource::make($activity->load('resources'));
    }

    public function store(StoreActivityRequest $request, Venue $venue, CreateActivityAction $action): JsonResponse
    {
        Gate::authorize('create', [Activity::class, $venue]);

        $dto = ActivityDTO::fromRequest($request);
        $activity = $action->handle($venue, $dto);

        return ActivityResource::make($activity)
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateActivityRequest $request, Venue $venue, Activity $activity, UpdateActivityAction $action): JsonResource
    {
        Gate::authorize('update', $activity);

        $dto = ActivityDTO::fromRequest($request);
        $activity = $action->handle($activity, $dto);

        return ActivityResource::make($activity->load('resources'));
    }

    public function destroy(Venue $venue, Activity $activity, DeleteActivityAction $action): JsonResponse
    {
        Gate::authorize('delete', $activity);

        $action->handle($activity);

        return response()->json(null, 204);
    }
}
