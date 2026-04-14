<?php

namespace App\Http\Controllers;

use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\ListCategoriesAction;
use App\Actions\Category\StoreCategoryAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(ListCategoriesAction $action): JsonResponse
    {
        $categories = $action->handle();

        return CategoryResource::collection($categories)->response();
    }

    public function show(Category $category): JsonResponse
    {
        return CategoryResource::make($category)->response();
    }

    public function store(StoreCategoryRequest $request, StoreCategoryAction $action): JsonResponse
    {
        $category = $action->handle($request);

        return CategoryResource::make($category)->response()->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): JsonResponse
    {
        $category = $action->handle($request, $category);

        return CategoryResource::make($category)->response();
    }

    public function destroy(Category $category, DeleteCategoryAction $action): JsonResponse
    {
        $action->handle($category);

        return response()->json(null, 204);
    }
}
