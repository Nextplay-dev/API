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
        'back-office.administration.venues',
        'back-office.administration.categories',
        'back-office.administration.users',
        'back-office.administration.roles',
        'back-office.administration.bug-reports',

        'venue.view',
        'venue.create',
        'venue.update',
        'venue.managers.view',
        'venue.managers.update',
        'venue.delete',

        'booking.view',
        'booking.create',
        'booking.update',
        'booking.delete',

        'category.view',
        'category.create',
        'category.update',
        'category.delete',

        'activity.view',
        'activity.create',
        'activity.update',
        'activity.delete',

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
        'me.booking.view',
        'me.update',

        'my-venue.view',
        'my-venue.update',

        'bug-report.view',
        'bug-report.create',
        'bug-report.update',
        'bug-report.delete',
    ];

    private const ROLE_PERMISSIONS = [
        'customer' => [
            'permissions' => [
                'venue.view',
                'category.view',
                'tournament.book',
                'tournament.cancel-booking',
                'me.view',
                'me.booking.view',
                'me.update',
                'booking.create',
                'bug-report.create',
            ],
            'weight' => 10,
        ],
        'manager' => [
            'permissions' => [
                'back-office.access',
                'tournament.view',
                'tournament.book',
                'tournament.cancel-booking',
                'venue.view',
                'my-venue.view',
                'my-venue.update',
                'category.view',
                'activity.view',
                'activity.create',
                'activity.update',
                'activity.delete',
                'tournament.create',
                'tournament.update',
                'me.view',
                'me.update',
                'bug-report.create',
            ],
            'weight' => 50,
        ],
        'admin' => [
            'permissions' => '*',
            'weight' => 100,
        ],
    ];

    public function run(): void
    {
        $permissions = collect(self::PERMISSIONS)->map(
            fn (string $name) => Permission::firstOrCreate(['name' => $name])
        );

        foreach (self::ROLE_PERMISSIONS as $roleName => $config) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'is_locked' => true,
                    'weight' => $config['weight']
                ]
            );

            if ($role->weight !== $config['weight']) {
                $role->update(['weight' => $config['weight']]);
            }

            if ($config['permissions'] === '*') {
                $role->permissions()->sync($permissions->pluck('id'));
                continue;
            }

            $permissionIds = $permissions
                ->whereIn('name', $config['permissions'])
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
