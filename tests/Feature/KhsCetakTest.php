<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KhsCetakTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '110 SKS',
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
            'name' => 'Budi Santoso',
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
            'jenis_kelas' => 'reguler',
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
            'kelas' => 'A',
        ]);

        $mahasiswa->pengampus()->attach([$pengampuWeb->id, $pengampuDb->id]);

        // Simpan nilai akhir di LMS
        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampuWeb->id,
            'mahasiswa_id' => $mahasiswa->id,
            'komponen' => 'akhir',
            'nilai' => 85.00, // Huruf A, Mutu 4.00 -> 4 sks * 4 = 16
        ]);

        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampuDb->id,
            'mahasiswa_id' => $mahasiswa->id,
            'komponen' => 'akhir',
            'nilai' => 76.00, // Huruf B+, Mutu 3.50 -> 3 sks * 3.5 = 10.5
        ]);

        return compact(
            'prodi',
            'tahun',
            'userAdmin',
            'userKaprodi',
            'kaprodi',
            'userDosen',
            'dosen',
            'userMhs',
            'mahasiswa',
            'pengampuWeb',
            'pengampuDb',
            'mkWeb',
            'mkDb',
        );
    }

    public function test_admin_dapat_mengakses_pilih_khs(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userAdmin'])
            ->get(route('khs.cetak-pilih'))
            ->assertOk()
            ->assertSee('Cetak KHS Mahasiswa')
            ->assertSee('Pilih Mahasiswa &amp; Semester KHS', false);
    }

    public function test_admin_dapat_melihat_khs_mahasiswa_dengan_kalkulasi_ips(): void
    {
        $data = $this->buatData();

        // Total SKS: 4 + 3 = 7
        // Poin: 16 + 10.5 = 26.5
        // IPS: 26.5 / 7 = 3.79
        $this->actingAs($data['userAdmin'])
            ->get(route('khs.cetak', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertSee('KARTU HASIL STUDI (KHS)')
            ->assertSee('Budi Santoso')
            ->assertSee('Pemrograman Web')
            ->assertSee('Basis Data')
            ->assertSee('3.79')
            ->assertSee('Dengan Pujian (Cumlaude)');
    }

    public function test_admin_dapat_mengunduh_pdf_khs(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userAdmin'])
            ->get(route('khs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_kaprodi_dapat_melihat_khs_mahasiswa_prodinya(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userKaprodi'])
            ->get(route('khs.cetak', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_kaprodi_ditolak_melihat_khs_prodi_lain(): void
    {
        $data = $this->buatData();

        $prodiLain = ProgramStudi::create([
            'kode_prodi' => 'AK',
            'nama_prodi' => 'Akuntansi',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $mhsLain = Mahasiswa::create([
            'nim' => '32250001',
            'nama' => 'Siti',
            'email' => 'siti@test.dev',
            'program_studi_id' => $prodiLain->id,
            'angkatan' => 2024,
        ]);

        $this->actingAs($data['userKaprodi'])
            ->get(route('khs.cetak', $mhsLain->id))
            ->assertForbidden();
    }

    public function test_mahasiswa_dapat_mengakses_khs_self(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userMhs'])
            ->get(route('khs.self'))
            ->assertOk()
            ->assertSee('KARTU HASIL STUDI (KHS)')
            ->assertSee('Budi Santoso')
            ->assertSee('3.79');
    }

    public function test_mahasiswa_ditolak_melihat_khs_orang_lain(): void
    {
        $data = $this->buatData();

        $userMhs2 = User::create([
            'name' => 'Mhs Lain',
            'email' => 'mhs2@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs2 = Mahasiswa::create([
            'user_id' => $userMhs2->id,
            'nim' => '32240002',
            'nama' => 'Rina',
            'email' => 'mhs2@test.dev',
            'program_studi_id' => $data['prodi']->id,
            'angkatan' => 2024,
        ]);

        // Mhs 1 mencoba akses KHS Mhs 2
        $this->actingAs($data['userMhs'])
            ->get(route('khs.cetak', $mhs2->id))
            ->assertForbidden();
    }
}
