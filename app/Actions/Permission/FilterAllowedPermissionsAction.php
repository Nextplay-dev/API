<?php

namespace App\Actions\Permission;

use App\Models\User;

class FilterAllowedPermissionsAction
{
    public function handle(User $user, array $requestedPermissionIds): array
    {
        $userPermissionIds = $user->getAllPermissionIds();

        return array_intersect($requestedPermissionIds, $userPermissionIds);
    }
}
