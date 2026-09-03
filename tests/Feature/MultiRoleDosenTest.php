<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleDosenTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    private function createProdi(): ProgramStudi
    {
        return ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'akreditasi' => 'Baik',
        ]);
    }

    public function test_admin_dapat_membuat_user_dengan_banyak_role_di_manajemen_user(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Dosen Direktur',
            'email' => 'dosen_direktur@test.dev',
            'password' => 'password123',
            'roles' => ['dosen', 'direktur'],
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'dosen_direktur@test.dev']);

        $user = User::where('email', 'dosen_direktur@test.dev')->first();
        $this->assertTrue($user->isDosen());
        $this->assertTrue($user->isDirektur());
        $this->assertTrue($user->hasRole('dosen'));
        $this->assertTrue($user->hasRole('direktur'));
    }

    public function test_admin_dapat_membuat_dosen_dengan_role_kaprodi_atau_direktur_di_manajemen_dosen(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        $response = $this->actingAs($admin)->post(route('dosen.store'), [
            'name' => 'Pak Direktur Dosen',
            'email' => 'direktur_dosen@test.dev',
            'nidn' => '99887766',
            'program_studi_id' => $prodi->id,
            'roles' => ['dosen', 'direktur'],
        ]);

        $response->assertRedirect(route('dosen.index'));

        $user = User::where('email', 'direktur_dosen@test.dev')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isDosen());
        $this->assertTrue($user->isDirektur());

        $dosen = Dosen::where('nidn', '99887766')->first();
        $this->assertNotNull($dosen);
        $this->assertEquals('Direktur', $dosen->jabatan);
    }

    public function test_admin_dapat_mengubah_role_dosen_di_manajemen_dosen(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        $user = User::create([
            'name' => 'Dosen Biasa',
            'email' => 'dosen_biasa@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '11223344',
            'jabatan' => 'Dosen',
        ]);

        $response = $this->actingAs($admin)->put(route('dosen.update', $dosen->id), [
            'name' => 'Dosen Jadi Kaprodi',
            'email' => 'dosen_biasa@test.dev',
            'nidn' => '11223344',
            'program_studi_id' => $prodi->id,
            'roles' => ['dosen', 'kaprodi'],
        ]);

        $response->assertRedirect(route('dosen.index'));

        $user->refresh();
        $dosen->refresh();

        $this->assertTrue($user->isDosen());
        $this->assertTrue($user->isKaprodi());
        $this->assertEquals('Kaprodi', $dosen->jabatan);
    }

    public function test_dosen_dengan_role_direktur_dapat_mengakses_lms_dan_dashboard_direktur(): void
    {
        $prodi = $this->createProdi();

        $user = User::create([
            'name' => 'Prof Direktur',
            'email' => 'prof_direktur@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'direktur'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '55667788',
            'jabatan' => 'Direktur',
        ]);

        $this->actingAs($user)
            ->get(route('lms.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('dashboard-direktur'))
            ->assertOk();
    }

    public function test_admin_dapat_mengupdate_user_dengan_role_kustom_seperti_kaprodi_trpl(): void
    {
        $admin = $this->createAdmin();
        $prodi = $this->createProdi();

        \App\Models\Role::create([
            'nama' => 'Kaprodi TRPL',
            'kode' => 'kaprodi_trpl',
            'deskripsi' => 'Ketua Program Studi TRPL',
        ]);

        $user = User::create([
            'name' => 'Calon Kaprodi',
            'email' => 'calon_kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '77889900',
            'jabatan' => 'Dosen',
        ]);

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'name' => 'Calon Kaprodi Updated',
            'email' => 'calon_kaprodi@test.dev',
            'roles' => ['dosen', 'kaprodi_trpl'],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $dosen->refresh();

        $this->assertTrue($user->hasRole('kaprodi_trpl'));
        $this->assertTrue($user->isKaprodi());
        $this->assertEquals('Kaprodi TRPL', $dosen->jabatan);
    }

    public function test_dosen_dengan_role_kaprodi_dan_direktur_melihat_menu_krs_dan_dashboard_direktur(): void
    {
        $prodi = $this->createProdi();

        $user = User::create([
            'name' => 'Dosen Kaprodi Direktur',
            'email' => 'all_in_one@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi', 'direktur'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99887766',
            'jabatan' => 'Direktur',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard-direktur'));
        $response->assertOk();
        $response->assertSee(route('krs.index'));
        $response->assertSee(route('dashboard-direktur'));
        $response->assertSee(route('dosen.self'));
        $response->assertSee(route('lms.index'));
    }

    public function test_dosen_dapat_mengakses_riwayat_self(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Dosen Test',
            'email' => 'dosentest@polsa.ac.id',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '0601019001',
            'jabatan' => 'Dosen',
        ]);

        $response = $this->actingAs($user)->get(route('dosen.self.riwayat'));
        $response->assertOk();
        $response->assertSee('Riwayat Mengajar');
        $response->assertSee('Dosen Test');
    }
}