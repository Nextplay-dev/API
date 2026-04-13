<?php

namespace App\Actions\Role;

use App\Models\Role;
use App\Models\User;

class FilterAllowedRolesAction
{
    public function handle(User $user, array $requestedRoleNames): array
    {
        return Role::whereIn('name', $requestedRoleNames)
            ->with('permissions')
            ->get()
            ->filter(fn (Role $role) => $this->isRoleGiveable($user, $role))
            ->pluck('name')
            ->toArray();
    }

    public function isRoleGiveable(User $user, Role $role): bool
    {
        if ($user->getHighestRoleWeight() <= $role->weight) {
            return false;
        }

        $userPermissionIds = $user->getAllPermissionIds();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return empty(array_diff($rolePermissionIds, $userPermissionIds));
    }
}
