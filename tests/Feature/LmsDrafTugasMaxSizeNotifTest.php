<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsTugas;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Notifications\DrafTugasRpsBaru;
use App\Notifications\TugasBaru;
use App\Services\GoogleDriveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LmsDrafTugasMaxSizeNotifTest extends TestCase
{
    use RefreshDatabase;

    private function setupKelas(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_berlaku' => 2026,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TRPL 2026',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'program_studi_id' => $prodi->id,
            'kode' => 'TRPL101',
            'nama' => 'Pemrograman Web Lanjut',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen Pengampu Web',
            'email' => 'dosenweb@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '11223344',
            'jabatan' => 'Dosen',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        // Mahasiswa di kelas
        $userMhs = User::create([
            'name' => 'Mahasiswa Kelas A',
            'email' => 'mhs@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'program_studi_id' => $prodi->id,
            'nim' => '2026001',
            'nama' => 'Mahasiswa Kelas A',
            'angkatan' => 2026,
            'status' => 'Aktif',
        ]);

        $pengampu->mahasiswas()->attach($mhs->id);

        // Kaprodi
        $userKaprodi = User::create([
            'name' => 'Kaprodi TRPL',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '55667788',
            'jabatan' => 'Kaprodi',
        ]);

        // Direktur
        $userDirektur = User::create([
            'name' => 'Direktur Kampus',
            'email' => 'direktur@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
        ]);

        // Admin
        $userAdmin = User::create([
            'name' => 'Admin LMS',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen Pengampu Web',
            'semester' => 3,
            'deskripsi' => 'RPS Web Lanjut',
            'status' => 'Disetujui',
        ]);

        return compact(
            'prodi', 'kurikulum', 'ta', 'mk', 'userDosen', 'dosen', 'pengampu',
            'userMhs', 'mhs', 'userKaprodi', 'kaprodi', 'userDirektur', 'userAdmin', 'rps'
        );
    }

    public function test_sync_draft_tugas_dari_rps_mengirim_notifikasi_draft_ke_dosen_pengampu(): void
    {
        Notification::fake();
        $data = $this->setupKelas();

        $rpsTugas = RpsTugas::create([
            'rps_id' => $data['rps']->id,
            'minggu_topik' => 3,
            'nama_tugas' => 'Tugas 3: REST API',
            'sub_cpmk' => 'Implementasi API',
            'penugasan' => 'Buat REST API Laravel',
            'ruang_lingkup' => 'Backend',
            'cara_pengerjaan' => 'Kelompok',
            'batas_waktu' => '1 Minggu',
            'luaran_tugas' => 'Repository Git',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 80,
        ]);

        $response = $this->actingAs($data['userDosen'])
            ->post(route('rps.tugas.upload-ke-lms', [$data['rps']->id, $rpsTugas->id]));

        $response->assertRedirect(route('rps.tugas.index', $data['rps']->id));

        $this->assertDatabaseHas('lms_tugas', [
            'pengampu_id' => $data['pengampu']->id,
            'rps_tugas_id' => $rpsTugas->id,
            'judul' => 'Tugas 3: REST API',
            'is_active' => false,
        ]);

        Notification::assertSentTo(
            $data['userDosen'],
            DrafTugasRpsBaru::class,
            function ($notification) use ($data) {
                return $notification->pengampu->id === $data['pengampu']->id
                    && str_contains($notification->toDatabase($data['userDosen'])['judul'], 'Draf Tugas Baru');
            }
        );
    }

    public function test_publikasikan_draft_tugas_memperbarui_batas_upload_mb_dan_deadline(): void
    {
        Notification::fake();
        $data = $this->setupKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Draf Tugas Proyek',
            'instruksi' => 'Kerjakan proyek',
            'deadline' => now()->addDays(2),
            'bobot_nilai' => 100,
            'batas_upload_mb' => 50,
            'is_active' => false,
        ]);

        $newDeadline = now()->addDays(10)->format('Y-m-d\TH:i');

        $response = $this->actingAs($data['userDosen'])
            ->post(route('lms.tugas.tugaskan', [$data['pengampu']->id, $tugas->id]), [
                'batas_upload_mb' => 25,
                'deadline' => $newDeadline,
            ]);

        $response->assertRedirect();
        $tugas->refresh();

        $this->assertTrue($tugas->is_active);
        $this->assertEquals(25, $tugas->batas_upload_mb);
        $this->assertEquals(now()->addDays(10)->format('Y-m-d H:i'), $tugas->deadline->format('Y-m-d H:i'));
    }

    public function test_publikasikan_draft_tugas_mengirim_notifikasi_ke_semua_stakeholder_non_admin(): void
    {
        Notification::fake();
        $data = $this->setupKelas();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas Database Migration',
            'instruksi' => 'Buat schema migration',
            'deadline' => now()->addDays(5),
            'bobot_nilai' => 90,
            'batas_upload_mb' => 10,
            'is_active' => false,
        ]);

        $response = $this->actingAs($data['userDosen'])
            ->post(route('lms.tugas.tugaskan', [$data['pengampu']->id, $tugas->id]), [
                'batas_upload_mb' => 10,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Mahasiswa kelas menerima notif
        Notification::assertSentTo($data['userMhs'], TugasBaru::class, function ($n) {
            return $n->targetRole === 'mahasiswa';
        });

        // Kaprodi, Direktur, dan Admin TIDAK menerima notif tugas harian
        Notification::assertNotSentTo($data['userKaprodi'], TugasBaru::class);
        Notification::assertNotSentTo($data['userDirektur'], TugasBaru::class);
        Notification::assertNotSentTo($data['userAdmin'], TugasBaru::class);
    }

    public function test_mahasiswa_upload_jawaban_melebihi_batas_upload_mb_ditolak(): void
    {
        $this->mock(GoogleDriveService::class, function ($mock) {
            $mock->shouldReceive('storeFile')->andReturn('lms/submissions/jawaban_pas.pdf');
        });

        $data = $this->setupKelas();

        // Tugas dengan batas 2 MB
        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas Kecil 2MB',
            'instruksi' => 'Kumpulkan file maks 2MB',
            'deadline' => now()->addDays(3),
            'bobot_nilai' => 50,
            'batas_upload_mb' => 2,
            'is_active' => true,
        ]);

        // Upload 3 MB (3072 KB) > batas 2 MB -> Harus gagal validasi
        $fileTerlaluBesar = UploadedFile::fake()->create('jawaban.pdf', 3072, 'application/pdf');

        $response = $this->actingAs($data['userMhs'])
            ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                'file_jawaban' => $fileTerlaluBesar,
                'catatan_mahasiswa' => 'Jawaban saya',
            ]);

        $response->assertSessionHasErrors('file_jawaban');

        // Upload 1 MB (1024 KB) <= batas 2 MB -> Harus berhasil
        $filePas = UploadedFile::fake()->create('jawaban_pas.pdf', 1024, 'application/pdf');

        $responseSuccess = $this->actingAs($data['userMhs'])
            ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                'file_jawaban' => $filePas,
                'catatan_mahasiswa' => 'Jawaban pas',
            ]);

        $responseSuccess->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lms_submissions', [
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mhs']->id,
        ]);
    }
}
