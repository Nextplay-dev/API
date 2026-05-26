<?php

namespace App\Policies;

use App\Models\BookingGuest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingInvitationPolicy
{
    use HandlesAuthorization;

    public function join(User $user, BookingGuest $guest): bool
    {
        dd($user, $guest);
        return $user->hasPermissionTo('booking.create') && $guest->user_id == $user->id;
    }

    public function decline(User $user, BookingGuest $guest): bool
    {
        return $user->hasPermissionTo('booking.create') && $guest->user_id == $user->id;
    }
}
