<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{

    public function run(): void
    {
        Activity::query()->truncate();
        Tournament::query()->truncate();

        $user = User::first();

        Activity::create([
            'name' => 'Metro Bowling',
            'address' => '17 Bd Victor Hugo, 59000 Lille',
            'media' => 'https://www.apaceloisirs.com/wp-content/uploads/metro-bowling-tarif-reduit-apace-loisirs-1.jpg',
            'category' => 'Category.Bowling'
        ])
            ->tournaments()->create([
                'name' => "Tournoi d'été de Lille",
                'game' => 'Bowling',
                'media' => 'https://images.bowl.com/bowl/media/legacy/uploadedimages/Source/Source_Home/_W9Y6821_308917.JPG',
                'level' => 'Level.Beginner',
                'start_date' => '2025-06-01 18:00:00',
                'end_date' => '2025-06-01 23:59:59',
            ], ['host' => true])
            ->bookings()->create([
                'payment' => true,
                'user_id' => $user->id
            ]);

        Activity::create([
            'name' => 'Lille Karting',
            'address' => 'Ennetières-en-Weppes',
            'media' => 'https://www.lillekarting.fr/wp-content/uploads/2021/09/piste-outdoor.jpg',
            'category' => 'Category.Karting'
            ])->tournaments()->create([
                'name' => 'Hauts-de-France Karting Cup',
                'game' => 'Karting',
                'media' => 'https://www.lillekarting.fr/wp-content/uploads/2021/09/piste-outdoor.jpg',
                'level' => 'Level.Intermediate',
                'start_date' => '2025-07-01 13:00:00',
                'end_date' => '2025-07-01 14:30:00',
            ], ['host' => true])
            ->bookings()->create([
                'payment' => false,
                'user_id' => $user->id
            ]);

        Activity::create([
            'name' => 'Tennis Club Lillois',
            'address' => '22 Rue du Mal Assis, 59000 Lille',
            'media' => 'https://res.cloudinary.com/anybuddy/image/upload/w_1120,h_560,c_fill/c_scale,w_auto/dpr_auto/f_auto,q_auto:eco/v1567521213/tc-lille.jpg',
            'category' => 'Category.Tennis'
            ])->tournaments()->create([
                'name' => 'Lille Tennis Open',
                'game' => 'Tennis',
                'media' => 'https://res.cloudinary.com/anybuddy/image/upload/w_1120,h_560,c_fill/c_scale,w_auto/dpr_auto/f_auto,q_auto:eco/v1567521213/tc-lille.jpg',
                'level' => 'Level.Advanced',
                'start_date' => '2025-08-01 10:00:00',
                'end_date' => '2025-08-01 15:00:00',
            ], ['host' => true])
            ->bookings()->create([
                'payment' => true,
                'user_id' => $user->id
            ]);
    }
}
