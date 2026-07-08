<?php

namespace App\Actions;

use App\Models\User;
use App\Models\VenueTournament;
use Str;

class UserBookTournamentAction
{
    public function handle(User $user, VenueTournament $tournament): void
    {
        if ($tournament->booking_id) {
            $tournament->booking->guests()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'email' => $user->email,
                    'status' => 'accepted',
                    'token' => Str::uuid()->toString(),
                ]
            );
        }
    }
}
