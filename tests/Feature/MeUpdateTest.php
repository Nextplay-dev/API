<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => 'password123',
            'bio' => 'Original bio',
            'picture_profile_url' => 'https://example.com/original.jpg',
        ]);

        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me', [
            'name' => 'Updated Name',
            'bio' => 'Updated bio information',
            'picture_profile_url' => 'https://example.com/updated.jpg',
            'email' => 'hacker@example.com',
        ]);

        $response->assertOk();
        $response->assertJsonPath('name', 'Updated Name');
        $response->assertJsonPath('bio', 'Updated bio information');
        $response->assertJsonPath('picture_profile_url', 'https://example.com/updated.jpg');
        $response->assertJsonPath('email', 'original@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'original@example.com',
            'bio' => 'Updated bio information',
            'picture_profile_url' => 'https://example.com/updated.jpg',
        ]);
    }

    public function test_authenticated_user_must_provide_correct_current_password_to_update_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $responseWrong = $this->putJson('/api/v1/me', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $responseWrong->assertStatus(422);
        $responseWrong->assertJsonValidationErrors('current_password');

        $responseCorrect = $this->putJson('/api/v1/me', [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $responseCorrect->assertOk();

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_social_user_cannot_update_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'social_provider' => 'google',
        ]);

        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me', [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password', 'current_password']);
    }
}
