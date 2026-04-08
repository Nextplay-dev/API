<?php

namespace App\Actions\ActivityCategory;

use App\Models\ActivityCategory;

class DeleteActivityCategoryAction
{
    public function handle(ActivityCategory $activityCategory): void
    {
        $activityCategory->delete();
    }
}
