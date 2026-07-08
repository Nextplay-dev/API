<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserNotificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PushTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_store_push_token(): void
    {
        $response = $this->postJson('/v1/me/push-token', [
            'push_token' => 'ExponentPushToken[12345]',
            'device_token' => 'apns-device-token-12345',
            'device_type' => 'ios',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_store_push_token(): void
    {
        $user = User::factory()->create();
        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $response = $this->postJson('/v1/me/push-token', [
            'push_token' => 'ExponentPushToken[12345]',
            'device_token' => 'apns-device-token-12345',
            'device_type' => 'ios',
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('user_notification_tokens', [
            'user_id' => $user->id,
            'push_token' => 'ExponentPushToken[12345]',
            'device_token' => 'apns-device-token-12345',
            'device_type' => 'ios',
        ]);
    }

    public function test_user_can_update_existing_push_token_record(): void
    {
        $user = User::factory()->create();
        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $existingToken = UserNotificationToken::create([
            'user_id' => User::factory()->create()->id,
            'push_token' => 'ExponentPushToken[12345]',
            'device_token' => 'apns-device-token-12345',
            'device_type' => 'ios',
        ]);

        $response = $this->postJson('/v1/me/push-token', [
            'push_token' => 'ExponentPushToken[12345]',
            'device_token' => 'apns-device-token-12345',
            'device_type' => 'ios',
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('user_notification_tokens', [
            'id' => $existingToken->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseCount('user_notification_tokens', 1);
    }
}
