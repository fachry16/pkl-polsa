<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KrsCetakTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TI',
            'status' => 'Aktif',
        ]);

        $tahun = TahunAkademik::create(['tahun' => 2024, 'semester' => 'Ganjil', 'is_active' => true]);

        $userAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $userKaprodi = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1100',
            'jabatan' => 'Kaprodi',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen Pengampu',
            'email' => 'dosen@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1200',
            'jabatan' => 'Dosen',
        ]);

        $userMhs = User::create([
            'name' => 'Mahasiswa',
            'email' => 'mhs@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '32240001',
            'nama' => 'Budi Santoso',
            'email' => 'mhs@test.dev',
            'program_studi_id' => $prodi->id,
            'angkatan' => 2024,
        ]);

        $mkWeb = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 3,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $mkDb = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI102',
            'nama' => 'Basis Data',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $pengampuWeb = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mkWeb->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampuDb = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mkDb->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'B',
        ]);

        $mahasiswa->pengampus()->attach([$pengampuWeb->id, $pengampuDb->id]);

        return compact(
            'prodi',
            'tahun',
            'userAdmin',
            'userKaprodi',
            'kaprodi',
            'userDosen',
            'dosen',
            'mahasiswa',
            'pengampuWeb',
            'pengampuDb',
            'mkWeb',
            'mkDb',
        );
    }

    public function test_admin_dapat_mengakses_halaman_pilih_cetak(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userAdmin'])
            ->get(route('krs.cetak-pilih'))
            ->assertOk()
            ->assertSee('Pilih Mahasiswa');
    }

    public function test_mahasiswa_options_mengembalikan_daftar_mahasiswa_per_prodi(): void
    {
        $data = $this->buatData();

        $response = $this->actingAs($data['userAdmin'])
            ->getJson(route('krs.mahasiswa-options', ['program_studi_id' => $data['prodi']->id]))
            ->assertOk();

        $this->assertCount(1, $response->json());
        $this->assertStringContainsString($data['mahasiswa']->nim, $response->json()[0]['label']);
    }

    public function test_admin_dapat_mencetak_krs_mahasiswa(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userAdmin'])
            ->get(route('krs.cetak', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertSee('KARTU RENCANA STUDI')
            ->assertSee('Pemrograman Web')
            ->assertSee('Basis Data')
            ->assertSee('Budi Santoso');
    }

    public function test_admin_dapat_mengunduh_pdf_krs(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userAdmin'])
            ->get(route('krs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_kaprodi_dapat_mencetak_krs_mahasiswa_prodi_sendiri(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userKaprodi'])
            ->get(route('krs.cetak', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_kaprodi_tidak_dapat_mencetak_krs_mahasiswa_prodi_lain(): void
    {
        $data = $this->buatData();

        $prodiLain = ProgramStudi::create([
            'kode_prodi' => 'AK',
            'nama_prodi' => 'Akuntansi',
            'jenjang' => 'S1',
            'akreditasi' => 'Baik',
        ]);

        $mahasiswaLain = Mahasiswa::create([
            'nim' => '32250001',
            'nama' => 'Siti',
            'email' => 'siti@test.dev',
            'program_studi_id' => $prodiLain->id,
            'angkatan' => 2025,
        ]);

        $this->actingAs($data['userKaprodi'])
            ->get(route('krs.cetak', $mahasiswaLain->id))
            ->assertForbidden();
    }

    public function test_dosen_biasa_tidak_dapat_mencetak_krs(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userDosen'])
            ->get(route('krs.cetak', $data['mahasiswa']->id))
            ->assertForbidden();
    }
}
