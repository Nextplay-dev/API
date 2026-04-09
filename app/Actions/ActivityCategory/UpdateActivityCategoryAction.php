<?php

namespace App\Actions\ActivityCategory;

use App\Http\Requests\UpdateActivityCategoryRequest;
use App\Models\ActivityCategory;

class UpdateActivityCategoryAction
{
    public function handle(UpdateActivityCategoryRequest $request, ActivityCategory $activityCategory): ActivityCategory
    {
        $activityCategory->update($request->validated());

        return $activityCategory->refresh();
    }
}
