<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\GetMeStatsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GetMeStatsController extends Controller
{
    public function __invoke(GetMeStatsAction $action): JsonResponse
    {
        $user = Auth::user();

        return response()->json($action->handle($user));
    }
}
