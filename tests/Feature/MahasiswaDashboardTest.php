<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_melihat_dashboard_personal_kpi_dan_katalog_kelas(): void
    {
        $ta = TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum TRPL 2025',
            'tahun_berlaku' => 2025,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum OBE',
            'status' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TRPL201',
            'nama' => 'Pemrograman Web Lanjut',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $userDosen = User::create([
            'name' => 'Bambang Sudarsono, M.Kom',
            'email' => 'bambang@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '0612345678',
            'jabatan' => 'Dosen',
        ]);

        $userMhs = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '32240015',
            'nama' => 'Ahmad Fauzi',
            'program_studi_id' => $prodi->id,
            'angkatan' => 2024,
            'status' => 'Aktif',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu->mahasiswas()->attach($mahasiswa->id);

        $tugas = LmsTugas::create([
            'pengampu_id' => $pengampu->id,
            'judul' => 'Tugas CRUD Laravel & Tailwind',
            'instruksi' => 'Implementasikan REST API',
            'deadline' => now()->addDays(2),
            'bobot_nilai' => 20,
        ]);

        $response = $this->actingAs($userMhs)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Portal Perkuliahan &amp; LMS OBE POLSA', false);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('32240015');
        $response->assertSee('Teknologi Rekayasa Perangkat Lunak');
        $response->assertSee('3 SKS Ditempuh');
        $response->assertSee('Mata Kuliah Diambil');
        $response->assertSee('Tugas Perlu Dikerjakan');
        $response->assertSee('Tugas CRUD Laravel &amp; Tailwind', false);
        $response->assertSee('Pemrograman Web Lanjut');
        $response->assertSee('Masuk Kelas');
    }
}
