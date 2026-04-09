<?php

namespace App\Http\Controllers;

use App\Actions\ActivityCategory\DeleteActivityCategoryAction;
use App\Actions\ActivityCategory\ListActivityCategoriesAction;
use App\Actions\ActivityCategory\StoreActivityCategoryAction;
use App\Actions\ActivityCategory\UpdateActivityCategoryAction;
use App\Http\Requests\StoreActivityCategoryRequest;
use App\Http\Requests\UpdateActivityCategoryRequest;
use App\Http\Resources\ActivityCategoryResource;
use App\Models\ActivityCategory;
use Illuminate\Http\JsonResponse;

class ActivityCategoryController extends Controller
{
    public function index(ListActivityCategoriesAction $action): JsonResponse
    {
        $categories = $action->handle();

        return ActivityCategoryResource::collection($categories)->response();
    }

    public function show(ActivityCategory $activityCategory): JsonResponse
    {
        return ActivityCategoryResource::make($activityCategory)->response();
    }

    public function store(StoreActivityCategoryRequest $request, StoreActivityCategoryAction $action): JsonResponse
    {
        $category = $action->handle($request);

        return ActivityCategoryResource::make($category)->response()->setStatusCode(201);
    }

    public function update(UpdateActivityCategoryRequest $request, ActivityCategory $activityCategory, UpdateActivityCategoryAction $action): JsonResponse
    {
        $category = $action->handle($request, $activityCategory);

        return ActivityCategoryResource::make($category)->response();
    }

    public function destroy(ActivityCategory $activityCategory, DeleteActivityCategoryAction $action): JsonResponse
    {
        $action->handle($activityCategory);

        return response()->json(null, 204);
    }
}
