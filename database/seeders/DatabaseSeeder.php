<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        
        if (ActivityCategory::count() === 0)
            $this->call([
                ActivityCategorySeeder::class,
            ]);

        if (Activity::count() === 0)
            $this->call([
                ActivitySeeder::class,
            ]);
    }
}
