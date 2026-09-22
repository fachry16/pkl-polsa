<?php

namespace Tests\Feature;

use App\Http\Controllers\TranskripController;
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

class TranskripTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $kaprodi;

    protected User $mhsUser;

    protected Mahasiswa $mhs1;

    protected Mahasiswa $mhs2LainProdi;

    protected Pengampu $pengampu1;

    protected Pengampu $pengampu2;

    protected TahunAkademik $ta;

    protected Kurikulum $kurikulum;

    protected Dosen $dosen;

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

        $mk2 = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'AB2105',
            'nama' => 'PENGANTAR BISNIS',
            'sks_teori' => 2,
            'sks_praktikum' => 0,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $mk3 = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'AB2108',
            'nama' => 'PERPAJAKAN',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 2,
            'jenis' => 'Wajib',
        ]);

        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin_transkrip@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $kaprodiUser = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi_transkrip@test.dev',
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

        $mhsUser1 = User::create([
            'name' => 'Mahasiswa Satu',
            'email' => 'mhs1_transkrip@test.dev',
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
            'email' => 'mhs2_transkrip@test.dev',
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

        $dosen = Dosen::create([
            'user_id' => $adminUser->id,
            'program_studi_id' => $prodiA->id,
            'nidn' => '0099887766',
            'jabatan' => 'Dosen',
        ]);

        $pengampu1 = Pengampu::create([
            'mata_kuliah_id' => $mk1->id,
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu2 = Pengampu::create([
            'mata_kuliah_id' => $mk2->id,
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu3 = Pengampu::create([
            'mata_kuliah_id' => $mk3->id,
            'dosen_id' => $dosen->id,
            'tahun_akademik_id' => $ta->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu1->mahasiswas()->attach($mhs1->id);
        $pengampu2->mahasiswas()->attach($mhs1->id);
        $pengampu3->mahasiswas()->attach($mhs1->id);

        $this->insertNilai($pengampu1, $mhs1, 77);
        $this->insertNilai($pengampu2, $mhs1, 74);

        $this->admin = $adminUser;
        $this->kaprodi = $kaprodiUser;
        $this->mhsUser = $mhsUser1;
        $this->mhs1 = $mhs1;
        $this->mhs2LainProdi = $mhs2;
        $this->pengampu1 = $pengampu1;
        $this->pengampu2 = $pengampu2;
        $this->ta = $ta;
        $this->kurikulum = $kurikulum;
        $this->dosen = $dosen;
    }

    protected function insertNilai(Pengampu $pengampu, Mahasiswa $mahasiswa, float $nilai): void
    {
        LmsNilaiMahasiswa::create([
            'pengampu_id' => $pengampu->id,
            'mahasiswa_id' => $mahasiswa->id,
            'komponen' => 'akhir',
            'nilai' => $nilai,
        ]);
    }

    public function test_konversi_huruf_dan_indeks_sesuai_spesifikasi(): void
    {
        $this->assertSame('A', TranskripController::konversiHuruf(82));
        $this->assertSame(4.00, TranskripController::konversiIndeks(82));
        $this->assertSame('A-', TranskripController::konversiHuruf(77));
        $this->assertSame(3.70, TranskripController::konversiIndeks(77));
        $this->assertSame('B+', TranskripController::konversiHuruf(74));
        $this->assertSame(3.40, TranskripController::konversiIndeks(74));
        $this->assertSame('B-', TranskripController::konversiHuruf(60));
        $this->assertSame(2.70, TranskripController::konversiIndeks(60));
        $this->assertSame('C+', TranskripController::konversiHuruf(55));
        $this->assertSame(2.40, TranskripController::konversiIndeks(55));
        $this->assertSame('C', TranskripController::konversiHuruf(52));
        $this->assertSame(2.00, TranskripController::konversiIndeks(52));
        $this->assertSame('D', TranskripController::konversiHuruf(45));
        $this->assertSame(1.00, TranskripController::konversiIndeks(45));
        $this->assertSame('E', TranskripController::konversiHuruf(30));
        $this->assertSame(0.00, TranskripController::konversiIndeks(30));
        $this->assertNull(TranskripController::konversiHuruf(null));
    }

    public function test_admin_melihat_transkrip_detail_mahasiswa(): void
    {
        $this->actingAs($this->admin)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertOk()
            ->assertSee('Transkrip Akademik')
            ->assertSee('MATEMATIKA BISNIS')
            ->assertSee('PENGANTAR BISNIS')
            ->assertSee('77.00')
            ->assertSee('74.00')
            ->assertSee('3.25')
            ->assertSee('7.00');
    }

    public function test_transkrip_mengelompokkan_per_semester(): void
    {
        $taGenap = TahunAkademik::create([
            'tahun' => 2026,
            'semester' => 'Genap',
            'is_active' => false,
        ]);

        $mk4 = MataKuliah::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode' => 'AB2110',
            'nama' => 'PENGANTAR MANAJEMEN',
            'sks_teori' => 2,
            'sks_praktikum' => 0,
            'semester' => 2,
            'jenis' => 'Wajib',
        ]);

        $pengampu4 = Pengampu::create([
            'mata_kuliah_id' => $mk4->id,
            'dosen_id' => $this->dosen->id,
            'tahun_akademik_id' => $taGenap->id,
            'semester_akademik' => 'Genap',
            'kelas' => 'A',
        ]);

        $pengampu4->mahasiswas()->attach($this->mhs1->id);
        $this->insertNilai($pengampu4, $this->mhs1, 60);

        $this->actingAs($this->admin)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertOk()
            ->assertSee('2026 Ganjil')
            ->assertSee('2026 Genap')
            ->assertSee('PENGANTAR MANAJEMEN')
            ->assertSee('MATEMATIKA BISNIS')
            ->assertSee('2.83');
    }

    public function test_ipk_dan_total_dihitung_dari_mk_yang_sudah_bernilai(): void
    {
        $this->actingAs($this->admin)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertOk()
            ->assertSee('3.25');
    }

    public function test_mahasiswa_hanya_bisa_melihat_transkrip_sendiri(): void
    {
        $this->actingAs($this->mhsUser)
            ->get(route('transkrip.saya'))
            ->assertOk()
            ->assertSee('MATEMATIKA BISNIS');

        $this->actingAs($this->mhsUser)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertForbidden();
    }

    public function test_mahasiswa_melihat_halaman_identitas_dengan_lihat_detail(): void
    {
        $response = $this->actingAs($this->mhsUser)
            ->get(route('mahasiswa.identitas'))
            ->assertOk()
            ->assertSee('Identitas — Mahasiswa Satu')
            ->assertSee('2024001')
            ->assertSee('Lihat Detail')
            ->assertSee('IPK Kumulatif')
            ->assertSee('MATEMATIKA BISNIS')
            ->assertSee('bagian akademik')
            ->assertSee('Transkrip')
            ->assertDontSee('Transkrip Saya');

        $response->assertSee('Identitas', false);
    }

    public function test_halaman_identitas_hanya_untuk_mahasiswa(): void
    {
        $this->actingAs($this->admin)
            ->get(route('mahasiswa.identitas'))
            ->assertForbidden();
    }

    public function test_dosen_biasa_tidak_boleh_mengakses(): void
    {
        $dosen = User::create([
            'name' => 'Dosen Biasa',
            'email' => 'dosenbiasa_transkrip@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $this->actingAs($dosen)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertForbidden();
    }

    public function test_kaprodi_hanya_melihat_mahasiswa_prodi_sendiri(): void
    {
        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.transkrip', $this->mhs1->id))
            ->assertOk()
            ->assertSee('MATEMATIKA BISNIS');

        $this->actingAs($this->kaprodi)
            ->get(route('mahasiswa.transkrip', $this->mhs2LainProdi->id))
            ->assertForbidden();
    }

    public function test_ekspor_pdf_transkrip(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('mahasiswa.transkrip.export', $this->mhs1->id));

        $response->assertOk();
        $this->assertStringStartsWith('application/pdf', $response->headers->get('content-type'));
    }
}
