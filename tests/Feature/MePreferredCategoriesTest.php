<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MePreferredCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_five_preferred_categories_using_booking_counts_and_random_fillers(): void
    {
        $user = User::factory()->create();
        Permission::query()->create(['name' => 'me.view']);
        $user->grantPermission('me.view');
        Sanctum::actingAs($user);

        $bookedPrimaryCategory = Category::query()->create([
            'name' => 'Booked Primary',
            'icon' => 'Ionicons/trophy-outline',
            'color' => '#111111',
        ]);

        $bookedSecondaryCategory = Category::query()->create([
            'name' => 'Booked Secondary',
            'icon' => 'Ionicons/trophy-outline',
            'color' => '#222222',
        ]);

        $otherCategories = collect(range(1, 4))->map(function (int $index) {
            return Category::query()->create([
                'name' => 'Random Category ' . $index,
                'icon' => 'Ionicons/trophy-outline',
                'color' => '#333333',
            ]);
        });

        $bookedPrimaryVenue = Venue::query()->create([
            'name' => 'Primary Venue',
            'address' => 'Primary Address',
            'category_id' => $bookedPrimaryCategory->id,
            'media' => null,
            'latitude' => null,
            'longitude' => null,
        ]);

        $bookedSecondaryVenue = Venue::query()->create([
            'name' => 'Secondary Venue',
            'address' => 'Secondary Address',
            'category_id' => $bookedSecondaryCategory->id,
            'media' => null,
            'latitude' => null,
            'longitude' => null,
        ]);

        $bookedPrimaryResource = Resource::query()->create([
            'venue_id' => $bookedPrimaryVenue->id,
            'name' => 'Primary Resource',
            'type' => 'court',
            'capacity' => 4,
        ]);

        $bookedSecondaryResource = Resource::query()->create([
            'venue_id' => $bookedSecondaryVenue->id,
            'name' => 'Secondary Resource',
            'type' => 'court',
            'capacity' => 4,
        ]);

        $primaryActivity = Activity::query()->create([
            'name' => 'Primary Activity',
            'duration_minutes' => 60,
            'slot_interval_minutes' => 30,
            'rules_json' => null,
            'venue_id' => $bookedPrimaryVenue->id,
        ]);

        $secondaryActivity = Activity::query()->create([
            'name' => 'Secondary Activity',
            'duration_minutes' => 60,
            'slot_interval_minutes' => 30,
            'rules_json' => null,
            'venue_id' => $bookedSecondaryVenue->id,
        ]);

        Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $bookedPrimaryResource->id,
            'activity_id' => $primaryActivity->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $bookedPrimaryResource->id,
            'activity_id' => $primaryActivity->id,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addHour(),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $bookedSecondaryResource->id,
            'activity_id' => $secondaryActivity->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        $response = $this->getJson('/api/v1/me/preffered-categories');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $response->assertJsonPath('data.0.id', $bookedPrimaryCategory->id);
        $response->assertJsonPath('data.1.id', $bookedSecondaryCategory->id);

        $returnedIds = collect($response->json('data'))->pluck('id');

        $this->assertCount(5, $returnedIds->unique()->all());
        $this->assertTrue($returnedIds->contains($bookedPrimaryCategory->id));
        $this->assertTrue($returnedIds->contains($bookedSecondaryCategory->id));
        $this->assertCount(4, $otherCategories);
    }
}