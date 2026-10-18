<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPenilaian;
use App\Models\RpsPertemuan;
use App\Models\RpsTugas;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RpsAjukanKelengkapanTest extends TestCase
{
    use RefreshDatabase;

    private function buatRps(int $jumlahPertemuan, bool $denganTugas, bool $denganPenilaian): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => '11',
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

        $userDosen = User::create([
            'name' => 'Dosen',
            'email' => 'dosen_kelengkapan_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1200'.random_int(10000, 99999),
            'jabatan' => 'Dosen',
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'semester' => 3,
            'dosen_pengampu' => $userDosen->name,
            'status' => 'Draft',
        ]);

        foreach (range(1, $jumlahPertemuan) as $minggu) {
            RpsPertemuan::create([
                'rps_id' => $rps->id,
                'minggu' => $minggu,
                'sub_cpmk' => 'Memahami',
                'materi' => 'Materi minggu '.$minggu,
                'metode' => 'Ceramah',
                'pengalaman_belajar' => 'Diskusi',
                'indikator' => 'Tepat',
                'bobot' => '5',
            ]);
        }

        if ($denganTugas) {
            RpsTugas::create([
                'rps_id' => $rps->id,
                'minggu_topik' => 'Minggu 1',
                'nama_tugas' => 'Tugas 1',
                'kategori_komponen' => 'tugas',
                'sub_cpmk' => 'Memahami',
            ]);
        }

        if ($denganPenilaian) {
            RpsPenilaian::create([
                'rps_id' => $rps->id,
                'tugas' => 40,
                'quiz' => 10,
                'uts' => 25,
                'uas' => 25,
            ]);
        }

        return compact('userDosen', 'rps');
    }

    public function test_ajukan_ditolak_saat_pertemuan_belum_14(): void
    {
        $data = $this->buatRps(Rps::JUMLAH_PERTEMUAN - 1, true, true);

        $this->actingAs($data['userDosen'])
            ->patch(route('rps.ajukan', $data['rps']->id))
            ->assertSessionHas('error');

        $this->assertSame('Draft', $data['rps']->fresh()->status);
    }

    public function test_ajukan_ditolak_saat_tugas_belum_diisi(): void
    {
        $data = $this->buatRps(Rps::JUMLAH_PERTEMUAN, false, true);

        $this->actingAs($data['userDosen'])
            ->patch(route('rps.ajukan', $data['rps']->id))
            ->assertSessionHas('error');

        $this->assertSame('Draft', $data['rps']->fresh()->status);
    }

    public function test_ajukan_ditolak_saat_penilaian_belum_diisi(): void
    {
        $data = $this->buatRps(Rps::JUMLAH_PERTEMUAN, true, false);

        $this->actingAs($data['userDosen'])
            ->patch(route('rps.ajukan', $data['rps']->id))
            ->assertSessionHas('error');

        $this->assertSame('Draft', $data['rps']->fresh()->status);
    }

    public function test_ajukan_berhasil_saat_kelengkapan_penuh(): void
    {
        $data = $this->buatRps(Rps::JUMLAH_PERTEMUAN, true, true);

        $this->actingAs($data['userDosen'])
            ->patch(route('rps.ajukan', $data['rps']->id))
            ->assertSessionHas('success');

        $this->assertSame('Diajukan', $data['rps']->fresh()->status);
    }

    public function test_halaman_rps_tidak_menampilkan_panel_kelengkapan(): void
    {
        $data = $this->buatRps(14, false, false);

        $this->actingAs($data['userDosen'])
            ->get(route('mata-kuliah.rps.index', $data['rps']->mata_kuliah_id))
            ->assertOk()
            ->assertDontSee('Kelengkapan RPS')
            ->assertDontSee('belum diisi minggu')
            ->assertSee('Ajukan ke Kaprodi', false);
    }
}
