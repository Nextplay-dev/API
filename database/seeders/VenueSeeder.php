<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Booking::query()->truncate();
        Tournament::query()->truncate();
        Venue::query()->truncate();

        $user = User::first() ?? User::factory()->create();

        $data = [
            // Bowling
            ['name' => 'Metro Bowling', 'address' => '17 Bd Victor Hugo, 59000 Lille', 'media' => 'https://images.unsplash.com/photo-1544216717-3bbf52512659?w=600', 'category' => 'Category.Bowling', 'game' => 'Bowling', 'latitude' => 50.63297, 'longitude' => 3.05858],
            ['name' => 'Bowling de Lomme', 'address' => 'ZAC du Grand But, 59160 Lomme', 'media' => 'https://images.unsplash.com/photo-1538510065631-7a634969964d?w=600', 'category' => 'Category.Bowling', 'game' => 'Bowling', 'latitude' => 50.6433, 'longitude' => 2.9866],
            ['name' => 'Strike Arena', 'address' => '45 Rue Pelouze, 75008 Paris', 'media' => 'https://images.unsplash.com/photo-1611250188496-e966043a062f?w=600', 'category' => 'Category.Bowling', 'game' => 'Bowling', 'latitude' => 48.8768, 'longitude' => 2.3164],
            ['name' => 'Bowling Stars', 'address' => '12 Allée Vauban, 69006 Lyon', 'media' => 'https://images.unsplash.com/photo-1600171221371-ebd6aa31518f?w=600', 'category' => 'Category.Bowling', 'game' => 'Bowling', 'latitude' => 45.7725, 'longitude' => 4.8624],
            ['name' => 'Golden Pins', 'address' => '8 Rue Nationale, 33000 Bordeaux', 'media' => 'https://images.unsplash.com/photo-1612469032591-638e4a8dae48?w=600', 'category' => 'Category.Bowling', 'game' => 'Bowling', 'latitude' => 44.8398, 'longitude' => -0.5754],

            // Karting
            ['name' => 'Lille Karting', 'address' => 'Rue du Grand But, 59160 Ennetières', 'media' => 'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?w=600', 'category' => 'Category.Karting', 'game' => 'Karting', 'latitude' => 50.6511, 'longitude' => 2.9788],
            ['name' => 'Speed Park', 'address' => '1 Avenue du Grand Angle, 95610 Éragny', 'media' => 'https://images.unsplash.com/photo-1596751303233-0498db7da061?w=600', 'category' => 'Category.Karting', 'game' => 'Karting', 'latitude' => 49.0324, 'longitude' => 2.0911],
            ['name' => 'Circuit Paul Ricard', 'address' => '2760 Route des Hauts du Camp, 83330 Le Castellet', 'media' => 'https://images.unsplash.com/photo-1574515982848-df86716bc5ec?w=600', 'category' => 'Category.Karting', 'game' => 'Karting', 'latitude' => 43.2506, 'longitude' => 5.7916],
            ['name' => 'Karting Indoor Lyon', 'address' => 'Avenue de l\'Industrie, 69960 Corbas', 'media' => 'https://images.unsplash.com/photo-1528652438515-58079df94695?w=600', 'category' => 'Category.Karting', 'game' => 'Karting', 'latitude' => 45.6733, 'longitude' => 4.9125],
            ['name' => 'Rk Karting', 'address' => '9 Rue de l\'Europe, 44240 La Chapelle-sur-Erdre', 'media' => 'https://images.unsplash.com/photo-1510591509121-6a2c2069c9ba?w=600', 'category' => 'Category.Karting', 'game' => 'Karting', 'latitude' => 47.2944, 'longitude' => -1.5472],

            // Tennis
            ['name' => 'Tennis Club Lillois', 'address' => '22 Rue du Mal Assis, 59000 Lille', 'media' => 'https://images.unsplash.com/photo-1595435997911-5619f07a4c2a?w=600', 'category' => 'Category.Tennis', 'game' => 'Tennis', 'latitude' => 50.6388, 'longitude' => 3.0361],
            ['name' => 'Stade Roland Garros', 'address' => '2 Avenue Gordon Bennett, 75016 Paris', 'media' => 'https://images.unsplash.com/photo-1530915534664-4ac6423816bc?w=600', 'category' => 'Category.Tennis', 'game' => 'Tennis', 'latitude' => 48.8472, 'longitude' => 2.2492],
            ['name' => 'Tennis Club de Lyon', 'address' => '3 Boulevard du 11 Novembre, 69100 Villeurbanne', 'media' => 'https://images.unsplash.com/photo-1599586120429-48281b6f0ebb?w=600', 'category' => 'Category.Tennis', 'game' => 'Tennis', 'latitude' => 45.7825, 'longitude' => 4.8622],
            ['name' => 'Garden Tennis', 'address' => '4 Avenue du Tennis, 17200 Royan', 'media' => 'https://images.unsplash.com/photo-1560012057-4372e14c5085?w=600', 'category' => 'Category.Tennis', 'game' => 'Tennis', 'latitude' => 45.6212, 'longitude' => -1.0344],
            ['name' => 'Nice Lawn Tennis Club', 'address' => '5 Rue du Commandant Hughes, 06000 Nice', 'media' => 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600', 'category' => 'Category.Tennis', 'game' => 'Tennis', 'latitude' => 43.7042, 'longitude' => 7.2514],

            // Padel
            ['name' => 'Padel Horizon', 'address' => '33 Route de la Pompadour, 94000 Créteil', 'media' => 'https://images.unsplash.com/photo-1613941209919-48022b938f84?w=600', 'category' => 'Category.Padel', 'game' => 'Padel', 'latitude' => 48.7772, 'longitude' => 2.4533],
            ['name' => 'Lille Padel Club', 'address' => 'Rue du Bourg, 59130 Lambersart', 'media' => 'https://images.unsplash.com/photo-1511225070737-5af5ac9a640b?w=600', 'category' => 'Category.Padel', 'game' => 'Padel', 'latitude' => 50.6555, 'longitude' => 3.0233],
            ['name' => 'The Padel Factory', 'address' => '15 Rue de l\'Octant, 38130 Échirolles', 'media' => 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=600', 'category' => 'Category.Padel', 'game' => 'Padel', 'latitude' => 45.1455, 'longitude' => 5.7194],
            ['name' => 'Padel Arena', 'address' => '2 Rue de la Garenne, 76150 Maromme', 'media' => 'https://images.unsplash.com/photo-1601646761285-65bfa67cd7a3?w=600', 'category' => 'Category.Padel', 'game' => 'Padel', 'latitude' => 49.4833, 'longitude' => 1.0501],
            ['name' => 'Toulouse Padel Club', 'address' => '11 Rue de l\'Égalité, 31200 Toulouse', 'media' => 'https://images.unsplash.com/photo-1593787406536-3676a152d9cb?w=600', 'category' => 'Category.Padel', 'game' => 'Padel', 'latitude' => 43.5933, 'longitude' => 1.4111],

            // Football
            ['name' => 'Urban Soccer Lille', 'address' => '4 Rue de la Performance, 59650 Villeneuve-d\'Ascq', 'media' => 'https://images.unsplash.com/photo-1579952213400-410f968ea1f4?w=600', 'category' => 'Category.Football', 'game' => 'Football', 'latitude' => 50.6122, 'longitude' => 3.1255],
            ['name' => 'Five Paris', 'address' => '21 Rue du Maréchal Leclerc, 75018 Paris', 'media' => 'https://images.unsplash.com/photo-1517926960057-58334484050d?w=600', 'category' => 'Category.Football', 'game' => 'Football', 'latitude' => 48.8955, 'longitude' => 2.3644],
            ['name' => 'Soccer Park Bordeaux', 'address' => '12 Avenue de l\'Aquitaine, 33140 Villenave-d\'Ornon', 'media' => 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=600', 'category' => 'Category.Football', 'game' => 'Football', 'latitude' => 44.7744, 'longitude' => -0.5533],
            ['name' => 'Arena Five Lyon', 'address' => '5 Rue des Frères Lumière, 69680 Chassieu', 'media' => 'https://images.unsplash.com/photo-1560272564-c83b66b1ad12?w=600', 'category' => 'Category.Football', 'game' => 'Football', 'latitude' => 45.7411, 'longitude' => 4.9655],
            ['name' => 'Foot Indoor Marseille', 'address' => '8 Traverse de la Montre, 13011 Marseille', 'media' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600', 'category' => 'Category.Football', 'game' => 'Football', 'latitude' => 43.2922, 'longitude' => 5.4633],

            // Wellness & Golf
            ['name' => 'Maison du Yoga', 'address' => '12 Rue de la Bourse, 59800 Lille', 'media' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600', 'category' => 'Category.Yoga', 'game' => 'Yoga', 'latitude' => 50.6372, 'longitude' => 3.0644],
            ['name' => 'Golf de Brigode', 'address' => '36 Avenue du Golf, 59650 Villeneuve-d\'Ascq', 'media' => 'https://images.unsplash.com/photo-1587174403403-b677df2772f2?w=600', 'category' => 'Category.Golf', 'game' => 'Golf', 'latitude' => 50.6255, 'longitude' => 3.1588],
            ['name' => 'Spa de la Source', 'address' => '78 Route de Valenciennes, 59500 Douai', 'media' => 'https://images.unsplash.com/photo-1540555708071-88b0d3c6156d?w=600', 'category' => 'Category.Wellness', 'game' => 'Spa', 'latitude' => 50.3688, 'longitude' => 3.0805],
            ['name' => 'Laser Game Evolution', 'address' => '4 Rue de l\'Artisanat, 59800 Lille', 'media' => 'https://images.unsplash.com/photo-1598550874175-4d0ef4abc61d?w=600', 'category' => 'Category.Laser Game', 'game' => 'Laser Game', 'latitude' => 50.6277, 'longitude' => 3.0441],
            ['name' => 'Aqua Fitness Centre', 'address' => 'Avenue de la République, 69002 Lyon', 'media' => 'https://images.unsplash.com/photo-1518611509439-f93060998951?w=600', 'category' => 'Category.Fitness', 'game' => 'Fitness', 'latitude' => 45.7661, 'longitude' => 4.8351],
        ];

        foreach ($data as $item) {
            $categoryName = str_replace('Category.', '', $item['category']);
            $category = Category::where('name', $categoryName)->first();

            $venue = Venue::create([
                'name' => $item['name'],
                'address' => $item['address'],
                'media' => $item['media'],
                'category_id' => $category?->id,
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
            ]);

            // Create 1 Tournament for each
            $venue->tournaments()->create([
                'name' => 'Tournoi '.$item['game'].' Open',
                'game' => $item['game'],
                'media' => $item['media'],
                'level' => 'Level.'.collect(['Beginner', 'Intermediate', 'Advanced'])->random(),
                'start_date' => now()->addDays(rand(5, 30))->format('Y-m-d H:i:s'),
                'end_date' => now()->addDays(rand(5, 30))->addHours(3)->format('Y-m-d H:i:s'),
            ], ['host' => true])
                ->bookings()->create([
                'payment' => rand(0, 1),
                'user_id' => $user->id,
            ]);
        }
    }
}
