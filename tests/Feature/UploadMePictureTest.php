<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadMePictureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_profile_picture_to_r2(): void
    {
        Storage::fake('r2');

        $user = User::factory()->create();
        Permission::query()->create(['name' => 'me.update']);
        $user->grantPermission('me.update');
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/v1/me/picture', [
            'picture' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url']);

        $filename = Storage::disk('r2')->allFiles('profiles')[0];
        Storage::disk('r2')->assertExists($filename);
    }
}
