<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListCategoriesAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->defaultSort('name')
            ->allowedSorts('name', 'id')
            ->paginate(20);
    }
}
