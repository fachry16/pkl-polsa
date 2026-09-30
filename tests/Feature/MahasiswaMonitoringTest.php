<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $kaprodi;

    protected User $kaprodiLain;

    protected Mahasiswa $mhs1;

    protected Mahasiswa $mhs2LainProdi;

    protected Mahasiswa $mhs3Do;

    protected Pengampu $pengampu1;

    protected function setUp(): void
    {
        parent::setUp();

        $prodiA = ProgramStudi::create([
            'kode_prodi' => '11',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $prodiB = ProgramStudi::create([
            'kode_prodi' => 'MB',
            'nama_prodi' => 'Manajemen Bisnis',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $ta = TahunAkademik::create([
            'tahun' => 2026,
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodiA->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TI',
            'status' => 'Aktif',
        ]);

        $mk1 = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'AB2101',
            'nama' => 'MATEMATIKA BISNIS',
            'sks_teori' => 2,
            'sks_praktikum' => 0,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $kaprodiUser = User::create([
            'name' => 'Kaprodi TI',
            'email' => 'kaprodi_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        $kaprodiLainUser = User::create([
            'name' => 'Kaprodi MB',
            'email' => 'kaprodi_lain@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $kaprodiUser->id,
            'program_studi_id' => $prodiA->id,
            'nidn' => '0087654321',
            'jabatan' => 'Kaprodi',
        ]);

        Dosen::create([
            'user_id' => $kaprodiLainUser->id,
            'program_studi_id' => $prodiB->id,
            'nidn' => '0076543210',
            'jabatan' => 'Kaprodi',
        ]);

        $dosen = Dosen::create([
            'user_id' => $adminUser->id,
            'program_studi_id' => $prodiA->id,
            'nidn' => '0099887766',
            'jabatan' => 'Dosen',
        ]);

        $mhsUser1 = User::create([
            'name' => 'Mahasiswa Satu',
            'email' => 'mhs1_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs1 = Mahasiswa::create([
            'user_id' => $mhsUser1->id,
            'nim' => '2024001',
            'nama' => 'Mahasiswa Satu',
            'program_studi_id' => $prodiA->id,
            'angkatan' => 2024,
        ]);

        $mhsUser2 = User::create([
            'name' => 'Mahasiswa Dua',
            'email' => 'mhs2_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs2 = Mahasiswa::create([
            'user_id' => $mhsUser2->id,
            'nim' => '2024002',
            'nama' => 'Mahasiswa Dua',
            'program_studi_id' => $prodiB->id,
            'angkatan' => 2024,
        ]);

        $mhsUser3 = User::create([
            'name' => 'Mahasiswa Tiga',
            'email' => 'mhs3_monitor@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs3 = Mahasiswa::create([
            'user_id' => $mhsUser3->id,
            'nim' => '2024003',
            'nama' => 'Mahasiswa Tiga',
            'program_studi_id' => $prodiA->id,
            'angkatan' => 2024,
            'status' => 'DO',
        ]);

        $pengampu1 = Pengampu::create([
            'mata_kuliah_id' => $mk1->id,
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu1->mahasiswas()->attach($mhs1->id);

        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampu1->id,
            'mahasiswa_id' => $mhs1->id,
            'komponen' => 'akhir',
            'nilai' => 77,
        ]);

        $this->admin = $adminUser;
        $this->kaprodi = $kaprodiUser;
        $this->kaprodiLain = $kaprodiLainUser;
        $this->mhs1 = $mhs1;
        $this->mhs2LainProdi = $mhs2;
        $this->mhs3Do = $mhs3;
        $this->pengampu1 = $pengampu1;
    }

    public function test_kaprodi_melihat_daftar_mahasiswa_prodi_sendiri_saja(): void
    {
        $this->withSession(['sidebar_show_kaprodi' => true])
            ->actingAs($this->kaprodi)
            ->get(route('mahasiswa.index'))
            ->assertOk()
            ->assertSee('Mahasiswa Satu')
            ->assertDontSee('Mahasiswa Dua')
            ->assertSee('Monitoring Mahasiswa')
            ->assertDontSee('Tambah Mahasiswa')
            ->assertDontSee('Import Data Excel / CSV')
            ->assertDontSee('Edit')
            ->assertDontSee('Hapus');
    }

    public function test_admin_melihat_semua_mahasiswa_dengan_tombol_crud(): void
    {
        $this->actingAs($this->admin)
            ->get(route('mahasiswa.index'))
            ->assertOk()
            ->assertSee('Mahasiswa Satu')
            ->assertSee('Mahasiswa Dua')
            ->assertSee('Tambah Mahasiswa')
            ->assertSee('Import Data Excel / CSV')
            ->assertSee('Edit')
            ->assertSee('Hapus');
    }

    public function test_kaprodi_mengakses_halaman_detail_mahasiswa_prodi_sendiri(): void
    {
        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.nilai', $this->mhs1->id))
            ->assertOk()
            ->assertSee('KHS')
            ->assertSee('MATEMATIKA BISNIS');

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.status', $this->mhs1->id))
            ->assertOk()
            ->assertSee('Status — Mahasiswa Satu')
            ->assertSee('Chart Status')
            ->assertSee('Aktif');

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.status', $this->mhs3Do->id))
            ->assertOk()
            ->assertSee('DO');

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertOk()
            ->assertSee('Transkrip')
            ->assertSee('MATEMATIKA BISNIS');
    }

    public function test_kaprodi_tidak_bisa_akses_mahasiswa_prodi_lain(): void
    {
        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.nilai', $this->mhs2LainProdi->id))
            ->assertForbidden();

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.status', $this->mhs2LainProdi->id))
            ->assertForbidden();

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.transkrip', $this->mhs2LainProdi->id))
            ->assertForbidden();
    }

    public function test_kaprodi_sendiri_tidak_boleh_akses_prodi_yang_sama_dengan_mahasiswa_prodi_lain(): void
    {
        $this->actingAs($this->kaprodiLain)
            ->get(route('mahasiswa.index'))
            ->assertOk()
            ->assertSee('Mahasiswa Dua')
            ->assertDontSee('Mahasiswa Satu');
    }

    public function test_dosen_biasa_dan_mahasiswa_tidak_boleh_akses(): void
    {
        $dosen = User::create([
            'name' => 'Dosen Biasa',
            'email' => 'dosen_biasa@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        $this->actingAs($dosen)
            ->get(route('mahasiswa.index'))
            ->assertForbidden();

        $this->actingAs($dosen)
            ->get(route('mahasiswa.nilai', $this->mhs1->id))
            ->assertForbidden();

        $mahasiswaUser = User::create([
            'name' => 'Akun Mahasiswa',
            'email' => 'mhs_akun@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $this->actingAs($mahasiswaUser)
            ->get(route('mahasiswa.index'))
            ->assertForbidden();
    }

    public function test_filter_status(): void
    {
        $this->actingAs($this->admin)
            ->get(route('mahasiswa.index', ['status' => 'DO']))
            ->assertOk()
            ->assertSee('Mahasiswa Tiga')
            ->assertDontSee('Mahasiswa Satu')
            ->assertDontSee('Mahasiswa Dua');

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.index', ['status' => 'DO']))
            ->assertOk()
            ->assertSee('Mahasiswa Tiga')
            ->assertDontSee('Mahasiswa Satu');
    }

    public function test_status_non_aktif_tersimpan_filter_dan_tampil_di_daftar(): void
    {
        $mhsUser = User::create([
            'name' => 'Mahasiswa Non Aktif',
            'email' => 'mhs_nonaktif@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $mhsUser->id,
            'nim' => '2024004',
            'nama' => 'Mahasiswa Non Aktif',
            'program_studi_id' => $this->mhs1->program_studi_id,
            'angkatan' => 2024,
            'status' => 'Non Aktif',
        ]);

        $this->actingAs($this->admin)
            ->get(route('mahasiswa.index', ['status' => 'Non Aktif']))
            ->assertOk()
            ->assertSee('Mahasiswa Non Aktif')
            ->assertDontSee('Mahasiswa Satu');
    }

    public function test_khs_export_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('mahasiswa.khs.export', $this->mhs1->id));

        $response->assertOk();
        $this->assertStringStartsWith('application/pdf', $response->headers->get('content-type'));

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.khs.export', $this->mhs2LainProdi->id))
            ->assertForbidden();
    }
}
