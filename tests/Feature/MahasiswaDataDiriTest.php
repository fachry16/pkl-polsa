<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaDataDiriTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_dapat_mengakses_halaman_data_diri_sendiri(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $user = User::create([
            'name' => 'Ahmad Mahasiswa',
            'email' => '2024001@polsa.ac.id',
            'password' => 'password',
            'role' => 'mahasiswa',
            'roles' => ['mahasiswa'],
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '2024001',
            'nama' => 'Ahmad Mahasiswa',
            'program_studi_id' => $prodi->id,
            'angkatan' => '2024',
            'jenis_kelas' => 'Reguler',
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.self'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Mahasiswa');
        $response->assertSee('2024001');
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Reguler');
        $response->assertSee('Data Diri');
    }

    public function test_user_tanpa_mahasiswa_diarahkan_ke_dashboard(): void
    {
        $user = User::create([
            'name' => 'User Biasa',
            'email' => 'user@polsa.ac.id',
            'password' => 'password',
            'role' => 'mahasiswa',
            'roles' => ['mahasiswa'],
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.self'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Data mahasiswa tidak ditemukan.');
    }
}
