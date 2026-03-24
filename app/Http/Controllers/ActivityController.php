<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;
use App\Sorts\NearestSort;

class ActivityController extends Controller
{
    public function index(): JsonResponse {
        $activities = QueryBuilder::for(Activity::class)
            ->with('tournaments')
            ->allowedSorts([
                AllowedSort::custom('nearest', new NearestSort),
                'name',
                'category',
            ])
            ->defaultSort('name')
            ->paginate(20);

        ActivityResource::collection($activities);

        return response()->json($activities);
    }

    public function show(Activity $activity): JsonResponse {
        $activity->load('tournaments');

        $activity = ActivityResource::make($activity);

        return response()->json($activity);
    }
}
