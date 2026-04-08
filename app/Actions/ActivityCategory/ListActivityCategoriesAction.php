<?php

namespace App\Actions\ActivityCategory;

use App\Models\ActivityCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class ListActivityCategoriesAction
{
    public function handle(): LengthAwarePaginator
    {
        return ActivityCategory::query()
            ->orderBy('name')
            ->paginate(20);
    }
}
