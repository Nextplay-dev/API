<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function index(Request $request): JsonResponse {
        $request->user()->load(['roles.permissions', 'permissions']);

        return response()->json(UserResource::make($request->user()));
    }
}
