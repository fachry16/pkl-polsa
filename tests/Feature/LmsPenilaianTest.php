<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPenilaian;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\PenilaianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsPenilaianTest extends TestCase
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

        return compact('dosen', 'mahasiswa', 'pengampu');
    }

    public function test_hitung_nilai_tugas_tertimbang(): void
    {
        $data = $this->buatKelas();

        $tugas1 = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 40,
        ]);

        $tugas2 = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 2',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(14),
            'bobot_nilai' => 60,
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas1->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 80,
            'dikumpulkan_pada' => now(),
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas2->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 100,
            'dikumpulkan_pada' => now(),
        ]);

        $service = app(PenilaianService::class);

        $this->assertEquals(92.0, $service->hitungTugas($data['pengampu'], $data['mahasiswa']));
    }

    public function test_sync_tidak_mengubah_bobot_rps(): void
    {
        $data = $this->buatKelas();

        $pengampu = $data['pengampu'];
        $rps = Rps::where('mata_kuliah_id', $pengampu->mata_kuliah_id)->first();
        $bobotTugas = $rps->penilaian->tugas;

        $tugas = LmsTugas::create([
            'pengampu_id' => $pengampu->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 100,
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 85,
            'dikumpulkan_pada' => now(),
        ]);

        $this->actingAs($data['dosen']->user)
            ->post(route('lms.tugas.sync', $pengampu->id))
            ->assertSessionHas('toast_success');

        $this->assertEquals($bobotTugas, $rps->penilaian->fresh()->tugas);

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $pengampu->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'tugas',
            'nilai' => 85.00,
        ]);
    }

    public function test_penilaian_submission_menyimpan_nilai_akhir(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 100,
        ]);

        $submission = LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'dikumpulkan_pada' => now(),
        ]);

        $this->actingAs($data['dosen']->user)
            ->patch(route('lms.submission.nilai', $submission->id), ['nilai' => 80])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'tugas',
            'nilai' => 80.00,
        ]);
    }

    public function test_nilai_akhir_dinormalisasi_saat_komponen_belum_lengkap(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 100,
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 80,
            'dikumpulkan_pada' => now(),
        ]);

        $service = app(PenilaianService::class);

        $this->assertEquals(80.0, $service->hitungTugas($data['pengampu'], $data['mahasiswa']));
        $this->assertEquals(80.0, $service->hitungNilaiAkhir($data['pengampu'], $data['mahasiswa']));

        $service->simpanNilaiKelas($data['pengampu']);

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'akhir',
            'nilai' => 80.00,
        ]);
    }

    public function test_dosen_menyimpan_nilai_komponen_uts_uas_quiz(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 40,
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 80,
            'dikumpulkan_pada' => now(),
        ]);

        $this->actingAs($data['dosen']->user)
            ->post(route('lms.tugas.komponen', $data['pengampu']->id), [
                'nilai' => [
                    $data['mahasiswa']->id => [
                        'quiz' => 100,
                        'uts' => 60,
                        'uas' => 80,
                        'praktikum' => '',
                        'project' => '',
                    ],
                ],
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'uts',
            'nilai' => 60.00,
        ]);

        $this->assertDatabaseHas('lms_nilai_mahasiswas', [
            'pengampu_id' => $data['pengampu']->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'komponen' => 'akhir',
            'nilai' => 77.00,
        ]);
    }

    public function test_mahasiswa_tidak_bisa_mengumpulkan_ulang_setelah_dinilai(): void
    {
        $data = $this->buatKelas();

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
            'catatan_mahasiswa' => 'Versi awal',
            'dikumpulkan_pada' => now(),
        ]);

        $submission->update(['nilai' => 85]);

        $this->actingAs($data['mahasiswa']->user)
            ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                'catatan_mahasiswa' => 'Nekat timpa',
            ])
            ->assertForbidden();

        $this->assertEquals('Versi awal', $submission->fresh()->catatan_mahasiswa);
    }

    public function test_mahasiswa_tidak_bisa_mengumpulkan_pertama_setelah_deadline(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->subDay(),
            'bobot_nilai' => 40,
        ]);

        $this->actingAs($data['mahasiswa']->user)
            ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                'catatan_mahasiswa' => 'Nekat kumpul',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('lms_submissions', ['lms_tugas_id' => $tugas->id]);
    }

    public function test_dosen_memperbarui_tugas(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas Lama',
            'instruksi' => 'Instruksi lama',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 40,
        ]);

        $this->actingAs($data['dosen']->user)
            ->patch(route('lms.tugas.update', [$data['pengampu']->id, $tugas->id]), [
                'judul' => 'Tugas Baru',
                'instruksi' => 'Instruksi baru',
                'deadline' => now()->addDays(14),
                'bobot_nilai' => 60,
            ])
            ->assertSessionHas('toast_success');

        $tugas->refresh();

        $this->assertEquals('Tugas Baru', $tugas->judul);
        $this->assertEquals(60, $tugas->bobot_nilai);
    }

    public function test_dosen_menghapus_tugas_beserta_submissions(): void
    {
        $data = $this->buatKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 40,
        ]);

        LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'nilai' => 85,
            'dikumpulkan_pada' => now(),
        ]);

        $this->actingAs($data['dosen']->user)
            ->delete(route('lms.tugas.destroy', ['pengampu' => $data['pengampu']->id, 'tugas' => $tugas->id]))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_tugas', ['id' => $tugas->id]);
        $this->assertDatabaseMissing('lms_submissions', ['lms_tugas_id' => $tugas->id]);
    }
}
