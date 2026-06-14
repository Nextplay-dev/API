<?php
namespace App\Actions\VenueTournament;
use App\Models\VenueTournament;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
class ListVenueTournamentsAction
{
    public function handle(int $venueId): LengthAwarePaginator
    {
        return QueryBuilder::for(VenueTournament::class)
            ->where('venue_id', $venueId)
            ->allowedFilters([
                AllowedFilter::partial('title'),
            ])
            ->defaultSort('-created_at')
            ->with(['activity'])
            ->paginate(request()->integer('per_page', 20))
            ->appends(request()->query());
    }
}
