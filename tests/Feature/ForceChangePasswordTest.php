<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForceChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function createDosenUser(string $nidn = '1234567890', bool $harusGanti = true): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => '11',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D4',
        ]);

        $user = User::create([
            'name' => 'Dosen Test',
            'email' => 'dosen@test.com',
            'password' => Hash::make($nidn),
            'role' => 'dosen',
            'roles' => ['dosen'],
            'harus_ganti_password' => $harusGanti,
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => $nidn,
            'jabatan' => 'Dosen',
        ]);

        return [$user, $dosen];
    }

    public function test_user_with_harus_ganti_password_is_redirected_to_force_change_page(): void
    {
        [$user] = $this->createDosenUser('1234567890', true);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('password.ganti-wajib'));
    }

    public function test_user_with_harus_ganti_password_can_view_force_change_page(): void
    {
        [$user] = $this->createDosenUser('1234567890', true);

        $response = $this->actingAs($user)->get(route('password.ganti-wajib'));

        $response->assertStatus(200);
        $response->assertSee('Perbarui Password Akun Anda');
    }

    public function test_user_cannot_reuse_default_password(): void
    {
        [$user] = $this->createDosenUser('1234567890', true);

        $response = $this->actingAs($user)->post(route('password.ganti-wajib.update'), [
            'password' => '1234567890',
            'password_confirmation' => '1234567890',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertTrue($user->fresh()->harus_ganti_password);
    }

    public function test_user_can_change_password_and_access_dashboard(): void
    {
        [$user] = $this->createDosenUser('1234567890', true);

        $response = $this->actingAs($user)->post(route('password.ganti-wajib.update'), [
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertFalse($user->fresh()->harus_ganti_password);
        $this->assertTrue(Hash::check('passwordBaru123', $user->fresh()->password));

        // Subsequent access should not be redirected
        $dashResponse = $this->actingAs($user->fresh())->get(route('dashboard'));
        $dashResponse->assertStatus(200);
    }

    public function test_admin_is_never_redirected_to_force_change_password(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'roles' => ['admin'],
            'harus_ganti_password' => true, // even if marked, admin is exempt
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_can_reset_non_admin_user_password(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        [$dosenUser] = $this->createDosenUser('1234567890', false);
        $dosenUser->update(['password' => Hash::make('customPassword123')]);

        $response = $this->actingAs($admin)->patch(route('users.reset-password', $dosenUser->id));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $dosenUser->refresh();
        $this->assertTrue($dosenUser->harus_ganti_password);
        $this->assertTrue(Hash::check('1234567890', $dosenUser->password));
    }

    public function test_admin_cannot_reset_another_admin_password(): void
    {
        $admin = User::create([
            'name' => 'Administrator 1',
            'email' => 'admin1@test.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $admin2 = User::create([
            'name' => 'Administrator 2',
            'email' => 'admin2@test.com',
            'password' => Hash::make('admin54321'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $response = $this->actingAs($admin)->patch(route('users.reset-password', $admin2->id));

        $response->assertForbidden();
    }
}
