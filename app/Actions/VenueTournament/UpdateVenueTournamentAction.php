<?php

namespace App\Actions\VenueTournament;

use App\DTOs\VenueTournamentDTO;
use App\Models\VenueTournament;

class UpdateVenueTournamentAction
{
    public function handle(VenueTournament $venueTournament, VenueTournamentDTO $dto): VenueTournament
    {
        $venueTournament->update($dto->toArray());

        return $venueTournament;
    }
}
