<?php

namespace App\Actions\ActivityCategory;

use App\Models\ActivityCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListActivityCategoriesAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(ActivityCategory::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->defaultSort('name')
            ->allowedSorts('name', 'id')
            ->paginate(20);
    }
}
