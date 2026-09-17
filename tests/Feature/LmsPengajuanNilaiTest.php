<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentApproval;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPenilaian;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Notifications\NilaiDiajukan;
use App\Notifications\NilaiDirevisi;
use App\Notifications\NilaiDisetujui;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LmsPengajuanNilaiTest extends TestCase
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

        $userKaprodi = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1300',
            'jabatan' => 'Kaprodi',
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

        return compact('userDosen', 'dosen', 'userKaprodi', 'kaprodi', 'mahasiswa', 'pengampu');
    }

    private function isiNilaiAkhir(Pengampu $pengampu, Mahasiswa $mahasiswa, float $nilai = 80): void
    {
        LmsNilaiMahasiswa::updateOrCreate(
            ['pengampu_id' => $pengampu->id, 'mahasiswa_id' => $mahasiswa->id, 'komponen' => 'akhir'],
            ['nilai' => $nilai]
        );
    }

    private function buatApproval(Pengampu $pengampu, string $status = 'menunggu'): AssessmentApproval
    {
        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => 'dinilai', 'created_by' => $pengampu->dosen->user_id]
        );

        return AssessmentApproval::updateOrCreate(
            ['assessment_id' => $assessment->id],
            ['status' => $status, 'diajukan_oleh' => $pengampu->dosen->user_id, 'diajukan_at' => now()]
        );
    }

    public function test_dosen_belum_bisa_mengajukan_saat_nilai_belum_lengkap(): void
    {
        $data = $this->buatKelas();

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.nilai.ajukan', $data['pengampu']->id))
            ->assertSessionHas('toast_error');

        $this->assertDatabaseCount('assessment_approvals', 0);
    }

    public function test_dosen_mengajukan_nilai_saat_lengkap_dan_notif_kaprodi(): void
    {
        Notification::fake();
        $data = $this->buatKelas();
        $this->isiNilaiAkhir($data['pengampu'], $data['mahasiswa']);

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.nilai.ajukan', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $this->assertDatabaseHas('assessment_approvals', [
            'assessment_id' => Assessment::where('pengampu_id', $data['pengampu']->id)->first()->id,
            'status' => 'menunggu',
            'diajukan_oleh' => $data['userDosen']->id,
        ]);

        Notification::assertSentTo($data['userKaprodi'], NilaiDiajukan::class);
    }

    public function test_tidak_bisa_mengajukan_kembali_saat_menunggu(): void
    {
        $data = $this->buatKelas();
        $this->isiNilaiAkhir($data['pengampu'], $data['mahasiswa']);
        $this->buatApproval($data['pengampu'], 'menunggu');

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.nilai.ajukan', $data['pengampu']->id))
            ->assertSessionHas('toast_error');
    }

    public function test_kaprodi_menyetujui_nilai_dan_notif_dosen(): void
    {
        Notification::fake();
        $data = $this->buatKelas();
        $approval = $this->buatApproval($data['pengampu'], 'menunggu');

        $this->actingAs($data['userKaprodi'])
            ->patch(route('assessment.setujui', $approval->assessment_id))
            ->assertSessionHas('success');

        $this->assertEquals('disetujui', $approval->fresh()->status);
        $this->assertNotNull($approval->fresh()->disetujui_at);

        Notification::assertSentTo($data['userDosen'], NilaiDisetujui::class);
    }

    public function test_kaprodi_minta_revisi_nilai_dan_notif_dosen(): void
    {
        Notification::fake();
        $data = $this->buatKelas();
        $approval = $this->buatApproval($data['pengampu'], 'menunggu');

        $this->actingAs($data['userKaprodi'])
            ->patch(route('assessment.revisi', $approval->assessment_id), [
                'catatan_revisi' => 'Periksa kembali bobot UTS.',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('direvisi', $approval->fresh()->status);
        $this->assertEquals('Periksa kembali bobot UTS.', $approval->fresh()->catatan_revisi);

        Notification::assertSentTo($data['userDosen'], NilaiDirevisi::class);
    }

    public function test_dosen_mengajukan_ulang_setelah_direvisi(): void
    {
        $data = $this->buatKelas();
        $this->isiNilaiAkhir($data['pengampu'], $data['mahasiswa']);
        $approval = $this->buatApproval($data['pengampu'], 'direvisi');
        $approval->update(['catatan_revisi' => 'Perbaiki nilai UTS']);

        $this->actingAs($data['userDosen'])
            ->patch(route('lms.nilai.ajukan', $data['pengampu']->id))
            ->assertSessionHas('toast_success');

        $approval->refresh();
        $this->assertEquals('menunggu', $approval->status);
        $this->assertNull($approval->catatan_revisi);
    }

    public function test_admin_tidak_bisa_mengajukan_nilai(): void
    {
        $data = $this->buatKelas();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->patch(route('lms.nilai.ajukan', $data['pengampu']->id))
            ->assertForbidden();
    }

    public function test_halaman_pengajuan_kaprodi(): void
    {
        $data = $this->buatKelas();
        $this->buatApproval($data['pengampu'], 'menunggu');

        $this->actingAs($data['userKaprodi'])
            ->get(route('assessment.pengajuan'))
            ->assertOk()
            ->assertSee('Pengajuan Nilai Kelas')
            ->assertSee('Pemrograman Web')
            ->assertSee('Menunggu');
    }

    public function test_monitoring_lms_menampilkan_badge_pengajuan_menunggu(): void
    {
        $data = $this->buatKelas();
        $this->buatApproval($data['pengampu'], 'menunggu');

        $this->actingAs($data['userKaprodi'])
            ->get(route('monitoring.lms'))
            ->assertOk()
            ->assertSee('Pengajuan Nilai');
    }

    public function test_monitoring_lms_tidak_menampilkan_badge_tanpa_pengajuan(): void
    {
        $data = $this->buatKelas();

        $this->actingAs($data['userKaprodi'])
            ->get(route('monitoring.lms'))
            ->assertOk()
            ->assertDontSee('Pengajuan Nilai');
    }
}
