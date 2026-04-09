<?php

namespace App\Actions\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class ListRolesAction
{
    public function handle(): Collection
    {
        return Role::all();
    }
}
