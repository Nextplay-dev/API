<?php

namespace App\Actions\Booking;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetJoinedBookingsAction
{
    public function handle(User $user): LengthAwarePaginator
    {
        return QueryBuilder::for($user->joinedBookings())
            ->with('activity', 'resource.venue', 'guests.user', 'user')
            ->allowedFilters([
                AllowedFilter::scope('upcoming'),
            ])
            ->defaultSort('start_at')
            ->paginate(20);
    }
}
