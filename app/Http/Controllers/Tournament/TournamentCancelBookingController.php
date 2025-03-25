<?php

namespace App\Http\Controllers\Tournament;

use App\Actions\UserCancelBookingTournamentAction;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentCancelBookingController extends Controller
{
    public function __invoke(Tournament $tournament, Request $request, UserCancelBookingTournamentAction $action): JsonResponse {
        $action->handle($request->user(), $tournament);

        return response()->json([
            'message' => 'Tournament booking cancelled successfully'
        ]);
    }
}
