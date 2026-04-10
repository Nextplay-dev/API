<?php

namespace App\Http\Controllers;

use App\Actions\Role\CreateRoleAction;
use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\ListRolesAction;
use App\Actions\Role\UpdateRoleAction;
use App\DTOs\RoleDTO;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(ListRolesAction $action): JsonResponse
    {
        $roles = $action->handle();

        return RoleResource::collection($roles)->response();
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');

        return RoleResource::make($role)->response();
    }

    public function store(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        $role = $action->handle(RoleDTO::fromRequest($request));

        $role->load('permissions');

        return RoleResource::make($role)->response()->setStatusCode(201);
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): JsonResponse
    {
        $role = $action->handle($role, RoleDTO::fromRequest($request));

        $role->load('permissions');

        return RoleResource::make($role)->response();
    }

    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $action->handle($role);

        return response()->json(null, 204);
    }
}
