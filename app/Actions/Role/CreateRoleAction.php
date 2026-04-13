<?php

namespace App\Actions\Role;

use App\Actions\Permission\FilterAllowedPermissionsAction;
use App\DTOs\RoleDTO;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateRoleAction
{
    public function __construct(
        protected FilterAllowedPermissionsAction $filterAllowedPermissionsAction
    ) {}

    public function handle(RoleDTO $dto): Role
    {
        $authUser = auth()->user();
        if ($authUser->getHighestRoleWeight() <= $dto->weight) {
            abort(403, 'You cannot create a role with a weight equal to or higher than your own.');
        }

        return DB::transaction(function () use ($dto) {
            $role = Role::create($dto->toArray());

            if ($dto->permissions !== null) {
                $permissionsToSync = $this->filterAllowedPermissionsAction->handle(
                    auth()->user(),
                    $dto->permissions
                );

                $role->permissions()->sync($permissionsToSync);
            }

            return $role;
        });
    }
}
