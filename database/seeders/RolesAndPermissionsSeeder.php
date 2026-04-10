<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    private const PERMISSIONS = [
        'back-office.access',
        'back-office.administration.dashboard',
        'back-office.administration.activities',
        'back-office.administration.categories',
        'back-office.administration.users',
        'back-office.administration.roles',

        'activity.view',
        'activity.create',
        'activity.update',
        'activity.managers.view',
        'activity.managers.update',
        'activity.delete',

        'activity-category.view',
        'activity-category.create',
        'activity-category.update',
        'activity-category.delete',

        'tournament.view',
        'tournament.create',
        'tournament.update',
        'tournament.delete',

        'tournament.book',
        'tournament.cancel-booking',

        'user.view',
        'user.create',
        'user.update',
        'user.delete',

        'role.view',
        'role.create',
        'role.update',
        'role.delete',

        'permission.view',

        'me.view',
        'me.update',

        'my-activity.view',
        'my-activity.update',
    ];

    private const ROLE_PERMISSIONS = [
        'customer' => [
            'activity.view',
            'activity-category.view',
            'tournament.book',
            'tournament.cancel-booking',
            'me.view',
        ],
        'manager' => [
            'back-office.access',

            'tournament.view',
            'tournament.book',
            'tournament.cancel-booking',

            'activity.view',
            'my-activity.view',
            'my-activity.update',

            'activity-category.view',

            'tournament.create',
            'tournament.update',
        ],
        'admin' => '*',
    ];

    public function run(): void
    {
        $permissions = collect(self::PERMISSIONS)->map(
            fn (string $name) => Permission::firstOrCreate(['name' => $name])
        );

        foreach (self::ROLE_PERMISSIONS as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['is_locked' => true]
            );

            if (!$role->is_locked) {
                $role->update(['is_locked' => true]);
            }

            if ($rolePermissions === '*') {
                $role->permissions()->sync($permissions->pluck('id'));
                continue;
            }

            $permissionIds = $permissions
                ->whereIn('name', $rolePermissions)
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
