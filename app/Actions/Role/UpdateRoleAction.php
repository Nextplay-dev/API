<?php

namespace App\Actions\Role;

use App\DTOs\RoleDTO;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UpdateRoleAction
{
    public function handle(Role $role, RoleDTO $dto): Role
    {
        if ($role->is_locked) {
            abort(403, 'System roles cannot be modified.');
        }

        return DB::transaction(function () use ($role, $dto) {
            $role->update($dto->toArray());

            if (isset($dto->permissions)) {
                $role->permissions()->sync($dto->permissions);
            }

            return $role;
        });
    }
}
