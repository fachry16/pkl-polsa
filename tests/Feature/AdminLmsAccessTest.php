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

class AdminLmsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_mengakses_halaman_lms_index_dan_semua_kelas(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $dosenUser = User::create([
            'name' => 'Dosen Test',
            'email' => 'dosen@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '12345678',
            'jabatan' => 'Dosen',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web II',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        $response = $this->actingAs($admin)->get(route('lms.index'));
        $response->assertStatus(200);
        $response->assertSee('Semua Kelas LMS');
        $response->assertSee($mk->nama);

        $showResponse = $this->actingAs($admin)->get(route('lms.show', $pengampu->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($mk->nama);
    }
}
