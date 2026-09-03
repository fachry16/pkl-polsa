<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\KhsApproval;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Notifications\KhsDisetujuiNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class KhsCetakTest extends TestCase
{
    use RefreshDatabase;

    private function buatData(): array
    {
        $prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '110 SKS',
            'deskripsi' => 'Kurikulum TI',
            'status' => 'Aktif',
        ]);

        $tahun = TahunAkademik::create(['tahun' => 2024, 'semester' => 'Ganjil', 'is_active' => true]);

        $userAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $userKaprodi = User::create([
            'name' => 'Kaprodi TI',
            'email' => 'kaprodi@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $kaprodi = Dosen::create([
            'user_id' => $userKaprodi->id,
            'program_studi_id' => $prodi->id,
            'nidn' => '1100',
            'jabatan' => 'Kaprodi',
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
            'nidn' => '1200',
            'jabatan' => 'Dosen',
        ]);

        $userMhs = User::create([
            'name' => 'Budi Santoso',
            'email' => 'mhs@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '32240001',
            'nama' => 'Budi Santoso',
            'email' => 'mhs@test.dev',
            'program_studi_id' => $prodi->id,
            'angkatan' => 2024,
            'jenis_kelas' => 'reguler',
        ]);

        $mkWeb = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI101',
            'nama' => 'Pemrograman Web',
            'sks_teori' => 3,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $mkDb = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'TI102',
            'nama' => 'Basis Data',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $pengampuWeb = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mkWeb->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampuDb = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mkDb->id,
            'tahun_akademik_id' => $tahun->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $mahasiswa->pengampus()->attach([$pengampuWeb->id, $pengampuDb->id]);

        // Simpan nilai akhir di LMS
        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampuWeb->id,
            'mahasiswa_id' => $mahasiswa->id,
            'komponen' => 'akhir',
            'nilai' => 85.00, // Huruf A, Mutu 4.00 -> 4 sks * 4 = 16
        ]);

        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampuDb->id,
            'mahasiswa_id' => $mahasiswa->id,
            'komponen' => 'akhir',
            'nilai' => 76.00, // Huruf B+, Mutu 3.50 -> 3 sks * 3.5 = 10.5
        ]);

        return compact(
            'prodi',
            'tahun',
            'userAdmin',
            'userKaprodi',
            'kaprodi',
            'userDosen',
            'dosen',
            'userMhs',
            'mahasiswa',
            'pengampuWeb',
            'pengampuDb',
            'mkWeb',
            'mkDb',
        );
    }

    public function test_kaprodi_dapat_mengakses_panel_verifikasi_khs(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userKaprodi'])
            ->get(route('khs.index', ['tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertSee('Persetujuan &amp; Verifikasi KHS', false)
            ->assertSee('Budi Santoso')
            ->assertSee('Menunggu');
    }

    public function test_kaprodi_dapat_menyetujui_khs_mahasiswa_dan_mengirim_notifikasi(): void
    {
        Notification::fake();
        $data = $this->buatData();

        $response = $this->actingAs($data['userKaprodi'])
            ->post(route('khs.approve', [$data['mahasiswa']->id, $data['tahun']->id]));

        $response->assertRedirect();

        $this->assertDatabaseHas('khs_approvals', [
            'mahasiswa_id' => $data['mahasiswa']->id,
            'tahun_akademik_id' => $data['tahun']->id,
            'status' => 'disetujui',
            'approved_by' => $data['userKaprodi']->id,
        ]);

        Notification::assertSentTo(
            $data['userMhs'],
            KhsDisetujuiNotification::class
        );
    }

    public function test_kaprodi_dapat_membatalkan_persetujuan_khs(): void
    {
        $data = $this->buatData();

        KhsApproval::create([
            'mahasiswa_id' => $data['mahasiswa']->id,
            'tahun_akademik_id' => $data['tahun']->id,
            'status' => 'disetujui',
            'approved_by' => $data['userKaprodi']->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($data['userKaprodi'])
            ->post(route('khs.unapprove', [$data['mahasiswa']->id, $data['tahun']->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('khs_approvals', [
            'mahasiswa_id' => $data['mahasiswa']->id,
            'tahun_akademik_id' => $data['tahun']->id,
            'status' => 'menunggu',
            'approved_by' => null,
        ]);
    }

    public function test_bulk_approval_khs_oleh_kaprodi(): void
    {
        $data = $this->buatData();

        $this->actingAs($data['userKaprodi'])
            ->post(route('khs.approve-all'), [
                'program_studi_id' => $data['prodi']->id,
                'tahun_akademik_id' => $data['tahun']->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('khs_approvals', [
            'mahasiswa_id' => $data['mahasiswa']->id,
            'tahun_akademik_id' => $data['tahun']->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_mahasiswa_belum_disetujui_melihat_notice_dan_ditolak_unduh_pdf(): void
    {
        $data = $this->buatData();

        // Mahasiswa buka halaman KHS web: melihat notice menunggu persetujuan
        $this->actingAs($data['userMhs'])
            ->get(route('khs.self'))
            ->assertOk()
            ->assertSee('KHS Belum Tersedia', false);

        // Mahasiswa coba unduh PDF sebelum disetujui: ditolak dengan pesan error
        $this->actingAs($data['userMhs'])
            ->get(route('khs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_mahasiswa_setelah_disetujui_dapat_mengunduh_pdf(): void
    {
        $data = $this->buatData();

        KhsApproval::create([
            'mahasiswa_id' => $data['mahasiswa']->id,
            'tahun_akademik_id' => $data['tahun']->id,
            'status' => 'disetujui',
            'approved_by' => $data['userKaprodi']->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($data['userMhs'])
            ->get(route('khs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_dan_kaprodi_tetap_dapat_mengunduh_pdf_draft_untuk_keperluan_verifikasi(): void
    {
        $data = $this->buatData();

        // Admin bisa unduh PDF kapanpun
        $this->actingAs($data['userAdmin'])
            ->get(route('khs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        // Kaprodi bisa unduh PDF kapanpun
        $this->actingAs($data['userKaprodi'])
            ->get(route('khs.cetak-pdf', [$data['mahasiswa']->id, 'tahun_akademik_id' => $data['tahun']->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
