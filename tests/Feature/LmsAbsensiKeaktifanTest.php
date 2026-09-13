<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\LmsSesiAbsensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPenilaian;
use App\Models\RpsPertemuan;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsAbsensiKeaktifanTest extends TestCase
{
    use RefreshDatabase;

    private User $dosenUser;

    private Dosen $dosen;

    private Pengampu $pengampu;

    private Mahasiswa $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);
        $tahun = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $this->dosenUser = User::create([
            'name' => 'Dosen Test',
            'email' => 'dosentest@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $this->dosen = Dosen::create([
            'user_id' => $this->dosenUser->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '12345678',
            'jabatan' => 'Dosen',
        ]);

        $kurikulum = \App\Models\Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_berlaku' => 2026,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 1,
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'kode_rps' => 'RPS-MK101',
            'semester' => 1,
            'dosen_pengampu' => 'Dosen Test',
            'dosen_id' => $this->dosen->id,
            'status' => 'Disetujui',
        ]);

        RpsPenilaian::create([
            'rps_id' => $rps->id,
            'tugas' => 20,
            'quiz' => 10,
            'uts' => 20,
            'uas' => 30,
            'praktikum' => 0,
            'project' => 0,
            'absensi' => 10,
            'keaktifan' => 10,
        ]);

        RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Sub CPMK 1',
            'materi' => 'Pengenalan Web',
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Paham',
            'bobot' => '10',
        ]);

        $this->pengampu = Pengampu::create([
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $userMhs = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhstest@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'program_studi_id' => $prodi->id,
            'nim' => '2026001',
            'nama' => 'Mahasiswa Test',
            'angkatan' => 2026,
            'status' => 'aktif',
        ]);

        $this->pengampu->mahasiswas()->attach($this->mahasiswa->id);
    }

    public function test_dosen_dapat_menghadirkan_semua_mahasiswa_dalam_sesi_presensi()
    {
        $sesi = LmsSesiAbsensi::create([
            'pengampu_id' => $this->pengampu->id,
            'rps_pertemuan_id' => RpsPertemuan::first()->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->dosenUser)
            ->post(route('lms.absensi.hadir-semua', [$this->pengampu->id, $sesi->id]));

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_absensis', [
            'sesi_id' => $sesi->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'status' => 'hadir',
        ]);
    }

    public function test_dosen_dapat_menyimpan_komponen_absensi_dan_keaktifan_di_rekap_nilai()
    {
        $response = $this->actingAs($this->dosenUser)
            ->post(route('lms.tugas.komponen', $this->pengampu->id), [
                'nilai' => [
                    $this->mahasiswa->id => [
                        'absensi' => 90,
                        'keaktifan' => 95,
                    ],
                ],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $this->pengampu->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'komponen' => 'absensi',
            'nilai' => 90,
        ]);

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $this->pengampu->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'komponen' => 'keaktifan',
            'nilai' => 95,
        ]);
    }

    public function test_perhitungan_absensi_menghitung_sakit_dan_izin_setengah_poin()
    {
        $pertemuan2 = RpsPertemuan::create([
            'rps_id' => RpsPertemuan::first()->rps_id,
            'minggu' => 2,
            'sub_cpmk' => 'Sub CPMK 2',
            'materi' => 'Materi Pertemuan 2',
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Paham',
            'bobot' => '10',
        ]);

        $sesi1 = LmsSesiAbsensi::create([
            'pengampu_id' => $this->pengampu->id,
            'rps_pertemuan_id' => RpsPertemuan::first()->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        $sesi2 = LmsSesiAbsensi::create([
            'pengampu_id' => $this->pengampu->id,
            'rps_pertemuan_id' => $pertemuan2->id,
            'tanggal_aktual' => now()->addDay()->toDateString(),
        ]);

        // Sesi 1 = Hadir (1.0 point), Sesi 2 = Sakit (0.5 point) -> Total: 1.5 / 2 = 75%
        \App\Models\LmsAbsensi::create(['sesi_id' => $sesi1->id, 'mahasiswa_id' => $this->mahasiswa->id, 'status' => 'hadir']);
        \App\Models\LmsAbsensi::create(['sesi_id' => $sesi2->id, 'mahasiswa_id' => $this->mahasiswa->id, 'status' => 'sakit']);

        $service = app(\App\Services\PenilaianService::class);
        $score = $service->hitungAbsensi($this->pengampu, $this->mahasiswa);

        $this->assertEquals(75.0, $score);
    }

    public function test_dosen_dapat_mengakses_tab_presensi_dan_export_jurnal()
    {
        $response = $this->actingAs($this->dosenUser)
            ->get(route('lms.show', [$this->pengampu->id, 'tab' => 'presensi']));

        $response->assertOk();
        $response->assertSee('Presensi Sesi Perkuliahan');

        $exportResponse = $this->actingAs($this->dosenUser)
            ->get(route('lms.absensi.export', $this->pengampu->id));

        $exportResponse->assertOk();
        $exportResponse->assertSee('JURNAL &amp; REKAPITULASI PRESENSI PERKULIAHAN', false);
    }
}
