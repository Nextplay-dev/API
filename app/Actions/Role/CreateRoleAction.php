<?php

namespace App\Actions\Role;

use App\DTOs\RoleDTO;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateRoleAction
{
    public function handle(RoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto) {
            $role = Role::create($dto->toArray());

            if (!empty($dto->permissions)) {
                $role->permissions()->sync($dto->permissions);
            }

            return $role;
        });
    }
}
