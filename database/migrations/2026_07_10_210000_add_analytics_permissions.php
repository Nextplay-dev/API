<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'analytics.view',
            'back-office.administration.analytics',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }

    public function down(): void
    {
        $permissions = [
            'analytics.view',
            'back-office.administration.analytics',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
