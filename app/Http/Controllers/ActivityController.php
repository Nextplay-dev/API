<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(): JsonResponse {
        $activities = Activity::all()
            ->load('tournaments');

        $activities = ActivityResource::collection($activities);

        return response()->json($activities);
    }

    public function show(Activity $activity): JsonResponse {
        $activity->load('tournaments');

        $activity = ActivityResource::make($activity);

        return response()->json($activity);
    }
}
