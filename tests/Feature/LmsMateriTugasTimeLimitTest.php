<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsMateri;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LmsMateriTugasTimeLimitTest extends TestCase
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

        return compact('userDosen', 'dosen', 'pengampu');
    }

    public function test_dosen_dapat_mengedit_dan_menghapus_materi_dalam_24_jam(): void
    {
        $data = $this->buatData();
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 8, 0, 0));

        $materi = LmsMateri::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Materi Pertemuan 1',
            'deskripsi' => 'Pengantar Web',
        ]);

        // Maju 12 jam
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 20, 0, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.materi.edit', [$data['pengampu']->id, $materi->id]))
            ->assertOk();

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.materi.update', [$data['pengampu']->id, $materi->id]), [
                'judul' => 'Materi Pertemuan 1 (Revisi)',
                'deskripsi' => 'Deskripsi baru',
            ])
            ->assertRedirect(route('lms.materi.index', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_materis', [
            'id' => $materi->id,
            'judul' => 'Materi Pertemuan 1 (Revisi)',
        ]);

        // Hapus sebelum 24 jam
        $this->actingAs($data['userDosen'])
            ->delete(route('lms.materi.destroy', [$data['pengampu']->id, $materi->id]))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_materis', ['id' => $materi->id]);

        Carbon::setTestNow();
    }

    public function test_dosen_ditolak_mengedit_dan_menghapus_materi_setelah_24_jam(): void
    {
        $data = $this->buatData();
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 8, 0, 0));

        $materi = LmsMateri::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Materi Lama',
            'deskripsi' => 'Deskripsi lama',
        ]);

        // Maju 25 jam
        Carbon::setTestNow(Carbon::create(2026, 9, 3, 9, 0, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.materi.edit', [$data['pengampu']->id, $materi->id]))
            ->assertRedirect(route('lms.materi.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.materi.update', [$data['pengampu']->id, $materi->id]), [
                'judul' => 'Mencoba Update',
            ])
            ->assertRedirect(route('lms.materi.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.materi.destroy', [$data['pengampu']->id, $materi->id]))
            ->assertSessionHas('toast_error');

        $this->assertDatabaseHas('lms_materis', ['id' => $materi->id]);

        Carbon::setTestNow();
    }

    public function test_dosen_dapat_mengedit_dan_menghapus_tugas_dalam_24_jam(): void
    {
        $data = $this->buatData();
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 8, 0, 0));

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1 Web',
            'instruksi' => 'Kerjakan modul 1',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 15,
        ]);

        // Maju 10 jam
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 18, 0, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.tugas.edit', [$data['pengampu']->id, $tugas->id]))
            ->assertOk();

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.tugas.update', [$data['pengampu']->id, $tugas->id]), [
                'judul' => 'Tugas 1 Web (Revisi)',
                'instruksi' => 'Kerjakan modul 1 dan 2',
                'deadline' => now()->addDays(8)->toDateTimeString(),
                'bobot_nilai' => 20,
            ])
            ->assertRedirect(route('lms.tugas.index', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_tugas', [
            'id' => $tugas->id,
            'judul' => 'Tugas 1 Web (Revisi)',
            'bobot_nilai' => 20,
        ]);

        // Hapus tugas
        $this->actingAs($data['userDosen'])
            ->delete(route('lms.tugas.destroy', [$data['pengampu']->id, $tugas->id]))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_tugas', ['id' => $tugas->id]);

        Carbon::setTestNow();
    }

    public function test_dosen_ditolak_mengedit_dan_menghapus_tugas_setelah_24_jam(): void
    {
        $data = $this->buatData();
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 8, 0, 0));

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas Lama',
            'instruksi' => 'Instruksi lama',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 15,
        ]);

        // Maju 25 jam
        Carbon::setTestNow(Carbon::create(2026, 9, 3, 9, 0, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.tugas.edit', [$data['pengampu']->id, $tugas->id]))
            ->assertRedirect(route('lms.tugas.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.tugas.update', [$data['pengampu']->id, $tugas->id]), [
                'judul' => 'Tugas Dicoba Ubah',
                'instruksi' => 'Instruksi baru',
                'deadline' => now()->addDays(5)->toDateTimeString(),
                'bobot_nilai' => 20,
            ])
            ->assertRedirect(route('lms.tugas.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.tugas.destroy', [$data['pengampu']->id, $tugas->id]))
            ->assertSessionHas('toast_error');

        $this->assertDatabaseHas('lms_tugas', ['id' => $tugas->id]);

        Carbon::setTestNow();
    }
}
