<?php

namespace App\Actions;

use App\Models\Tournament;
use App\Models\User;

class UserBookTournamentAction
{
    public function handle(User $user, Tournament $tournament): void
    {
        $tournament->bookings()->firstOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'payment' => false,
                'user_id' => $user->id
            ]
        );
    }
}
