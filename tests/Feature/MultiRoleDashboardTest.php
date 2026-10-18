<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_dengan_role_kaprodi_melihat_tab_kaprodi_dan_dosen_di_dashboard(): void
    {
        $ta = TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => '14',
            'nama_prodi' => 'Teknik Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Kaprodi TRPL POLSA',
            'email' => 'kaprodi_trpl@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '88990011',
            'jabatan' => 'Kaprodi',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum TRPL 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'OBE TRPL',
            'status' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TRPL101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 2,
            'sks_praktikum' => 2,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Sebelum switch: dashboard kaprodi terkunci, langsung tampil dashboard dosen
        $response->assertDontSee('Program Studi (14)');
        $response->assertDontSee('Pusat Kendali Program Studi');
        $response->assertSee('Kelas Paket Diampu');

        // Setelah switch: tab kaprodi terbuka & jadi default
        $this->actingAs($user)->post(route('sidebar.toggle', 'kaprodi'));

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Multi-role tab header
        $response->assertSee('Program Studi (14)');
        $response->assertSee('Mengajar (Dosen)');
        // Kaprodi section
        $response->assertSee('Pusat Kendali Program Studi');
        $response->assertSee('Teknik Rekayasa Perangkat Lunak');
        $response->assertSee('Mahasiswa Aktif Prodi');
        $response->assertSee('Dosen Homebase Prodi');
        // Dosen section
        $response->assertSee('Kelas Paket Diampu');
        $response->assertSee('Ruang Kelas LMS yang Diampu');
    }

    public function test_dosen_dengan_role_direktur_melihat_tab_direktur_dan_dosen_di_dashboard(): void
    {
        $ta = TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => '11',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Direktur POLSA',
            'email' => 'direktur@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'direktur'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '77665544',
            'jabatan' => 'Direktur',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Sebelum switch: dashboard direktur terkunci, langsung tampil dashboard dosen
        $response->assertDontSee('Direktur (Eksekutif)');
        $response->assertSee('Kelas Paket Diampu');
        $response->assertDontSee('Dashboard Eksekutif &amp; Tata Kelola', false);
        $response->assertDontSee('Rekapitulasi Program Studi POLSA');

        // Setelah switch: tab direktur terbuka & jadi default
        $this->actingAs($user)->post(route('sidebar.toggle', 'direktur'));

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Direktur (Eksekutif)');
        $response->assertSee('Mengajar (Dosen)');
        // Direktur section
        $response->assertSee('Dashboard Eksekutif &amp; Tata Kelola', false);
        $response->assertSee('Rekapitulasi Program Studi POLSA');
    }

    public function test_halaman_dashboard_direktur_route(): void
    {
        $ta = TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => '15',
            'nama_prodi' => 'Akuntansi',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $user = User::create([
            'name' => 'Direktur Eksekutif',
            'email' => 'direktur_eksekutif@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);

        $response = $this->actingAs($user)->get(route('dashboard-direktur'));

        $response->assertOk();
        // Pastikan tombol navbar switch role muncul di route dashboard-direktur
        $response->assertSee('Direktur (Eksekutif)');
        $response->assertSee('Mengajar (Dosen)');
        $response->assertSee('Dashboard Eksekutif &amp; Tata Kelola', false);
        $response->assertSee('Rekapitulasi Program Studi POLSA');
    }
}
