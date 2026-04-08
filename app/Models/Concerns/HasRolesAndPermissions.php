<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function assignRole(string ...$roleNames): void
    {
        $roles = Role::whereIn('name', $roleNames)->get();

        $this->roles()->syncWithoutDetaching($roles);
    }

    public function removeRole(string ...$roleNames): void
    {
        $roles = Role::whereIn('name', $roleNames)->get();

        $this->roles()->detach($roles);
    }

    public function grantPermission(string ...$permissionNames): void
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();

        $this->permissions()->syncWithoutDetaching($permissions);
    }

    public function revokePermission(string ...$permissionNames): void
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();

        $this->permissions()->detach($permissions);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(string ...$roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        return $this->hasPermissionThroughRole($permissionName);
    }

    protected function hasPermissionThroughRole(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permissionName))
            ->exists();
    }
}
