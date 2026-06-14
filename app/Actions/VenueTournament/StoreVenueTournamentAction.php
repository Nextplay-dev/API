<?php
namespace App\Actions\VenueTournament;
use App\DTOs\VenueTournamentDTO;
use App\Models\VenueTournament;
class StoreVenueTournamentAction
{
    public function handle(VenueTournamentDTO $dto): VenueTournament
    {
        return VenueTournament::create($dto->toArray());
    }
}