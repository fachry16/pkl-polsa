<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_melihat_seluruh_widget_monitoring_polsa(): void
    {
        $ta = TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknik Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $admin = User::create([
            'name' => 'Admin POLSA',
            'email' => 'admin_polsa@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Pusat Kendali Akademik POLSA');
        $response->assertSee('Tahun Akademik Aktif');
        $response->assertSee('Total Mahasiswa Rombel');
        $response->assertSee('Total Dosen Pengajar');
        $response->assertSee('Total Kelas Paket Aktif');
        $response->assertSee('Kesiapan RPS Mata Kuliah');
        $response->assertSee('Progres 16 Pertemuan LMS');
        $response->assertSee('Peringatan Rombel Mahasiswa');
        $response->assertSee('Antrean Penilaian Tugas Mahasiswa');
        $response->assertSee('Rekapitulasi Program Studi POLSA');
        $response->assertSee('Sebaran Akun Pengguna Terdaftar POLSA');
        $response->assertSee('Teknik Rekayasa Perangkat Lunak');
    }

    public function test_dosen_melihat_dashboard_dosen(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Pak Dosen',
            'email' => 'dosen@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '12345678',
            'jabatan' => 'Dosen',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Pusat Kendali Akademik POLSA');
        $response->assertSee('Ruang Kelas LMS');
    }
}