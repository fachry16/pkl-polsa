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
use App\Models\RpsPenilaian;
use App\Models\RpsPertemuan;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Notifications\MateriBaru;
use App\Notifications\RpsDiajukan;
use App\Notifications\SubmissionBaru;
use App\Notifications\TugasBaru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
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

        return compact('userDosen', 'userMhs', 'dosen', 'mahasiswa', 'pengampu');
    }

    public function test_tambah_materi_mengirim_notif_ke_mahasiswa_di_kelas(): void
    {
        $data = $this->buatData();

        Notification::fake();

        $this->actingAs($data['userDosen'])
            ->post(route('lms.materi.store', $data['pengampu']->id), [
                'judul' => 'Materi Bab 1',
                'deskripsi' => 'Pengantar',
            ])
            ->assertSessionHas('toast_success');

        Notification::assertSentTo($data['userMhs'], MateriBaru::class);
    }

    public function test_tambah_tugas_mengirim_notif_ke_mahasiswa_di_kelas(): void
    {
        $data = $this->buatData();

        Notification::fake();

        $this->actingAs($data['userDosen'])
            ->post(route('lms.tugas.store', $data['pengampu']->id), [
                'judul' => 'Tugas 1',
                'instruksi' => 'Kerjakan',
                'deadline' => now()->addDays(7)->format('Y-m-d\TH:i'),
                'bobot_nilai' => 20,
            ])
            ->assertSessionHas('toast_success');

        Notification::assertSentTo($data['userMhs'], TugasBaru::class);
    }

    public function test_kumpul_tugas_mengirim_notif_ke_dosen_pengampu(): void
    {
        $data = $this->buatData();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 20,
        ]);

        Notification::fake();

        $this->actingAs($data['userMhs'])
            ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                'catatan_mahasiswa' => 'Sudah selesai',
            ])
            ->assertSessionHas('toast_success');

        Notification::assertSentTo($data['userDosen'], SubmissionBaru::class);
    }

    public function test_ajukan_rps_mengirim_notif_ke_kaprodi(): void
    {
        $data = $this->buatData();

        $userKaprodi = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $prodiId = $data['pengampu']->mataKuliah->kurikulum->program_studi_id;

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodiId,
            'nidn' => '1300',
            'jabatan' => 'Kaprodi',
        ]);

        $rps = Rps::create([
            'mata_kuliah_id' => $data['pengampu']->mata_kuliah_id,
            'semester' => 3,
            'dosen_pengampu' => $data['dosen']->user->name,
            'status' => 'Draft',
        ]);

        RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => 1,
            'sub_cpmk' => 'Memahami',
            'materi' => 'Pengantar',
            'metode' => 'Ceramah',
            'pengalaman_belajar' => 'Diskusi',
            'indikator' => 'Tepat',
            'bobot' => '5',
        ]);

        RpsPenilaian::create([
            'rps_id' => $rps->id,
            'tugas' => 40,
            'quiz' => 10,
            'uts' => 25,
            'uas' => 25,
        ]);

        Notification::fake();

        $this->actingAs($data['userDosen'])
            ->patch(route('rps.ajukan', $rps->id))
            ->assertSessionHas('success');

        Notification::assertSentTo($userKaprodi, RpsDiajukan::class);
    }

    public function test_mark_as_read_menandai_notifikasi_terbaca(): void
    {
        $data = $this->buatData();

        $tugas = LmsTugas::create([
            'pengampu_id' => $data['pengampu']->id,
            'judul' => 'Tugas 1',
            'instruksi' => 'Kerjakan',
            'deadline' => now()->addDays(7),
            'bobot_nilai' => 20,
        ]);

        $data['userMhs']->notify(new TugasBaru($data['pengampu'], $tugas));

        $notification = $data['userMhs']->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $this->actingAs($data['userMhs'])
            ->get(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
