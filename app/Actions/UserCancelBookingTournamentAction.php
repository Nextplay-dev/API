<?php

namespace App\Actions;

use App\Models\VenueTournament;
use App\Models\User;

class UserCancelBookingTournamentAction
{
    public function handle(User $user, VenueTournament $tournament): void
    {
        if ($tournament->booking_id) {
            $guest = $tournament->booking->guests()
                ->where('user_id', $user->id)
                ->first();

            if ($guest) {
                $guest->delete();
            }
        }
    }
}
