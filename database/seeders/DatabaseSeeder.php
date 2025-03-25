<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Activity::count() === 0)
            $this->call([
                ActivitySeeder::class,
            ]);
    }
}
