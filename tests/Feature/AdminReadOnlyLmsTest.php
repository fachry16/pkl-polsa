<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsMateri;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
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

class AdminReadOnlyLmsTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
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
            'deskripsi' => 'Deskripsi Kurikulum TRPL',
            'status' => 'Aktif',
        ]);

        $ta = TahunAkademik::create(['tahun' => 2026, 'semester' => 'Ganjil', 'is_active' => true]);

        $userAdmin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
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
            'nidn' => '11223344',
            'jabatan' => 'Dosen',
        ]);

        $userMhs = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '32240010',
            'nama' => 'Mahasiswa Test',
            'email' => 'mhs@test.dev',
            'program_studi_id' => $prodi->id,
            'kurikulum_id' => $kurikulum->id,
            'semester' => 3,
            'angkatan' => 2024,
        ]);

        $mk = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'RPL301',
            'nama' => 'Arsitektur Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_pengembang_id' => $dosen->id,
            'dosen_pengampu' => 'Dosen Pengampu',
            'semester' => 3,
            'deskripsi' => 'RPS APL',
            'status' => 'Disetujui',
        ]);

        $pertemuan = RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Memahami Arsitektur Software',
            'materi' => 'Konsep Monolitik vs Microservices',
            'metode' => 'Diskusi & Ceramah',
            'pengalaman_belajar' => 'Studi kasus',
            'indikator' => 'Ketepatan analisis',
            'bobot' => 10,
        ]);

        $pengampu = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_akademik_id' => $ta->id,
            'kelas' => 'A',
            'semester_akademik' => 'Ganjil 2026/2027',
        ]);

        $pengampu->mahasiswas()->attach($mahasiswa->id);

        $materi = LmsMateri::create([
            'pengampu_id' => $pengampu->id,
            'rps_pertemuan_id' => $pertemuan->id,
            'judul' => 'Materi 1: Arsitektur',
            'deskripsi' => 'Pengenalan Monolitik',
        ]);

        $tugas = LmsTugas::create([
            'pengampu_id' => $pengampu->id,
            'rps_pertemuan_id' => $pertemuan->id,
            'judul' => 'Tugas 1: Diagram Monolitik',
            'instruksi' => 'Buat diagram',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 100,
            'is_active' => true,
        ]);

        $submission = LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $mahasiswa->id,
            'catatan_mahasiswa' => 'Jawaban saya',
            'dikumpulkan_pada' => now(),
        ]);

        return compact('userAdmin', 'userDosen', 'mahasiswa', 'pengampu', 'materi', 'tugas', 'submission', 'pertemuan');
    }

    public function test_admin_dapat_melihat_daftar_kelas_dan_detail_lms(): void
    {
        $data = $this->setupData();

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.index'))
            ->assertStatus(200);

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.show', $data['pengampu']->id))
            ->assertStatus(200)
            ->assertSee('Read-Only (Admin)');

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.materi.index', $data['pengampu']->id))
            ->assertStatus(200);

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.tugas.index', $data['pengampu']->id))
            ->assertStatus(200);

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.tugas.show', [$data['pengampu']->id, $data['tugas']->id]))
            ->assertStatus(200);

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.tugas.rekap', $data['pengampu']->id))
            ->assertStatus(200);

        $this->actingAs($data['userAdmin'])
            ->get(route('lms.monitor'))
            ->assertStatus(200);
    }

    public function test_admin_ditolak_melakukan_crud_materi_dan_tugas_lms(): void
    {
        $data = $this->setupData();

        // Store Materi -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.materi.store', $data['pengampu']->id), [
                'judul' => 'Materi Ilegal',
                'deskripsi' => 'Materi oleh Admin',
            ])
            ->assertStatus(403);

        // Update Materi -> 403
        $this->actingAs($data['userAdmin'])
            ->patch(route('lms.materi.update', [$data['pengampu']->id, $data['materi']->id]), [
                'judul' => 'Materi Edit Admin',
            ])
            ->assertStatus(403);

        // Delete Materi -> 403
        $this->actingAs($data['userAdmin'])
            ->delete(route('lms.materi.destroy', [$data['pengampu']->id, $data['materi']->id]))
            ->assertStatus(403);

        // Store Tugas -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.tugas.store', $data['pengampu']->id), [
                'judul' => 'Tugas Admin',
                'instruksi' => 'Instruksi',
                'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'bobot_nilai' => 50,
            ])
            ->assertStatus(403);

        // Tugaskan -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.tugas.tugaskan', [$data['pengampu']->id, $data['tugas']->id]))
            ->assertStatus(403);

        // Update Tugas -> 403
        $this->actingAs($data['userAdmin'])
            ->patch(route('lms.tugas.update', [$data['pengampu']->id, $data['tugas']->id]), [
                'judul' => 'Tugas Edit Admin',
                'instruksi' => 'Edit',
                'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'bobot_nilai' => 50,
            ])
            ->assertStatus(403);

        // Delete Tugas -> 403
        $this->actingAs($data['userAdmin'])
            ->delete(route('lms.tugas.destroy', ['pengampu' => $data['pengampu']->id, 'tugas' => $data['tugas']->id]))
            ->assertStatus(403);

        // Nilai Submission -> 403
        $this->actingAs($data['userAdmin'])
            ->patch(route('lms.submission.nilai', $data['submission']->id), [
                'nilai' => 90,
            ])
            ->assertStatus(403);
    }

    public function test_admin_ditolak_melakukan_crud_pengumuman_forum_dan_absensi(): void
    {
        $data = $this->setupData();

        // Store Pengumuman -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.pengumuman.store', $data['pengampu']->id), [
                'judul' => 'Pengumuman Admin',
                'isi' => 'Isi pengumuman',
            ])
            ->assertStatus(403);

        // Store Forum -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.forum.store', $data['pengampu']->id), [
                'pesan' => 'Pesan Forum Admin',
            ])
            ->assertStatus(403);

        // Store Komentar -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.topik.komentar.store', $data['pengampu']->id), [
                'tipe_topik' => 'tugas',
                'topik_id' => $data['tugas']->id,
                'pesan' => 'Komentar Admin',
            ])
            ->assertStatus(403);

        // Buka Sesi Absensi -> 403
        $this->actingAs($data['userAdmin'])
            ->post(route('lms.absensi.buka', $data['pengampu']->id), [
                'rps_pertemuan_id' => $data['pertemuan']->id,
            ])
            ->assertStatus(403);
    }
}
