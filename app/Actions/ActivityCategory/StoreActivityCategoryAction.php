<?php

namespace App\Actions\ActivityCategory;

use App\Http\Requests\StoreActivityCategoryRequest;
use App\Models\ActivityCategory;

class StoreActivityCategoryAction
{
    public function handle(StoreActivityCategoryRequest $request): ActivityCategory
    {
        return ActivityCategory::create($request->validated());
    }
}
