<?php

namespace App\Http\Controllers;

use App\Actions\User\DeleteUserAction;
use App\Actions\User\ListUsersAction;
use App\Actions\User\StoreUserAction;
use App\Actions\User\UpdateUserAction;
use App\DTOs\UserDTO;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(ListUsersAction $action): JsonResponse
    {
        $users = $action->handle();

        return UserResource::collection($users)->response();
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'permissions']);

        return UserResource::make($user)->response();
    }

    public function store(StoreUserRequest $request, StoreUserAction $action): JsonResponse
    {
        $user = $action->handle(UserDTO::fromRequest($request));

        $user->load(['roles', 'permissions']);

        return UserResource::make($user)->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): JsonResponse
    {
        $user = $action->handle($user, UserDTO::fromRequest($request));

        $user->load(['roles', 'permissions']);

        return UserResource::make($user)->response();
    }

    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $action->handle($user);

        return response()->json(null, 204);
    }
}
