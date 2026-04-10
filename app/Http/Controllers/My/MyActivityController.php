<?php

namespace App\Http\Controllers\My;

use App\Actions\Activity\UpdateActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MyActivityController extends Controller
{
    public function index(): JsonResponse
    {
        $activities = QueryBuilder::for(auth()->user()->managedActivities())
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

        return ActivityResource::collection($activities)->response();
    }

    public function show(Activity $activity): JsonResponse
    {
        abort_unless($activity->managers()->where('users.id', auth()->id())->exists(), 403);

        $activity->load(['tournaments', 'category', 'managers']);

        return ActivityResource::make($activity)->response();
    }

    public function update(UpdateActivityRequest $request, Activity $activity, UpdateActivityAction $action): JsonResponse
    {
        abort_unless($activity->managers()->where('users.id', auth()->id())->exists(), 403);

        // We use the standard UpdateActivityAction but it currently doesn't handle managers anyway.
        // The user specifically said managers should not be updateable here.
        $activity = $action->handle($request, $activity);

        return ActivityResource::make($activity)->response();
    }
}
