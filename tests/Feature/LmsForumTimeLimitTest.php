<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsForumDiskusi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LmsForumTimeLimitTest extends TestCase
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

        return compact('userDosen', 'dosen', 'userMhs', 'mahasiswa', 'pengampu');
    }

    public function test_mahasiswa_dapat_mengubah_dan_menghapus_pesan_dalam_15_menit(): void
    {
        $data = $this->buatData();

        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 0, 0));

        $diskusi = LmsForumDiskusi::create([
            'pengampu_id' => $data['pengampu']->id,
            'user_id' => $data['userMhs']->id,
            'pesan' => 'Pesan asli mahasiswa',
        ]);

        // Maju 10 menit (masih dalam batas 15 menit)
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 10, 0));

        $this->actingAs($data['userMhs'])
            ->patch(route('mahasiswa.lms.forum.update', [$data['pengampu']->id, $diskusi->id]), [
                'pesan' => 'Pesan diperbarui oleh mahasiswa',
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_forum_diskusis', [
            'id' => $diskusi->id,
            'pesan' => 'Pesan diperbarui oleh mahasiswa',
        ]);

        // Hapus sebelum 15 menit
        $this->actingAs($data['userMhs'])
            ->delete(route('mahasiswa.lms.forum.destroy', [$data['pengampu']->id, $diskusi->id]))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_forum_diskusis', ['id' => $diskusi->id]);

        Carbon::setTestNow();
    }

    public function test_mahasiswa_ditolak_mengubah_dan_menghapus_pesan_setelah_15_menit(): void
    {
        $data = $this->buatData();

        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 0, 0));

        $diskusi = LmsForumDiskusi::create([
            'pengampu_id' => $data['pengampu']->id,
            'user_id' => $data['userMhs']->id,
            'pesan' => 'Pesan lama mahasiswa',
        ]);

        // Maju 16 menit (lewat batas 15 menit)
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 16, 0));

        $this->actingAs($data['userMhs'])
            ->patch(route('mahasiswa.lms.forum.update', [$data['pengampu']->id, $diskusi->id]), [
                'pesan' => 'Mencoba ubah pesan lama',
            ])
            ->assertForbidden();

        $this->actingAs($data['userMhs'])
            ->delete(route('mahasiswa.lms.forum.destroy', [$data['pengampu']->id, $diskusi->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('lms_forum_diskusis', [
            'id' => $diskusi->id,
            'pesan' => 'Pesan lama mahasiswa',
        ]);

        Carbon::setTestNow();
    }

    public function test_dosen_ditolak_mengedit_pesan_sendiri_setelah_15_menit(): void
    {
        $data = $this->buatData();

        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 0, 0));

        $diskusi = LmsForumDiskusi::create([
            'pengampu_id' => $data['pengampu']->id,
            'user_id' => $data['userDosen']->id,
            'pesan' => 'Pesan dosen',
        ]);

        // Maju 20 menit
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 10, 20, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.forum.edit', [$data['pengampu']->id, $diskusi->id]))
            ->assertForbidden();

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.forum.update', [$data['pengampu']->id, $diskusi->id]), [
                'pesan' => 'Ubah pesan dosen lama',
            ])
            ->assertForbidden();

        Carbon::setTestNow();
    }

    public function test_dosen_tidak_dapat_menghapus_pesan_mahasiswa(): void
    {
        $data = $this->buatData();

        $diskusi = LmsForumDiskusi::create([
            'pengampu_id' => $data['pengampu']->id,
            'user_id' => $data['userMhs']->id,
            'pesan' => 'Pesan mahasiswa',
        ]);

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.forum.destroy', [$data['pengampu']->id, $diskusi->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('lms_forum_diskusis', ['id' => $diskusi->id]);
    }

    public function test_dosen_dapat_menghapus_pesan_sendiri(): void
    {
        $data = $this->buatData();

        $diskusi = LmsForumDiskusi::create([
            'pengampu_id' => $data['pengampu']->id,
            'user_id' => $data['userDosen']->id,
            'pesan' => 'Pesan dosen sendiri',
        ]);

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.forum.destroy', [$data['pengampu']->id, $diskusi->id]))
            ->assertRedirect(route('lms.forum.index', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_forum_diskusis', ['id' => $diskusi->id]);
    }
}
