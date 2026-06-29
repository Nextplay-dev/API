<?php
namespace App\Http\Controllers;
use App\Actions\VenueTournament\DeleteVenueTournamentAction;
use App\Actions\VenueTournament\ListVenueTournamentsAction;
use App\Actions\VenueTournament\StoreVenueTournamentAction;
use App\Actions\VenueTournament\UpdateVenueTournamentAction;
use App\DTOs\VenueTournamentDTO;
use App\Http\Requests\StoreVenueTournamentRequest;
use App\Http\Requests\UpdateVenueTournamentRequest;
use App\Http\Resources\VenueTournamentResource;
use App\Models\Venue;
use App\Models\VenueTournament;
use Illuminate\Http\JsonResponse;
class VenueTournamentController extends Controller
{
    public function index(Venue $venue, ListVenueTournamentsAction $action): JsonResponse
    {
        $tournaments = $action->handle($venue->id);
        return VenueTournamentResource::collection($tournaments)->response();
    }
    public function store(StoreVenueTournamentRequest $request, Venue $venue, StoreVenueTournamentAction $action): JsonResponse
    {
        $tournament = $action->handle(VenueTournamentDTO::fromRequest($request, $venue->id));
        $tournament->load(['activity', 'booking', 'venue']);
        return VenueTournamentResource::make($tournament)->response()->setStatusCode(201);
    }
    public function show(VenueTournament $venueTournament): JsonResponse
    {
        $venueTournament->load(['activity', 'booking', 'venue']);
        return VenueTournamentResource::make($venueTournament)->response();
    }
    public function update(UpdateVenueTournamentRequest $request, VenueTournament $venueTournament, UpdateVenueTournamentAction $action): JsonResponse
    {
        $tournament = $action->handle($venueTournament, VenueTournamentDTO::fromRequest($request));
        $tournament->load(['activity', 'booking', 'venue']);
        return VenueTournamentResource::make($tournament)->response();
    }
    public function destroy(VenueTournament $venueTournament, DeleteVenueTournamentAction $action): JsonResponse
    {
        $action->handle($venueTournament);
        return response()->json(null, 204);
    }
}
