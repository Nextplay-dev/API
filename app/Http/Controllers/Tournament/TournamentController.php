<?php

namespace App\Http\Controllers\Tournament;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    public function index(): JsonResponse {
        $tournaments = Tournament::all()
            ->load('activities');

        $tournaments = TournamentResource::collection($tournaments);

        return response()->json($tournaments);
    }

    public function show(Tournament $tournament): JsonResponse {
        $tournament->load('activities', 'bookings', 'bookings.user');

        $tournament = TournamentResource::make($tournament);

        return response()->json($tournament);
    }
}
