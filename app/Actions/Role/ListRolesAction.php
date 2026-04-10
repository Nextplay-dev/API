<?php

namespace App\Actions\Role;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListRolesAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('-created_at')
            ->with('permissions')
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
