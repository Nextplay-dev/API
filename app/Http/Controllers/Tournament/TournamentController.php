<?php

namespace App\Http\Controllers\Tournament;

use App\Actions\VenueTournament\ListTournamentsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\VenueTournamentResource;
use App\Models\VenueTournament;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    public function index(ListTournamentsAction $action): JsonResponse {
        $tournaments = $action->handle();

        return VenueTournamentResource::collection($tournaments)->response();
    }

    public function show(VenueTournament $tournament): JsonResponse {
        $tournament->load(['activity', 'venue', 'booking', 'booking.guests']);

        return VenueTournamentResource::make($tournament)->response();
    }
}
