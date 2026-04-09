<?php

namespace App\Http\Controllers;

use App\Actions\Role\ListRolesAction;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(ListRolesAction $action): JsonResponse
    {
        $roles = $action->handle();

        return RoleResource::collection($roles)->response();
    }
}
