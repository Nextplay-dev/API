<?php

namespace App\Actions\BugReport;

use App\Models\BugReport;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListBugReportsAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(BugReport::class)
            ->allowedFilters([
                AllowedFilter::partial('message'),
            ])
            ->defaultSort('-created_at')
            ->with('user')
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
