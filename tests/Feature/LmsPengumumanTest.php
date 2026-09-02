<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsPengumuman;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsPengumumanTest extends TestCase
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

    public function test_dosen_dapat_membuat_pengumuman(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userDosen'])
            ->post(route('lms.pengumuman.store', $data['pengampu']->id), [
                'judul' => 'Ujian Tengah Semester',
                'isi' => 'UTS dilaksanakan pekan depan.',
            ])
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_pengumumans', [
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Ujian Tengah Semester',
            'isi' => 'UTS dilaksanakan pekan depan.',
        ]);
    }

    public function test_dosen_dapat_menampilkan_form_edit_pengumuman(): void
    {
        $data = $this->buatData();
        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul Asli',
            'isi' => 'Isi asli',
            'published_at' => now(),
        ]);

        $this->actingAs($data['userDosen'])
            ->get(route('lms.pengumuman.edit', [$data['pengampu']->id, $pengumuman->id]))
            ->assertOk()
            ->assertSee('Judul Asli');
    }

    public function test_dosen_dapat_memperbarui_pengumuman(): void
    {
        $data = $this->buatData();
        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul Asli',
            'isi' => 'Isi asli',
            'published_at' => now(),
        ]);

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.pengumuman.update', [$data['pengampu']->id, $pengumuman->id]), [
                'judul' => 'Judul Baru',
                'isi' => 'Isi baru',
            ])
            ->assertRedirect(route('lms.pengumuman.index', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('lms_pengumumans', [
            'id' => $pengumuman->id,
            'judul' => 'Judul Baru',
            'isi' => 'Isi baru',
        ]);
    }

    public function test_dosen_dapat_menghapus_pengumuman(): void
    {
        $data = $this->buatData();
        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul',
            'isi' => 'Isi',
            'published_at' => now(),
        ]);

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.pengumuman.destroy', [$data['pengampu']->id, $pengumuman->id]))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseMissing('lms_pengumumans', ['id' => $pengumuman->id]);
    }

    public function test_dosen_ditolak_mengedit_pengumuman_setelah_30_menit(): void
    {
        $data = $this->buatData();
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::create(2026, 9, 2, 10, 0, 0));

        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul Awal',
            'isi' => 'Isi awal',
            'published_at' => now(),
        ]);

        // Maju 35 menit
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::create(2026, 9, 2, 10, 35, 0));

        $this->actingAs($data['userDosen'])
            ->get(route('lms.pengumuman.edit', [$data['pengampu']->id, $pengumuman->id]))
            ->assertRedirect(route('lms.pengumuman.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.pengumuman.update', [$data['pengampu']->id, $pengumuman->id]), [
                'judul' => 'Judul Berubah',
                'isi' => 'Isi berubah',
            ])
            ->assertRedirect(route('lms.pengumuman.index', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        \Illuminate\Support\Carbon::setTestNow();
    }

    public function test_dosen_ditolak_menghapus_pengumuman_setelah_24_jam(): void
    {
        $data = $this->buatData();
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::create(2026, 9, 2, 10, 0, 0));

        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul Awal',
            'isi' => 'Isi awal',
            'published_at' => now(),
        ]);

        // Maju 25 jam
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::create(2026, 9, 3, 11, 0, 0));

        $this->actingAs($data['userDosen'])
            ->delete(route('lms.pengumuman.destroy', [$data['pengampu']->id, $pengumuman->id]))
            ->assertSessionHas('toast_error');

        $this->assertDatabaseHas('lms_pengumumans', ['id' => $pengumuman->id]);

        \Illuminate\Support\Carbon::setTestNow();
    }

    public function test_dosen_lain_tidak_dapat_mengelola_pengumuman(): void
    {
        $data = $this->buatData();
        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Judul',
            'isi' => 'Isi',
            'published_at' => now(),
        ]);

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
            ->get(route('lms.pengumuman.edit', [$data['pengampu']->id, $pengumuman->id]))
            ->assertForbidden();

        $this->actingAs($userLain)
            ->patch(route('lms.pengumuman.update', [$data['pengampu']->id, $pengumuman->id]), [
                'judul' => 'Hacked',
                'isi' => 'Hacked',
            ])
            ->assertForbidden();
    }
}
