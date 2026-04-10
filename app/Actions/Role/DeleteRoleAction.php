<?php

namespace App\Actions\Role;

use App\Models\Role;

class DeleteRoleAction
{
    public function handle(Role $role): void
    {
        if ($role->is_locked) {
            abort(403, 'System roles cannot be deleted.');
        }

        $role->delete();
    }
}
