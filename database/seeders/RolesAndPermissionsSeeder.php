<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    private const PERMISSIONS = [
        'activity.create',
        'activity.update',
        'activity.delete',

        'activity-category.create',
        'activity-category.update',
        'activity-category.delete',

        'tournament.create',
        'tournament.update',
        'tournament.delete',

        'tournament.book',
        'tournament.cancel-booking',

        'user.view',
        'user.update',
        'user.delete',
    ];

    private const ROLE_PERMISSIONS = [
        'customer' => [
            'tournament.book',
            'tournament.cancel-booking',
        ],
        'manager' => [
            'tournament.book',
            'tournament.cancel-booking',

            'activity.create',
            'activity.update',

            'activity-category.create',
            'activity-category.update',

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
            $role = Role::firstOrCreate(['name' => $roleName]);

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
