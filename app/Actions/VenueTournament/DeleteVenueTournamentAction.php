<?php

namespace App\Actions\VenueTournament;

use App\Models\VenueTournament;

class DeleteVenueTournamentAction
{
    public function handle(VenueTournament $venueTournament): void
    {
        $venueTournament->delete();
    }
}
