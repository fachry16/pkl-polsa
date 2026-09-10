<?php

namespace Tests\Feature;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsPengumuman;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\Rps;
use App\Models\RpsBentukEvaluasi;
use App\Models\RpsPertemuan;
use App\Models\RpsTugas;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $kaprodi;

    protected User $dosen;

    protected User $mahasiswa;

    protected ProgramStudi $prodi;

    protected TahunAkademik $tahunAkademik;

    protected Kurikulum $kurikulum;

    protected MataKuliah $mataKuliah;

    protected Rps $rps;

    protected Pengampu $pengampu;

    protected Dosen $dosenModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prodi = ProgramStudi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'tahun' => 2026,
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->kurikulum = Kurikulum::create([
            'program_studi_id' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum 2024',
            'tahun_berlaku' => 2024,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum TI',
            'status' => 'Aktif',
        ]);

        $this->mataKuliah = MataKuliah::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode' => 'MK001',
            'nama' => 'Mata Kuliah Test',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin_crud@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $this->dosen = User::create([
            'name' => 'Dosen',
            'email' => 'dosen_crud@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $this->dosenModel = Dosen::create([
            'user_id' => $this->dosen->id,
            'program_studi_id' => $this->prodi->id,
            'nidn' => '0012345678',
            'jabatan' => 'Dosen',
        ]);

        $this->kaprodi = User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi_crud@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $this->kaprodi->id,
            'program_studi_id' => $this->prodi->id,
            'nidn' => '0087654321',
            'jabatan' => 'Kaprodi',
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Mahasiswa',
            'email' => 'mhs_crud@test.dev',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $this->mahasiswa->id,
            'nim' => '2024001',
            'nama' => 'Mahasiswa Test',
            'email' => 'mhs_crud@test.dev',
            'program_studi_id' => $this->prodi->id,
            'kurikulum_id' => $this->kurikulum->id,
            'semester' => 3,
            'angkatan' => 2024,
        ]);

        $this->rps = Rps::create([
            'mata_kuliah_id' => $this->mataKuliah->id,
            'semester' => 1,
            'dosen_pengampu' => 'Dosen Test',
            'status' => 'Draft',
        ]);

        $this->pengampu = Pengampu::create([
            'mata_kuliah_id' => $this->mataKuliah->id,
            'dosen_id' => $this->dosenModel->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);
    }

    // ── Master Data ──────────────────────────────────────────

    public function test_program_studi_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('program-studi.index'))->assertOk();
        $this->get(route('program-studi.create'))->assertOk();

        $this->post(route('program-studi.store'), [
            'kode_prodi' => 'TK',
            'nama_prodi' => 'Teknik Komputer',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ])->assertRedirect();
        $this->assertDatabaseHas('program_studis', ['kode_prodi' => 'TK']);

        $ps = ProgramStudi::where('kode_prodi', 'TK')->first();
        $this->get(route('program-studi.edit', $ps))->assertOk();

        $this->put(route('program-studi.update', $ps), [
            'kode_prodi' => 'TK',
            'nama_prodi' => 'Teknik Komputer Updated',
            'jenjang' => 'D3',
            'akreditasi' => 'Baik',
        ])->assertRedirect();
        $this->assertDatabaseHas('program_studis', ['id' => $ps->id, 'nama_prodi' => 'Teknik Komputer Updated']);

        $this->delete(route('program-studi.destroy', $ps))->assertRedirect();
        $this->assertDatabaseMissing('program_studis', ['id' => $ps->id]);
    }

    public function test_dosen_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('dosen.index'))->assertOk();
        $this->get(route('dosen.create'))->assertOk();

        $this->post(route('dosen.store'), [
            'nidn' => '0099887766',
            'name' => 'Dosen Baru',
            'email' => 'dosenbaru@test.com',
            'program_studi_id' => $this->prodi->id,
            'jabatan' => 'Dosen',
        ])->assertRedirect();

        $dosen = Dosen::where('nidn', '0099887766')->first();
        $this->assertNotNull($dosen);

        $this->get(route('dosen.show', $dosen))->assertOk();
        $this->get(route('dosen.edit', $dosen))->assertOk();

        $this->put(route('dosen.update', $dosen), [
            'nidn' => $dosen->nidn,
            'name' => 'Dosen Updated',
            'email' => 'dosenbaru@test.com',
            'program_studi_id' => $this->prodi->id,
            'jabatan' => 'Dosen',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $dosen->user_id, 'name' => 'Dosen Updated']);
    }

    public function test_mahasiswa_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('mahasiswa.index'))->assertOk();
        $this->get(route('mahasiswa.create'))->assertOk();

        $this->post(route('mahasiswa.store'), [
            'nim' => '2024099',
            'nama' => 'MHS Baru',
            'program_studi_id' => $this->prodi->id,
            'angkatan' => 2024,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'semester' => 3,
        ])->assertRedirect();

        $mhs = Mahasiswa::where('nim', '2024099')->first();
        $this->assertNotNull($mhs);

        $this->get(route('mahasiswa.show', $mhs))->assertOk();
        $this->get(route('mahasiswa.edit', $mhs))->assertOk();

        $this->put(route('mahasiswa.update', $mhs), [
            'nim' => $mhs->nim,
            'nama' => 'MHS Updated',
            'program_studi_id' => $this->prodi->id,
            'angkatan' => 2024,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'semester' => 3,
        ])->assertRedirect();
        $this->assertDatabaseHas('mahasiswas', ['id' => $mhs->id, 'nama' => 'MHS Updated']);
    }

    public function test_user_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('users.index'))->assertOk();
        $this->get(route('users.create'))->assertOk();

        $this->post(route('users.store'), [
            'name' => 'User Baru',
            'email' => 'userbaru@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'dosen',
        ])->assertRedirect();

        $user = User::where('email', 'userbaru@test.com')->first();
        $this->assertNotNull($user);

        $this->get(route('users.edit', $user))->assertOk();

        $this->put(route('users.update', $user), [
            'name' => 'User Updated',
            'email' => $user->email,
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'User Updated']);
    }

    public function test_role_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('roles.index'))->assertOk();
        $this->get(route('roles.create'))->assertOk();

        $this->post(route('roles.store'), ['nama' => 'Editor', 'kode' => 'editor', 'deskripsi' => 'Editor role'])->assertRedirect();
        $role = Role::where('kode', 'editor')->first();
        $this->assertNotNull($role);

        $this->get(route('roles.edit', $role))->assertOk();

        $this->put(route('roles.update', $role), ['nama' => 'Editor', 'kode' => 'editor', 'deskripsi' => 'Updated'])->assertRedirect();
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'deskripsi' => 'Updated']);
    }

    public function test_tahun_akademik_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('tahun-akademik.index'))->assertOk();
        $this->get(route('tahun-akademik.create'))->assertOk();

        $this->post(route('tahun-akademik.store'), [
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
        ])->assertRedirect();

        $ta = TahunAkademik::where('tahun', '2025/2026')->first();
        $this->assertNotNull($ta);

        $this->get(route('tahun-akademik.show', $ta))->assertRedirect();
        $this->get(route('tahun-akademik.edit', $ta))->assertOk();

        $this->put(route('tahun-akademik.update', $ta), [
            'tahun' => '2025/2026',
            'semester' => 'Genap',
        ])->assertRedirect();
        $this->assertDatabaseHas('tahun_akademiks', ['id' => $ta->id, 'semester' => 'Genap']);

        $this->delete(route('tahun-akademik.destroy', $ta))->assertRedirect();
    }

    public function test_pengampu_crud(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('pengampu.index'))->assertOk();
        $this->get(route('pengampu.create'))->assertOk();

        $this->post(route('pengampu.store'), [
            'mata_kuliah_id' => $this->mataKuliah->id,
            'dosen_id' => $this->dosenModel->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'B',
        ])->assertRedirect();
        $this->assertDatabaseHas('pengampus', ['kelas' => 'B', 'mata_kuliah_id' => $this->mataKuliah->id]);
    }

    // ── Kurikulum ──────────────────────────────────────────────

    public function test_kurikulum_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('program-studi.kurikulum', $this->prodi))->assertOk();
        $this->get(route('kurikulum.create'))->assertOk();

        $this->post(route('kurikulum.store'), [
            'nama_kurikulum' => 'Kurikulum Baru 2025',
            'program_studi_id' => $this->prodi->id,
            'tahun_berlaku' => 2025,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Kurikulum Baru',
            'status' => 'Draft',
        ])->assertRedirect();

        $k = Kurikulum::where('nama_kurikulum', 'Kurikulum Baru 2025')->first();
        $this->assertNotNull($k);

        $this->get(route('kurikulum.detail', $k))->assertOk();
        $this->get(route('kurikulum.edit', $k))->assertOk();

        $this->put(route('kurikulum.update', $k), [
            'nama_kurikulum' => 'Kurikulum Updated',
            'program_studi_id' => $this->prodi->id,
            'tahun_berlaku' => 2025,
            'beban_studi' => '144 SKS',
            'deskripsi' => 'Updated',
            'status' => 'Draft',
        ])->assertRedirect();
        $this->assertDatabaseHas('kurikulums', ['id' => $k->id, 'nama_kurikulum' => 'Kurikulum Updated']);
    }

    public function test_cpl_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.cpl.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.cpl.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.cpl.store', $this->kurikulum), [
            'kode_cpl' => 'CPL-01',
            'deskripsi' => 'CPL Test',
        ])->assertRedirect();

        $cpl = Cpl::where('kode_cpl', 'CPL-01')->first();
        $this->assertNotNull($cpl);

        $this->get(route('kurikulum.cpl.edit', [$this->kurikulum, $cpl]))->assertOk();

        $this->put(route('kurikulum.cpl.update', [$this->kurikulum, $cpl]), [
            'kode_cpl' => 'CPL-01',
            'deskripsi' => 'CPL Updated',
        ])->assertRedirect();
    }

    public function test_cpmk_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.cpmk.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.cpmk.create', $this->kurikulum))->assertOk();

        $cpl = Cpl::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode_cpl' => 'CPL-CPMK',
            'deskripsi' => 'CPL for CPMK',
        ]);

        $this->post(route('kurikulum.cpmk.store', $this->kurikulum), [
            'kode_cpmk' => 'CPMK-01',
            'deskripsi' => 'CPMK Test',
            'cpl_id' => $cpl->id,
        ])->assertRedirect();

        $cpmk = Cpmk::where('kode_cpmk', 'CPMK-01')->first();
        $this->assertNotNull($cpmk);

        $this->get(route('kurikulum.cpmk.edit', [$this->kurikulum, $cpmk]))->assertOk();

        $this->put(route('kurikulum.cpmk.update', [$this->kurikulum, $cpmk]), [
            'kode_cpmk' => 'CPMK-01',
            'deskripsi' => 'CPMK Updated',
            'cpl_id' => $cpl->id,
        ])->assertRedirect();
    }

    public function test_mata_kuliah_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.mata-kuliah.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.mata-kuliah.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.mata-kuliah.store', $this->kurikulum), [
            'kode' => 'MK002',
            'nama' => 'MK Baru',
            'sks_teori' => 3,
            'sks_praktikum' => 0,
            'semester' => 2,
            'jenis' => 'Wajib',
        ])->assertRedirect();

        $mk = MataKuliah::where('kode', 'MK002')->first();
        $this->assertNotNull($mk);

        $this->get(route('kurikulum.mata-kuliah.edit', [$this->kurikulum, $mk]))->assertOk();

        $this->put(route('kurikulum.mata-kuliah.update', [$this->kurikulum, $mk]), [
            'kode' => 'MK002',
            'nama' => 'MK Updated',
            'sks_teori' => 4,
            'sks_praktikum' => 0,
            'semester' => 2,
            'jenis' => 'Wajib',
        ])->assertRedirect();
    }

    public function test_bahan_kajian_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.bahan-kajian.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.bahan-kajian.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.bahan-kajian.store', $this->kurikulum), [
            'nama_bahan_kajian' => 'Bahan Kajian Test',
        ])->assertRedirect();
    }

    public function test_profil_lulusan_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.profil-lulusan.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.profil-lulusan.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.profil-lulusan.store', $this->kurikulum), [
            'nama_profil_lulusan' => 'Profil Lulusan Test',
            'deskripsi' => 'Deskripsi PL',
        ])->assertRedirect();
    }

    public function test_metode_bobot_penilaian_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.metode-bobot-penilaian.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.metode-bobot-penilaian.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.metode-bobot-penilaian.store', $this->kurikulum), [
            'komponen' => 'Tugas',
            'bobot' => 30,
        ])->assertRedirect();
    }

    public function test_rumusan_nilai_akhir_mk_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.rumusan-nilai-akhir-mk.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.rumusan-nilai-akhir-mk.create', $this->kurikulum))->assertOk();

        $cpl = Cpl::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode_cpl' => 'CPL-RN',
            'deskripsi' => 'CPL for Rumusan',
        ]);
        $cpmk = Cpmk::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode_cpmk' => 'CPMK-RN',
            'deskripsi' => 'CPMK for Rumusan',
            'cpl_id' => $cpl->id,
        ]);

        $this->post(route('kurikulum.rumusan-nilai-akhir-mk.store', $this->kurikulum), [
            'cpmk_id' => $cpmk->id,
            'bobot' => 100,
        ])->assertRedirect();
    }

    public function test_rumusan_nilai_akhir_cpl_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.rumusan-nilai-akhir-cpl.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.rumusan-nilai-akhir-cpl.create', $this->kurikulum))->assertOk();

        $cpl = Cpl::create([
            'kurikulum_id' => $this->kurikulum->id,
            'kode_cpl' => 'CPL-RNC',
            'deskripsi' => 'CPL for Rumusan CPL',
        ]);

        $this->post(route('kurikulum.rumusan-nilai-akhir-cpl.store', $this->kurikulum), [
            'cpl_id' => $cpl->id,
            'bobot' => 100,
        ])->assertRedirect();
    }

    public function test_evaluasi_kurikulum_crud(): void
    {
        $this->actingAs($this->kaprodi);
        $this->get(route('kurikulum.evaluasi-kurikulum.index', $this->kurikulum))->assertOk();
        $this->get(route('kurikulum.evaluasi-kurikulum.create', $this->kurikulum))->assertOk();

        $this->post(route('kurikulum.evaluasi-kurikulum.store', $this->kurikulum), [
            'judul' => 'Evaluasi Test',
            'deskripsi' => 'Deskripsi Evaluasi',
        ])->assertRedirect();
    }

    // ── RPS ──────────────────────────────────────────────────

    public function test_rps_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('mata-kuliah.rps.index', $this->mataKuliah))->assertOk();
        $this->get(route('mata-kuliah.rps.edit', [$this->mataKuliah, $this->rps]))->assertOk();

        $this->put(route('mata-kuliah.rps.update', [$this->mataKuliah, $this->rps]), [
            'semester' => 1,
            'dosen_pengampu' => 'Dosen Updated',
            'deskripsi_mata_kuliah' => 'Deskripsi Updated',
        ])->assertRedirect();
    }

    public function test_rps_pertemuan_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('rps.pertemuan.index', $this->rps))->assertOk();
        $this->get(route('rps.pertemuan.create', $this->rps))->assertOk();

        $this->post(route('rps.pertemuan.store', $this->rps), [
            'minggu' => 1,
            'sub_cpmk' => 'Sub CPMK Test',
            'materi' => 'Materi Test',
            'metode' => 'Kuliah',
            'pengalaman_belajar' => 'Pengalaman Belajar Test',
            'indikator' => 'Indikator Test',
            'bobot' => 10,
        ])->assertRedirect();

        $pertemuan = RpsPertemuan::where('rps_id', $this->rps->id)->first();
        $this->assertNotNull($pertemuan);

        $this->get(route('rps.pertemuan.edit', [$this->rps, $pertemuan]))->assertOk();

        $this->put(route('rps.pertemuan.update', [$this->rps, $pertemuan]), [
            'minggu' => 1,
            'sub_cpmk' => 'Sub CPMK Updated',
            'materi' => 'Materi Updated',
            'metode' => 'Praktikum',
            'pengalaman_belajar' => 'Pengalaman Belajar Updated',
            'indikator' => 'Indikator Updated',
            'bobot' => 20,
        ])->assertRedirect();
    }

    public function test_rps_bentuk_evaluasi_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('rps.bentuk-evaluasi.index', $this->rps))->assertOk();
        $this->get(route('rps.bentuk-evaluasi.create', $this->rps))->assertOk();

        $this->post(route('rps.bentuk-evaluasi.store', $this->rps), [
            'bentuk_evaluasi' => 'Tugas',
            'bobot' => 30,
        ])->assertRedirect();

        $be = RpsBentukEvaluasi::where('rps_id', $this->rps->id)->first();
        $this->assertNotNull($be);

        $this->get(route('rps.bentuk-evaluasi.edit', [$this->rps, $be]))->assertOk();

        $this->put(route('rps.bentuk-evaluasi.update', [$this->rps, $be]), [
            'bentuk_evaluasi' => 'Tugas',
            'bobot' => 40,
        ])->assertRedirect();
    }

    public function test_rps_tugas_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('rps.tugas.index', $this->rps))->assertOk();
        $this->get(route('rps.tugas.create', $this->rps))->assertOk();

        $this->post(route('rps.tugas.store', $this->rps), [
            'minggu_topik' => 'Minggu 1',
            'nama_tugas' => 'Tugas RPS 1',
            'bobot_nilai' => 30,
        ])->assertRedirect();

        $tugas = RpsTugas::where('rps_id', $this->rps->id)->first();
        $this->assertNotNull($tugas);

        $this->get(route('rps.tugas.edit', [$this->rps, $tugas]))->assertOk();

        $this->put(route('rps.tugas.update', [$this->rps, $tugas]), [
            'minggu_topik' => 'Minggu 1',
            'nama_tugas' => 'Tugas RPS 1 Updated',
            'bobot_nilai' => 40,
        ])->assertRedirect();
    }

    public function test_rps_penilaian(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('rps.penilaian.index', $this->rps))->assertOk();
        $this->get(route('rps.penilaian.create', $this->rps))->assertOk();
    }

    // ── KRS ──────────────────────────────────────────────────

    public function test_krs_index_and_cetak(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('krs.index'))->assertOk();
        $this->get(route('krs.create'))->assertOk();
    }

    // ── LMS Dosen ──────────────────────────────────────────

    public function test_lms_tugas_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('lms.tugas.index', $this->pengampu))->assertOk();

        $this->post(route('lms.tugas.store', $this->pengampu), [
            'judul' => 'Tugas LMS 1',
            'instruksi' => 'Instruksi Tugas',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i'),
            'bobot_nilai' => 30,
        ])->assertRedirect();

        $tugas = LmsTugas::where('pengampu_id', $this->pengampu->id)->first();
        $this->assertNotNull($tugas);

        $this->get(route('lms.tugas.show', [$this->pengampu, $tugas]))->assertOk();
        $this->get(route('lms.tugas.edit', [$this->pengampu, $tugas]))->assertOk();

        $this->patch(route('lms.tugas.update', [$this->pengampu, $tugas]), [
            'judul' => 'Tugas LMS 1 Updated',
            'instruksi' => 'Instruksi Updated',
        ])->assertRedirect();
    }

    public function test_lms_materi_store_and_destroy(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('lms.materi.index', $this->pengampu))->assertOk();

        $this->post(route('lms.materi.store', $this->pengampu), [
            'judul' => 'Materi LMS 1',
            'deskripsi' => 'Deskripsi Materi',
        ])->assertRedirect();

        $materi = LmsMateri::where('pengampu_id', $this->pengampu->id)->first();
        $this->assertNotNull($materi);

        $this->get(route('lms.materi.show', [$this->pengampu, $materi]))->assertOk();
        $this->get(route('lms.materi.edit', [$this->pengampu, $materi]))->assertOk();

        $this->patch(route('lms.materi.update', [$this->pengampu, $materi]), [
            'judul' => 'Materi Updated',
        ])->assertRedirect();

        $this->delete(route('lms.materi.destroy', [$this->pengampu, $materi]))->assertRedirect();
    }

    public function test_lms_forum_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('lms.forum.index', $this->pengampu))->assertOk();

        $this->post(route('lms.forum.store', $this->pengampu), [
            'judul' => 'Forum Topik 1',
            'pesan' => 'Selamat datang di forum',
        ])->assertRedirect();

        $diskusi = LmsForumDiskusi::where('pengampu_id', $this->pengampu->id)->first();
        $this->assertNotNull($diskusi);

        $this->get(route('lms.forum.edit', [$this->pengampu, $diskusi]))->assertOk();

        $this->patch(route('lms.forum.update', [$this->pengampu, $diskusi]), [
            'pesan' => 'Forum Updated',
        ])->assertRedirect();
    }

    public function test_lms_pengumuman_crud(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('lms.pengumuman.index', $this->pengampu))->assertOk();

        $this->post(route('lms.pengumuman.store', $this->pengampu), [
            'judul' => 'Pengumuman 1',
            'isi' => 'Isi pengumuman',
        ])->assertRedirect();

        $pengumuman = LmsPengumuman::where('pengampu_id', $this->pengampu->id)->first();
        $this->assertNotNull($pengumuman);

        $this->get(route('lms.pengumuman.edit', [$this->pengampu, $pengumuman]))->assertOk();

        $this->patch(route('lms.pengumuman.update', [$this->pengampu, $pengumuman]), [
            'judul' => 'Pengumuman Updated',
            'isi' => 'Isi pengumuman',
        ])->assertRedirect();
    }

    // ── LMS Mahasiswa ──────────────────────────────────────

    public function test_lms_mahasiswa_index(): void
    {
        $this->pengampu->mahasiswas()->attach($this->mahasiswa->mahasiswa->id);
        $this->actingAs($this->mahasiswa);
        $this->get(route('mahasiswa.lms.index'))->assertOk();
    }

    // ── Assessment ──────────────────────────────────────────

    public function test_assessment_index(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('assessment.index'))->assertOk();
    }

    // ── Dashboard ──────────────────────────────────────────

    public function test_dashboard_admin(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_dashboard_dosen(): void
    {
        $this->actingAs($this->dosen);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_dashboard_mahasiswa(): void
    {
        $this->actingAs($this->mahasiswa);
        $this->get(route('dashboard'))->assertOk();
    }

    // ── Profile ──────────────────────────────────────────────

    public function test_profile_edit(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('profile.edit'))->assertOk();
    }
}
