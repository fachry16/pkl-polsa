<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this
            ->actingAs($user)
            ->post('/profile/avatar', [
                'avatar' => $file,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_delete_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/dummy.jpg']);
        Storage::disk('public')->put('avatars/dummy.jpg', 'content');

        $response = $this
            ->actingAs($user)
            ->delete('/profile/avatar');

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/dummy.jpg');
    }

    public function test_account_deletion_is_disabled_for_academic_integrity(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile');

        $response->assertRedirect('/profile');
        $this->assertNotNull($user->fresh());
    }
}
