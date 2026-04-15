<?php

namespace App\Actions\Booking;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetMyBookingsAction
{
    public function handle(User $user): LengthAwarePaginator
    {
        return QueryBuilder::for($user->bookings())
            ->with('activity', 'resource.venue')
            ->allowedFilters([
                AllowedFilter::scope('upcoming'),
            ])
            ->defaultSort('start_at')
            ->paginate(20);
    }
}
