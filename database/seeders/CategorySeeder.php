<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private const CATEGORIES = [
        ['name' => 'Padel', 'icon' => 'MaterialCommunityIcons/tennis', 'color' => '#7C3AED'],
        ['name' => 'Tennis', 'icon' => 'Ionicons/tennisball', 'color' => '#10B981'],
        ['name' => 'Football', 'icon' => 'MaterialCommunityIcons/soccer', 'color' => '#2563EB'],
        ['name' => 'Basketball', 'icon' => 'Ionicons/basketball', 'color' => '#EA580C'],
        ['name' => 'Volleyball', 'icon' => 'MaterialCommunityIcons/volleyball', 'color' => '#14B8A6'],
        ['name' => 'Bowling', 'icon' => 'MaterialCommunityIcons/bowling', 'color' => '#A855F7'],
        ['name' => 'Wellness', 'icon' => 'Ionicons/leaf', 'color' => '#22C55E'],
        ['name' => 'Swimming', 'icon' => 'MaterialCommunityIcons/swim', 'color' => '#06B6D4'],
        ['name' => 'Golf', 'icon' => 'Ionicons/golf', 'color' => '#84CC16'],
        ['name' => 'Badminton', 'icon' => 'MaterialCommunityIcons/badminton', 'color' => '#F97316'],
        ['name' => 'Table Tennis', 'icon' => 'MaterialCommunityIcons/table-tennis', 'color' => '#0EA5E9'],
        ['name' => 'Running', 'icon' => 'MaterialCommunityIcons/run', 'color' => '#EF4444'],
        ['name' => 'Cycling', 'icon' => 'MaterialCommunityIcons/bike', 'color' => '#22D3EE'],
        ['name' => 'Yoga', 'icon' => 'MaterialCommunityIcons/yoga', 'color' => '#8B5CF6'],
        ['name' => 'Boxing', 'icon' => 'MaterialCommunityIcons/boxing-glove', 'color' => '#DC2626'],
        ['name' => 'Fitness', 'icon' => 'MaterialCommunityIcons/dumbbell', 'color' => '#F59E0B'],
        ['name' => 'Climbing', 'icon' => 'MaterialCommunityIcons/karate', 'color' => '#EAB308'],
        ['name' => 'Hockey', 'icon' => 'MaterialCommunityIcons/hockey-sticks', 'color' => '#1D4ED8'],
        ['name' => 'Rugby', 'icon' => 'MaterialCommunityIcons/rugby', 'color' => '#059669'],
        ['name' => 'Handball', 'icon' => 'MaterialCommunityIcons/handball', 'color' => '#D946EF'],
        ['name' => 'Karting', 'icon' => 'MaterialCommunityIcons/car-speed-limiter', 'color' => '#334155'],
        ['name' => 'Laser Game', 'icon' => 'MaterialCommunityIcons/ray-start', 'color' => '#BE123C'],
        ['name' => 'Team', 'icon' => 'Ionicons/people-outline', 'color' => '#6366F1'],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['icon' => $category['icon'], 'color' => $category['color']]
            );
        }
    }
}
