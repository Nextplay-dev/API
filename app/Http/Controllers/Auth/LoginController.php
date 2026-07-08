<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user && $user->social_provider) {
            return response()->json([
                'social_provider' => $user->social_provider,
                'message' => 'Please use social login to log in',
            ], 403);
        }

        $request->authenticate();

        $user = $request->user();

        $token = $user->createToken('auth');

        $user->load(['roles.permissions', 'permissions']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
