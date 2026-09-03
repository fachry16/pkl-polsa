<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_melihat_widget_mengajar_dan_kelas_lms(): void
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

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE TRPL',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TRPL POLSA',
            'status' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TRPL101',
            'nama' => 'Algoritma Pemrograman',
            'sks_teori' => 2,
            'sks_praktikum' => 2,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen Pengajar POLSA',
            'email' => 'dosen_polsa@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '12345678',
            'jabatan' => 'Dosen',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $response = $this->actingAs($userDosen)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Selamat Datang, Dosen Pengajar POLSA');
        $response->assertSee('Dosen Pengajar POLSA');
        $response->assertSee('Kelas Paket Diampu');
        $response->assertSee('Total Mahasiswa Diajar');
        $response->assertSee('Tugas Perlu Dinilai');
        $response->assertSee('Kesiapan RPS Saya');
        $response->assertSee('Ruang Kelas LMS yang Diampu');
        $response->assertSee('Algoritma Pemrograman');
        $response->assertSee('Kelas A (Reguler)');
    }
}