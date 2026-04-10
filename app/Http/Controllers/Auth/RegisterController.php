<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterAction;
use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $user = $action->handle(UserDTO::fromRequest($request));

        $user->load(['roles.permissions', 'permissions']);

        $token = $user->createToken('auth');

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
