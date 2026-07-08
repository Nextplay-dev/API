<?php

namespace App\Actions\Role;

use App\Actions\Permission\FilterAllowedPermissionsAction;
use App\DTOs\RoleDTO;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UpdateRoleAction
{
    public function __construct(
        protected FilterAllowedPermissionsAction $filterAllowedPermissionsAction
    ) {}

    public function handle(Role $role, RoleDTO $dto): Role
    {
        $authUser = auth()->user();

        $userMaxWeight = $authUser->getHighestRoleWeight();
        if ($userMaxWeight <= $role->weight || $userMaxWeight <= $dto->weight) {
            abort(403, 'You do not have enough authority to manage this role or set this weight.');
        }

        if ($role->is_locked) {
            abort(403, 'System roles cannot be modified.');
        }

        return DB::transaction(function () use ($role, $dto, $authUser) {
            $role->update($dto->toArray());

            if (isset($dto->permissions)) {
                $userPermissionIds = $authUser->getAllPermissionIds();

                $protectedPermissions = $role->permissions()
                    ->get()
                    ->filter(fn ($p) => ! in_array($p->id, $userPermissionIds))
                    ->pluck('id')
                    ->toArray();

                $allowedPermissionsFromRequest = $this->filterAllowedPermissionsAction->handle(
                    $authUser,
                    $dto->permissions
                );

                $role->permissions()->sync(array_unique(array_merge($protectedPermissions, $allowedPermissionsFromRequest)));
            }

            return $role;
        });
    }
}
