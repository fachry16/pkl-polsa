<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsSesiAbsensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsAbsensiTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(array $pertemuan = [1, 2]): array
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

        $userDosen = User::create([
            'name' => 'Dosen',
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
            'nama' => 'Mahasiswa',
            'email' => 'mhs@test.dev',
            'program_studi_id' => $prodi->id,
            'kurikulum_id' => $kurikulum->id,
            'semester' => 3,
            'angkatan' => 2024,
        ]);

        $matakuliah = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $matakuliah->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu->mahasiswas()->attach($mahasiswa->id);

        $rps = Rps::create([
            'mata_kuliah_id' => $matakuliah->id,
            'kode_rps' => 'RPS-TI101',
            'semester' => 3,
            'dosen_pengampu' => 'Dosen',
            'status' => 'Disetujui',
        ]);

        $pertemuans = collect($pertemuan)->map(fn ($minggu) => RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => $minggu,
            'sub_cpmk' => "Sub CPMK $minggu",
            'materi' => "Materi minggu $minggu",
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Paham',
            'bobot' => '10',
        ]));

        return compact('userDosen', 'dosen', 'userMhs', 'mahasiswa', 'pengampu', 'rps', 'pertemuans');
    }

    public function test_dosen_dapat_membuka_sesi_dan_menyimpan_presensi(): void
    {
        $data = $this->buatData();
        $pertemuan = $data['pertemuans']->first();

        $this->actingAs($data['userDosen'])
            ->post(route('lms.absensi.buka', $data['pengampu']->id), [
                'rps_pertemuan_id' => $pertemuan->id,
            ]);

        $sesi = LmsSesiAbsensi::where('rps_pertemuan_id', $pertemuan->id)->first();

        $this->assertNotNull($sesi);
        $this->assertEquals(now()->toDateString(), $sesi->tanggal_aktual->format('Y-m-d'));

        $this->actingAs($data['userDosen'])
            ->post(route('lms.absensi.store', [$data['pengampu']->id, $sesi->id]), [
                'status' => [$data['mahasiswa']->id => 'hadir'],
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_absensis', [
            'sesi_id' => $sesi->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'status' => 'hadir',
        ]);

        $this->actingAs($data['userDosen'])
            ->get(route('lms.absensi.index', $data['pengampu']->id))
            ->assertRedirect(route('lms.show', [$data['pengampu']->id, 'tab' => 'presensi']));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.show', [$data['pengampu']->id, 'tab' => 'presensi']))
            ->assertOk()
            ->assertSee('Presensi Kehadiran');

        $this->actingAs($data['userDosen'])
            ->get(route('lms.absensi.show', [$data['pengampu']->id, $sesi->id]))
            ->assertOk()
            ->assertSee($data['mahasiswa']->nama);
    }

    public function test_sesi_tidak_bisa_dibuka_dua_kali(): void
    {
        $data = $this->buatData();
        $pertemuan = $data['pertemuans']->first();

        LmsSesiAbsensi::create([
            'pengampu_id' => $data['pengampu']->id,
            'rps_pertemuan_id' => $pertemuan->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        $this->actingAs($data['userDosen'])
            ->post(route('lms.absensi.buka', $data['pengampu']->id), [
                'rps_pertemuan_id' => $pertemuan->id,
            ])
            ->assertSessionHas('toast_error');

        $this->assertEquals(1, LmsSesiAbsensi::where('rps_pertemuan_id', $pertemuan->id)->count());
    }

    public function test_sesi_tetap_bisa_diedit_meskipun_sesi_berikutnya_dibuka(): void
    {
        $data = $this->buatData();
        [$p1, $p2] = $data['pertemuans']->values();

        $sesi1 = LmsSesiAbsensi::create([
            'pengampu_id' => $data['pengampu']->id,
            'rps_pertemuan_id' => $p1->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        LmsSesiAbsensi::create([
            'pengampu_id' => $data['pengampu']->id,
            'rps_pertemuan_id' => $p2->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        $this->actingAs($data['userDosen'])
            ->post(route('lms.absensi.store', [$data['pengampu']->id, $sesi1->id]), [
                'status' => [$data['mahasiswa']->id => 'hadir'],
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_absensis', [
            'sesi_id' => $sesi1->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'status' => 'hadir',
        ]);
    }

    public function test_sesi_belum_diubah_masih_bisa_diubah_sebelum_sesi_berikutnya(): void
    {
        $data = $this->buatData();
        $p1 = $data['pertemuans']->first();

        $sesi1 = LmsSesiAbsensi::create([
            'pengampu_id' => $data['pengampu']->id,
            'rps_pertemuan_id' => $p1->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        $this->actingAs($data['userDosen'])
            ->post(route('lms.absensi.store', [$data['pengampu']->id, $sesi1->id]), [
                'status' => [$data['mahasiswa']->id => 'sakit'],
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_absensis', [
            'sesi_id' => $sesi1->id,
            'status' => 'sakit',
        ]);
    }

    public function test_dosen_lain_tidak_dapat_mengakses_presensi(): void
    {
        $data = $this->buatData();

        $userLain = User::create([
            'name' => 'Dosen Lain',
            'email' => 'dosen-lain@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        Dosen::create([
            'user_id' => $userLain->id,
            'program_studi_id' => $data['pengampu']->mataKuliah->kurikulum->program_studi_id,
            'nidn' => '1300',
            'jabatan' => 'Dosen',
        ]);

        $this->actingAs($userLain)
            ->get(route('lms.absensi.index', $data['pengampu']->id))
            ->assertForbidden();

        $this->actingAs($userLain)
            ->post(route('lms.absensi.buka', $data['pengampu']->id), [
                'rps_pertemuan_id' => $data['pertemuans']->first()->id,
            ])
            ->assertForbidden();
    }
}
