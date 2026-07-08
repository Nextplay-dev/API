<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_delete_profile(): void
    {
        $response = $this->deleteJson('/v1/me');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_without_permission_cannot_delete_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/v1/me');

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_delete_profile_anonymizing_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'bio' => 'Short bio',
            'picture_profile_url' => 'https://example.com/picture.jpg',
            'social_provider' => 'google',
            'enable_core_notification' => true,
            'enable_commercial_notification' => true,
        ]);

        $permission = Permission::query()->create(['name' => 'me.delete']);
        $user->grantPermission('me.delete');

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/v1/me');

        $response->assertStatus(204);

        $user->refresh();

        $this->assertEquals('Deleted User', $user->name);
        $this->assertEquals('deleted_'.$user->id.'@nextplay.app', $user->email);
        $this->assertNull($user->bio);
        $this->assertNull($user->picture_profile_url);
        $this->assertNull($user->social_provider);
        $this->assertFalse((bool) $user->enable_core_notification);
        $this->assertFalse((bool) $user->enable_commercial_notification);
        $this->assertNull($user->email_verified_at);

        $this->assertCount(0, $user->roles);
        $this->assertCount(0, $user->permissions);
        $this->assertCount(0, $user->tokens);
    }
}
