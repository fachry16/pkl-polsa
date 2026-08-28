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
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsSubmissionUpdateTest extends TestCase
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

        return compact('userDosen', 'userMhs', 'mahasiswa', 'pengampu');
    }

    private function buatSubmission(array $data, array $override = []): array
    {
        $tugas = LmsTugas::create(array_merge([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 20,
        ], $override));

        $submission = LmsSubmission::create([
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $data['mahasiswa']->id,
            'catatan_mahasiswa' => 'Versi awal',
            'dikumpulkan_pada' => now(),
        ]);

        return compact('tugas', 'submission');
    }

    public function test_mahasiswa_dapat_memperbarui_kiriman(): void
    {
        $data = $this->buatData();
        $payload = $this->buatSubmission($data);
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('jawaban-lama.pdf', 10);
        $payload['submission']->update(['file_jawaban' => $oldFile->store('lms/submissions', 'public')]);

        $newFile = UploadedFile::fake()->create('jawaban-baru.pdf', 20);

        $this->actingAs($data['userMhs'])
            ->patch(route('mahasiswa.lms.tugas.update', $payload['submission']->id), [
                'file_jawaban' => $newFile,
                'catatan_mahasiswa' => 'Versi revisi',
            ])
            ->assertSessionHas('toast_success');

        $submission = $payload['submission']->fresh();

        $this->assertNotEquals($payload['submission']->file_jawaban, $submission->file_jawaban);
        $this->assertEquals('Versi revisi', $submission->catatan_mahasiswa);

        Storage::disk('public')->assertMissing($payload['submission']->file_jawaban);
        Storage::disk('public')->assertExists($submission->file_jawaban);
    }

    public function test_mahasiswa_tidak_bisa_memperbarui_setelah_deadline(): void
    {
        $data = $this->buatData();
        $payload = $this->buatSubmission($data, ['deadline' => now()->subDay()]);

        $this->actingAs($data['userMhs'])
            ->patch(route('mahasiswa.lms.tugas.update', $payload['submission']->id), [
                'catatan_mahasiswa' => 'Nekat revisi',
            ])
            ->assertForbidden();

        $this->assertEquals('Versi awal', $payload['submission']->fresh()->catatan_mahasiswa);
    }

    public function test_mahasiswa_tidak_bisa_memperbarui_setelah_dinilai(): void
    {
        $data = $this->buatData();
        $payload = $this->buatSubmission($data);
        $payload['submission']->update(['nilai' => 85]);

        $this->actingAs($data['userMhs'])
            ->patch(route('mahasiswa.lms.tugas.update', $payload['submission']->id), [
                'catatan_mahasiswa' => 'Nekat revisi',
            ])
            ->assertForbidden();
    }

    public function test_mahasiswa_lain_tidak_bisa_memperbarui_kiriman(): void
    {
        $data = $this->buatData();
        $payload = $this->buatSubmission($data);

        $userLain = User::create([
            'name' => 'Mahasiswa Lain',
            'email' => 'lain@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswaLain = Mahasiswa::create([
            'user_id' => $userLain->id,
            'nim' => '32240002',
            'nama' => 'Mahasiswa Lain',
            'email' => 'lain@test.dev',
            'program_studi_id' => $data['pengampu']->mataKuliah->kurikulum->program_studi_id,
            'kurikulum_id' => $data['pengampu']->mataKuliah->kurikulum_id,
            'semester' => 3,
            'angkatan' => 2024,
        ]);

        $data['pengampu']->mahasiswas()->attach($mahasiswaLain->id);

        $this->actingAs($userLain)
            ->patch(route('mahasiswa.lms.tugas.update', $payload['submission']->id), [
                'catatan_mahasiswa' => 'Curi revisi',
            ])
            ->assertForbidden();
    }
}
