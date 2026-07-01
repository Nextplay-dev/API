<?php

namespace App\Http\Controllers\Tournament;

use App\Actions\UserBookTournamentAction;
use App\Http\Controllers\Controller;
use App\Models\VenueTournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentBookController extends Controller
{
    public function __invoke(VenueTournament $tournament, Request $request, UserBookTournamentAction $action): JsonResponse {
        $action->handle($request->user(), $tournament);

        return response()->json([
            'message' => 'Tournament booked successfully'
        ]);
    }
}
