<?php

namespace App\Actions;

use App\Models\Tournament;
use App\Models\User;

class UserCancelBookingTournamentAction
{
    public function handle(User $user, Tournament $tournament): void
    {
        $booking = $tournament
            ->bookings()
            ->where('user_id', $user->id)
            ->first();

        if ($booking)
            $booking->delete();
    }
}
