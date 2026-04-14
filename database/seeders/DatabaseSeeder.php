<?php

namespace Database\Seeders;

use App\Models\Venue;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        
        if (Category::count() === 0)
            $this->call([
                CategorySeeder::class,
            ]);

        if (Venue::count() === 0)
            $this->call([
                VenueSeeder::class,
            ]);
    }
}
