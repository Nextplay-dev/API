<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\DeleteMeAction;
use App\Actions\Auth\UpdateMeAction;
use App\DTOs\UpdateMeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->user()->load(['roles.permissions', 'permissions']);

        return response()->json(UserResource::make($request->user()));
    }

    public function update(UpdateMeRequest $request, UpdateMeAction $action): JsonResponse
    {
        $user = $action->handle(
            $request->user(),
            UpdateMeDTO::fromRequest($request)
        );

        $user->load(['roles.permissions', 'permissions']);

        return response()->json(UserResource::make($user));
    }

    public function destroy(Request $request, DeleteMeAction $action): JsonResponse
    {
        $action->handle($request->user());

        return response()->json(null, 204);
    }
}
