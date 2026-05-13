<?php

namespace App\Actions\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetUserNotificationsAction
{
    public function handle(User $user): LengthAwarePaginator
    {
        return QueryBuilder::for(
            Notification::where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
        )
            ->allowedFilters([
                AllowedFilter::exact('mailbox'),
                AllowedFilter::exact('type'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at', 'read_at')
            ->paginate(20);
    }
}
