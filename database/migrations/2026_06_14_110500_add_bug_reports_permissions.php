<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'bug-report.view',
            'bug-report.create',
            'bug-report.update',
            'bug-report.delete',
            'back-office.administration.bug-reports',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }

    public function down(): void
    {
        $permissions = [
            'bug-report.view',
            'bug-report.create',
            'bug-report.update',
            'bug-report.delete',
            'back-office.administration.bug-reports',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
