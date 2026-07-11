<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $perm = Permission::firstOrCreate(['name' => 'back-office.administration.workflows']);
        
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching([$perm->id]);
        }
    }

    public function down(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->detach(
                Permission::where('name', 'back-office.administration.workflows')->value('id')
            );
        }
        
        Permission::where('name', 'back-office.administration.workflows')->delete();
    }
};
