<?php
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'venue-tournament.view',
            'venue-tournament.create',
            'venue-tournament.update',
            'venue-tournament.delete',
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }
    public function down(): void
    {
        $permissions = [
            'venue-tournament.view',
            'venue-tournament.create',
            'venue-tournament.update',
            'venue-tournament.delete',
        ];
        Permission::whereIn('name', $permissions)->delete();
    }
};
