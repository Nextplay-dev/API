<?php

namespace App\Http\Controllers;

use App\Actions\Activity\DeleteActivityAction;
use App\Actions\Activity\ListActivitiesAction;
use App\Actions\Activity\StoreActivityAction;
use App\Actions\Activity\UpdateActivityAction;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(ListActivitiesAction $action): JsonResponse {
        $activities = $action->handle();

        return ActivityResource::collection($activities)->response();
    }

    public function show(Activity $activity): JsonResponse {
        $this->authorize('view', $activity);
        $activity->load(['tournaments', 'category', 'managers']);

        return ActivityResource::make($activity)->response();
    }

    public function store(StoreActivityRequest $request, StoreActivityAction $action): JsonResponse
    {
        $activity = $action->handle($request);

        return ActivityResource::make($activity)->response()->setStatusCode(201);
    }

    public function update(UpdateActivityRequest $request, Activity $activity, UpdateActivityAction $action): JsonResponse
    {
        $this->authorize('update', $activity);
        $activity = $action->handle($request, $activity);

        return ActivityResource::make($activity)->response();
    }

    public function destroy(Activity $activity, DeleteActivityAction $action): JsonResponse
    {
        $this->authorize('delete', $activity);
        $action->handle($activity);

        return response()->json(null, 204);
    }
}
