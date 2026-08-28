<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSesiAbsensi;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsScopingFileTest extends TestCase
{
    use RefreshDatabase;

    private function buatKelas(): array
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
            'semester' => 3,
            'dosen_pengampu' => 'Dosen',
            'status' => 'Disetujui',
        ]);

        RpsPenilaian::create([
            'rps_id' => $rps->id,
            'tugas' => 40,
            'quiz' => 10,
            'uts' => 25,
            'uas' => 25,
            'praktikum' => 0,
            'project' => 0,
        ]);

        $pertemuan = RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Sub CPMK 1',
            'materi' => 'Materi minggu 1',
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Paham',
            'bobot' => '10',
        ]);

        return compact('dosen', 'mahasiswa', 'pengampu', 'pertemuan', 'matakuliah');
    }

    public function test_materi_menolak_pertemuan_dari_rps_lain(): void
    {
        $data = $this->buatKelas();

        $mkLain = MataKuliah::create([
            'kurikulum_id' => $data['matakuliah']->kurikulum_id,
            'kode' => 'TI202',
            'nama' => 'Basis Data',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rpsLain = Rps::create([
            'mata_kuliah_id' => $mkLain->id,
            'semester' => 3,
            'dosen_pengampu' => 'Dosen',
            'status' => 'Disetujui',
        ]);

        $pertemuanLain = RpsPertemuan::create([
            'rps_id' => $rpsLain->id,
            'minggu' => 1,
            'sub_cpmk' => 'Sub CPMK lain',
            'materi' => 'Pertemuan kelas lain',
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Paham',
            'bobot' => '10',
        ]);

        $this->actingAs($data['dosen']->user)
            ->post(route('lms.materi.store', $data['pengampu']->id), [
                'judul' => 'Materi 1',
                'deskripsi' => 'Deskripsi',
                'rps_pertemuan_id' => $pertemuanLain->id,
                'file' => UploadedFile::fake()->create('materi.pdf', 10),
            ])
            ->assertSessionHasErrors('rps_pertemuan_id');

        $this->assertDatabaseMissing('lms_materis', ['judul' => 'Materi 1']);
    }

    public function test_forum_balasan_menolak_parent_dari_kelas_lain(): void
    {
        $data = $this->buatKelas();

        $kelasLain = Pengampu::create([
            'dosen_id' => $data['dosen']->id,
            'mata_kuliah_id' => $data['matakuliah']->id,
            'tahun_akademik_id' => $data['pengampu']->tahun_akademik_id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'B',
        ]);

        $postKelasLain = LmsForumDiskusi::create([
            'pengampu_id' => $kelasLain->id,
            'user_id' => $data['mahasiswa']->user_id,
            'pesan' => 'Diskusi di kelas lain',
        ]);

        $this->actingAs($data['mahasiswa']->user)
            ->post(route('mahasiswa.lms.forum.store', $data['pengampu']->id), [
                'pesan' => 'Nekat balas',
                'parent_id' => $postKelasLain->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->assertDatabaseMissing('lms_forum_diskusis', ['pesan' => 'Nekat balas']);
    }

    public function test_file_materi_hanya_bisa_diakses_dosen_pengampu(): void
    {
        $data = $this->buatKelas();

        Storage::fake('public');

        $materi = LmsMateri::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Materi 1',
            'file_path' => 'lms/materi/rahasia.pdf',
        ]);

        $userDosenLain = User::create([
            'name' => 'Dosen Lain',
            'email' => 'dosenlain@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosenLain = Dosen::create([
            'user_id' => $userDosenLain->id,
            'program_studi_id' => $data['mahasiswa']->program_studi_id,
            'nidn' => '9999',
            'jabatan' => 'Dosen',
        ]);

        Storage::disk('public')->put($materi->file_path, 'pdf-content');

        $this->actingAs($userDosenLain)
            ->get(route('lms.file', ['materi', $materi->id]))
            ->assertForbidden();

        $this->actingAs($data['dosen']->user)
            ->get(route('lms.file', ['materi', $materi->id]))
            ->assertOk();
    }

    public function test_file_jawaban_hanya_bisa_diakses_pemilik(): void
    {
        $data = $this->buatKelas();

        Storage::fake('public');

        $userMhsLain = User::create([
            'name' => 'Mahasiswa Lain',
            'email' => 'mhslain@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswaLain = Mahasiswa::create([
            'user_id' => $userMhsLain->id,
            'nim' => '32240002',
            'nama' => 'Mahasiswa Lain',
            'email' => 'mhslain@test.dev',
            'program_studi_id' => $data['mahasiswa']->program_studi_id,
            'kurikulum_id' => $data['mahasiswa']->kurikulum_id,
            'semester' => 3,
            'angkatan' => 2024,
        ]);

        $data['pengampu']->mahasiswas()->attach($mahasiswaLain->id);

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 40,
        ]);

        $submission = LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'file_jawaban' => 'lms/submissions/jawaban.pdf',
            'dikumpulkan_pada' => now(),
        ]);

        Storage::disk('public')->put($submission->file_jawaban, 'jawaban-content');

        $this->actingAs($userMhsLain)
            ->get(route('mahasiswa.lms.file', ['submission', $submission->id]))
            ->assertForbidden();

        $this->actingAs($data['mahasiswa']->user)
            ->get(route('mahasiswa.lms.file', ['submission', $submission->id]))
            ->assertOk();
    }

    public function test_komponen_nilai_dihapus_saat_dikosongkan(): void
    {
        $data = $this->buatKelas();

        LmsNilaiMahasiswa::create([
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'uts',
            'nilai' => 75,
        ]);

        $this->actingAs($data['dosen']->user)
            ->post(route('lms.tugas.komponen', $data['pengampu']->id), [
                'nilai' => [
                    $data['mahasiswa']->id => [
                        'quiz' => '',
                        'uts' => '',
                        'uas' => '',
                        'praktikum' => '',
                        'project' => '',
                    ],
                ],
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_nilai_mahasiswas', [
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'uts',
        ]);
    }

    public function test_mahasiswa_melihat_tab_nilai_dan_kehadiran(): void
    {
        $data = $this->buatKelas();

        LmsNilaiMahasiswa::create([
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'akhir',
            'nilai' => 85,
        ]);

        LmsSesiAbsensi::create([
            'pengampu_id' => $data['pengampu']->id,
            'rps_pertemuan_id' => $data['pertemuan']->id,
            'tanggal_aktual' => now(),
        ]);

        $response = $this->actingAs($data['mahasiswa']->user)
            ->get(route('mahasiswa.lms.show', $data['pengampu']->id))
            ->assertOk();

        $response->assertSee('Rekap Nilai');
        $response->assertSee('85');
        $response->assertSee('Kehadiran');
        $response->assertSee('Hadir 0 dari 1 sesi');
    }
}
