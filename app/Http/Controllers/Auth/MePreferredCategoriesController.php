<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Category\GetPreferredCategoriesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MePreferredCategoriesController extends Controller
{
    public function index(GetPreferredCategoriesAction $getPreferredCategoriesAction): JsonResponse
    {
        $categories = $getPreferredCategoriesAction->handle(Auth::user());

        return CategoryResource::collection($categories)->response();
    }
}