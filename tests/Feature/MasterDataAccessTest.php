<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    private function createDirektur(): User
    {
        return User::create([
            'name' => 'Direktur User',
            'email' => 'direktur_test@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);
    }

    private function createKaprodi(): User
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Kaprodi User',
            'email' => 'kaprodi_test@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99001122',
            'jabatan' => 'Kaprodi',
        ]);

        return $user;
    }

    private function createDosenBiasa(): User
    {
        $prodi = ProgramStudi::first() ?? ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Dosen Biasa',
            'email' => 'dosen_biasa@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99003344',
            'jabatan' => 'Dosen',
        ]);

        return $user;
    }

    public function test_admin_dapat_mengakses_semua_master_data(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->get(route('tahun-akademik.index'))->assertOk();
        $this->actingAs($admin)->get(route('program-studi.index'))->assertOk();
        $this->actingAs($admin)->get(route('dosen.index'))->assertOk();
        $this->actingAs($admin)->get(route('mahasiswa.index'))->assertOk();
        $this->actingAs($admin)->get(route('pengampu.index'))->assertOk();
        $this->actingAs($admin)->get(route('krs.index'))->assertOk();
    }

    public function test_direktur_ditolak_mengakses_master_data_dan_krs(): void
    {
        $direktur = $this->createDirektur();

        $this->actingAs($direktur)->get(route('tahun-akademik.index'))->assertForbidden();
        $this->actingAs($direktur)->get(route('program-studi.index'))->assertForbidden();
        $this->actingAs($direktur)->get(route('dosen.index'))->assertForbidden();
        $this->actingAs($direktur)->get(route('mahasiswa.index'))->assertForbidden();
        $this->actingAs($direktur)->get(route('pengampu.index'))->assertForbidden();
        $this->actingAs($direktur)->get(route('krs.index'))->assertForbidden();
    }

    public function test_kaprodi_ditolak_mengakses_master_data_umum(): void
    {
        $kaprodi = $this->createKaprodi();

        $this->actingAs($kaprodi)->get(route('tahun-akademik.index'))->assertForbidden();
        $this->actingAs($kaprodi)->get(route('program-studi.index'))->assertForbidden();
        $this->actingAs($kaprodi)->get(route('dosen.index'))->assertForbidden();
        $this->actingAs($kaprodi)->get(route('mahasiswa.index'))->assertForbidden();
        $this->actingAs($kaprodi)->get(route('pengampu.index'))->assertForbidden();
    }

    public function test_kaprodi_masih_dapat_mengakses_krs_prodinya(): void
    {
        $kaprodi = $this->createKaprodi();

        $this->actingAs($kaprodi)->get(route('krs.index'))->assertOk();
    }

    public function test_dosen_biasa_ditolak_mengakses_master_data_dan_krs(): void
    {
        $dosen = $this->createDosenBiasa();

        $this->actingAs($dosen)->get(route('tahun-akademik.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('program-studi.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('dosen.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('mahasiswa.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('pengampu.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('krs.index'))->assertForbidden();
    }
}