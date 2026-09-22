<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsMateri;
use App\Models\LmsTugas;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use App\Models\RpsTugas;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RpsDrivenLmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_dapat_melihat_pilihan_rps_dan_menilai_upload_lms(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen RPL',
            'email' => 'dosenrpl@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99887766',
            'jabatan' => 'Dosen',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Deskripsi Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'RPL202',
            'nama' => 'Desain Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen RPL',
            'semester' => 3,
            'deskripsi' => 'RPS Desain PL',
            'status' => 'Disetujui',
        ]);

        $pertemuan = RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Memahami Arsitektur Software',
            'materi' => 'Konsep Monolitik vs Microservices',
            'metode' => 'Diskusi & Ceramah',
            'pengalaman_belajar' => 'Studi kasus arsitektur',
            'indikator' => 'Ketepatan analisis',
            'bobot' => 10,
        ]);

        $rpsTugas = RpsTugas::create([
            'rps_id' => $rps->id,
            'minggu_topik' => 1,
            'nama_tugas' => 'Tugas 1: Analisis Arsitektur',
            'sub_cpmk' => 'Memahami Arsitektur Software',
            'penugasan' => 'Buat diagram arsitektur sistem',
            'ruang_lingkup' => 'Sistem e-commerce',
            'cara_pengerjaan' => 'Mandiri PDF',
            'batas_waktu' => '1 Minggu',
            'luaran_tugas' => 'Laporan PDF',
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        // Access LMS materi index
        $materiRes = $this->actingAs($userDosen)->get(route('lms.materi.index', $pengampu->id));
        $materiRes->assertStatus(200);
        $materiRes->assertSee('Kelola Pertemuan RPS');

        // Access LMS tugas index
        $tugasRes = $this->actingAs($userDosen)->get(route('lms.tugas.index', $pengampu->id));
        $tugasRes->assertStatus(200);
        $tugasRes->assertSee('Kelola Rancangan Tugas di RPS');
    }

    public function test_upload_ke_lms_membuat_draf_dan_bisa_ditugaskan(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL2',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak 2',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen RPL 2',
            'email' => 'dosenrpl2@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99887767',
            'jabatan' => 'Dosen',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Deskripsi Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'RPL203',
            'nama' => 'Konstruksi Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen RPL 2',
            'semester' => 3,
            'deskripsi' => 'RPS Konstruksi PL',
            'status' => 'Disetujui',
        ]);

        $rpsTugas = RpsTugas::create([
            'rps_id' => $rps->id,
            'minggu_topik' => 2,
            'nama_tugas' => 'Tugas 2: Clean Code',
            'sub_cpmk' => 'Refactoring',
            'penugasan' => 'Refactor kode jelek',
            'ruang_lingkup' => 'OOP',
            'cara_pengerjaan' => 'Mandiri',
            'batas_waktu' => '1 Minggu',
            'luaran_tugas' => 'Pull Request',
            'deadline' => now()->addDays(5),
            'bobot_nilai' => 85,
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        // POST upload ke LMS
        $response = $this->actingAs($userDosen)
            ->post(route('rps.tugas.upload-ke-lms', [$rps->id, $rpsTugas->id]));

        $response->assertRedirect(route('rps.tugas.index', $rps->id));
        $this->assertDatabaseHas('lms_tugas', [
            'pengampu_id' => $pengampu->id,
            'judul' => 'Tugas 2: Clean Code',
            'is_active' => false,
        ]);

        $lmsTugas = LmsTugas::where('pengampu_id', $pengampu->id)->first();
        $this->assertNotNull($lmsTugas);
        $this->assertFalse($lmsTugas->is_active);

        // POST Tugaskan
        $tugaskanRes = $this->actingAs($userDosen)
            ->post(route('lms.tugas.tugaskan', [$pengampu->id, $lmsTugas->id]));

        $tugaskanRes->assertRedirect();
        $this->assertDatabaseHas('lms_tugas', [
            'id' => $lmsTugas->id,
            'is_active' => true,
        ]);
    }

    public function test_edit_rps_tugas_otomatis_memperbarui_lms_tugas(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL3',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak 3',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen RPL 3',
            'email' => 'dosenrpl3@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99887768',
            'jabatan' => 'Dosen',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Deskripsi Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'RPL204',
            'nama' => 'Pengujian Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen RPL 3',
            'semester' => 3,
            'deskripsi' => 'RPS PPL',
            'status' => 'Disetujui',
        ]);

        $rpsTugas = RpsTugas::create([
            'rps_id' => $rps->id,
            'minggu_topik' => 3,
            'nama_tugas' => 'Tugas 3: Unit Testing',
            'sub_cpmk' => 'Unit Test',
            'penugasan' => 'Buat PHPUnit test',
            'ruang_lingkup' => 'Model Test',
            'cara_pengerjaan' => 'Mandiri',
            'batas_waktu' => '1 Minggu',
            'luaran_tugas' => 'Code Coverage',
            'deadline' => now()->addDays(3),
            'bobot_nilai' => 80,
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        // Upload ke LMS
        $this->actingAs($userDosen)->post(route('rps.tugas.upload-ke-lms', [$rps->id, $rpsTugas->id]));

        $lmsTugas = LmsTugas::where('pengampu_id', $pengampu->id)->first();
        $this->assertEquals('Tugas 3: Unit Testing', $lmsTugas->judul);
        $this->assertEquals($rpsTugas->id, $lmsTugas->rps_tugas_id);

        // Edit RPS Tugas
        $newDeadline = now()->addDays(10)->format('Y-m-d H:i:s');
        $editRes = $this->actingAs($userDosen)->put(route('rps.tugas.update', [$rps->id, $rpsTugas->id]), [
            'minggu_topik' => 'Minggu 3',
            'nama_tugas' => 'Tugas 3: Unit Testing (Revisi)',
            'sub_cpmk' => 'Unit Test & Integration Test',
            'penugasan' => 'Buat unit test & integration test lengkap',
            'ruang_lingkup' => 'Model & Controller Test',
            'cara_pengerjaan' => 'Mandiri',
            'batas_waktu' => '2 Minggu',
            'luaran_tugas' => 'Code Coverage 100%',
            'deadline' => $newDeadline,
            'bobot_nilai' => 95,
        ]);

        $editRes->assertRedirect(route('rps.tugas.index', $rps->id));

        // Verifikasi LmsTugas otomatis ter-update!
        $lmsTugasRefresh = $lmsTugas->fresh();
        $this->assertEquals('Tugas 3: Unit Testing (Revisi)', $lmsTugasRefresh->judul);
        $this->assertEquals(95, $lmsTugasRefresh->bobot_nilai);
        $this->assertStringContainsString('Buat unit test & integration test lengkap', $lmsTugasRefresh->instruksi);
    }

    public function test_upload_materi_rps_membuat_lms_materi_di_semua_kelas(): void
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TRPL4',
            'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak 4',
            'jenjang' => 'D4',
            'akreditasi' => 'Baik',
        ]);

        $userDosen = User::create([
            'name' => 'Dosen RPL 4',
            'email' => 'dosenrpl4@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '99887769',
            'jabatan' => 'Dosen',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Deskripsi Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'RPL205',
            'nama' => 'Arsitektur Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen RPL 4',
            'semester' => 3,
            'deskripsi' => 'RPS Arsitektur PL',
            'status' => 'Disetujui',
        ]);

        $pertemuan = RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Konsep Arsitektur',
            'materi' => 'Pengantar Arsitektur Perangkat Lunak',
            'metode' => 'Diskusi',
            'pengalaman_belajar' => 'Studi kasus',
            'indikator' => 'Ketepatan',
            'bobot' => 10,
        ]);

        $pengampuA = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        $pengampuB = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'B',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf');

        $response = $this->actingAs($userDosen)
            ->post(route('rps.pertemuan.upload-materi', [$rps->id, $pertemuan->id]), [
                'file' => $file,
            ]);

        $response->assertRedirect(route('rps.pertemuan.index', $rps->id));

        foreach ([$pengampuA, $pengampuB] as $pengampu) {
            $this->assertDatabaseHas('lms_materis', [
                'pengampu_id' => $pengampu->id,
                'rps_pertemuan_id' => $pertemuan->id,
                'judul' => 'Pengantar Arsitektur Perangkat Lunak',
            ]);
        }

        $this->assertSame(2, LmsMateri::where('rps_pertemuan_id', $pertemuan->id)->count());

        // Upload ulang seharusnya idempoten, tidak menggandakan materi
        $this->actingAs($userDosen)
            ->post(route('rps.pertemuan.upload-materi', [$rps->id, $pertemuan->id]), [
                'file' => UploadedFile::fake()->create('modul-v2.pdf', 100, 'application/pdf'),
            ]);

        $this->assertSame(2, LmsMateri::where('rps_pertemuan_id', $pertemuan->id)->count());

        // Hapus pertemuan menghapus materi terkait
        $this->actingAs($userDosen)
            ->delete(route('rps.pertemuan.destroy', [$rps->id, $pertemuan->id]));

        $this->assertSame(0, LmsMateri::where('rps_pertemuan_id', $pertemuan->id)->count());
    }

    public function test_unduh_materi_rps_melewati_kontroler_tidak_403(): void
    {
        $prodi = ProgramStudi::create(['kode_prodi' => 'TRPL5', 'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak 5', 'jenjang' => 'D4', 'akreditasi' => 'Baik']);
        $userDosen = User::create(['name' => 'Dosen RPL 5', 'email' => 'dosenrpl5@test.dev', 'password' => bcrypt('password'), 'role' => 'dosen']);
        $dosen = Dosen::create(['user_id' => $userDosen->id, 'program_studi_id' => $prodi->id, 'nidn' => '99887770', 'jabatan' => 'Dosen']);
        $kurikulum = Kurikulum::create(['program_studi_id' => $prodi->id, 'nama_kurikulum' => 'Kurikulum 2024', 'tahun_berlaku' => 2024, 'beban_studi' => '144 SKS', 'deskripsi' => 'D', 'status' => 'Aktif']);
        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);
        $mk = MataKuliah::create(['kurikulum_id' => $kurikulum->id, 'kode' => 'RPL206', 'nama' => 'Basis Data', 'sks_teori' => 3, 'sks_praktikum' => 0, 'semester' => 2, 'jenis' => 'Wajib']);
        $rps = Rps::create(['mata_kuliah_id' => $mk->id, 'dosen_pengembang_id' => $dosen->id, 'dosen_pengampu' => 'Dosen RPL 5', 'semester' => 2, 'deskripsi' => 'RPS', 'status' => 'Disetujui']);

        Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf');

        $pertemuan = RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'ERD',
            'materi' => 'Konsep Basis Data',
            'metode' => 'Diskusi',
            'pengalaman_belajar' => 'Studi kasus',
            'indikator' => 'Ketepatan',
            'bobot' => 10,
        ]);

        $this->actingAs($userDosen)
            ->post(route('rps.pertemuan.upload-materi', [$rps->id, $pertemuan->id]), ['file' => $file])
            ->assertRedirect();

        $response = $this->actingAs($userDosen)
            ->get(route('rps.pertemuan.file', [$rps->id, $pertemuan->id]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
