<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $renamed_permissions = [
            'activity.view' => 'venue.view',
            'activity.create' => 'venue.create',
            'activity.update' => 'venue.update',
            'activity.delete' => 'venue.delete',
            'activity.managers.view' => 'venue.managers.view',
            'activity.managers.update' => 'venue.managers.update',
            'activity-category.view' => 'category.view',
            'activity-category.create' => 'category.create',
            'activity-category.update' => 'category.update',
            'activity-category.delete' => 'category.delete',
            'back-office.administration.activities' => 'back-office.administration.venues',
            'my-activity.view' => 'my-venue.view',
            'my-activity.update' => 'my-venue.update',
        ];

        foreach ($renamed_permissions as $old_permission => $new_permission) {
            if (Permission::where('name', $new_permission)->exists()) {
                Permission::where('name', $old_permission)->delete();
            } else {
                Permission::where('name', $old_permission)->update(['name' => $new_permission]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $renamed_permissions = [
            'venue.view' => 'activity.view',
            'venue.create' => 'activity.create',
            'venue.update' => 'activity.update',
            'venue.delete' => 'activity.delete',
            'venue.managers.view' => 'activity.managers.view',
            'venue.managers.update' => 'activity.managers.update',
            'category.view' => 'activity-category.view',
            'category.create' => 'activity-category.create',
            'category.update' => 'activity-category.update',
            'category.delete' => 'activity-category.delete',
            'back-office.administration.venues' => 'back-office.administration.activities',
            'my-venue.view' => 'my-activity.view',
            'my-venue.update' => 'my-activity.update',
        ];

        foreach ($renamed_permissions as $old_permission => $new_permission) {
            if (Permission::where('name', $new_permission)->exists()) {
                Permission::where('name', $old_permission)->delete();
            } else {
                Permission::where('name', $old_permission)->update(['name' => $new_permission]);
            }
        }
    }
};
