<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\RumusanNilaiAkhirMk;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\AssessmentCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentObeTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin(): User
    {
        return User::create([
            'name' => 'Admin Assessment',
            'email' => 'admin_assessment@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    private function buatProdi(string $kode = 'TI', string $nama = 'Teknik Informatika'): ProgramStudi
    {
        return ProgramStudi::create([
            'kode_prodi' => $kode,
            'nama_prodi' => $nama,
            'jenjang' => 'D3',
        ]);
    }

    private function buatDosen(ProgramStudi $prodi, string $nidn = '99887766'): User
    {
        $user = User::create([
            'name' => 'Dosen Assessment',
            'email' => 'dosen_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => $nidn,
            'jabatan' => 'Dosen',
        ]);

        return $user;
    }

    private function buatKaprodi(ProgramStudi $prodi, string $nidn = '11223344'): User
    {
        $user = User::create([
            'name' => 'Kaprodi Assessment',
            'email' => 'kaprodi_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen', 'kaprodi'],
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $prodi->id,
            'nidn' => $nidn,
            'jabatan' => 'Kaprodi',
        ]);

        return $user;
    }

    private function buatDirektur(): User
    {
        return User::create([
            'name' => 'Direktur Assessment',
            'email' => 'direktur_'.uniqid().'@test.dev',
            'password' => bcrypt('password'),
            'role' => 'direktur',
            'roles' => ['direktur'],
        ]);
    }

    private function buatTahunAkademik(): TahunAkademik
    {
        return TahunAkademik::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
    }

    /**
     * Skema merujuk pada LEDIK - WORKHSOP PIKOBE.xlsx (Contoh Asesmen).
     *
     * MK02: CPMK021 (CPL02, 30), CPMK022 (CPL02, 30), CPMK031 (CPL03, 40) => max 100
     * MK03: CPMK032 (CPL03, 20), CPMK034 (CPL03, 30), CPMK023 (CPL02, 50) => max 100
     * Mhs 1: 20/20/30 + 14/27/43 => MK02=70, MK03=84, CPL02=83/110=75%, CPL03=71/90=79%
     * Mhs 3: 27/28/38 + 16/26/46 => MK02=93, MK03=88, CPL02=101/110=92%, CPL03=80/90=89%
     *
     * @return array<string, mixed>
     */
    private function buatDataDasar(): array
    {
        $prodi = $this->buatProdi();

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2025',
            'tahun_berlaku' => 2025,
            'beban_studi' => '108 SKS',
            'deskripsi' => 'Kurikulum contoh',
            'status' => 'Aktif',
        ]);

        $cpl2 = Cpl::create(['kurikulum_id' => $kurikulum->id, 'kode_cpl' => 'CPL02', 'deskripsi' => 'Pemahaman konsep']);
        $cpl3 = Cpl::create(['kurikulum_id' => $kurikulum->id, 'kode_cpl' => 'CPL03', 'deskripsi' => 'Keterampilan teknis']);

        $cpmks = [];
        foreach (['CPMK021', 'CPMK022', 'CPMK031', 'CPMK032', 'CPMK034', 'CPMK023'] as $kode) {
            $cpmks[$kode] = Cpmk::create([
                'kurikulum_id' => $kurikulum->id,
                'kode_cpmk' => $kode,
                'deskripsi' => 'Menguasai '.$kode,
            ]);
        }

        $mk2 = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK02',
            'nama' => 'Pemrograman Dasar',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $mk3 = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => 'MK03',
            'nama' => 'Basis Data',
            'sks_teori' => 2,
            'sks_praktikum' => 1,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $tahunAkademik = $this->buatTahunAkademik();

        $dosenUser = $this->buatDosen($prodi, '99887766');
        $dosen = $dosenUser->dosen;

        $kaprodiUser = $this->buatKaprodi($prodi, '11223344');

        $pengampu2 = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk2->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $pengampu3 = Pengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mk3->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester_akademik' => 'Ganjil',
            'kelas' => 'A',
        ]);

        $mhs1 = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2025112001',
            'nama' => 'Mahasiswa Satu',
            'angkatan' => 2025,
            'status' => 'Aktif',
        ]);

        $mhs3 = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2025112003',
            'nama' => 'Mahasiswa Tiga',
            'angkatan' => 2025,
            'status' => 'Aktif',
        ]);

        foreach ([$pengampu2, $pengampu3] as $pengampu) {
            $pengampu->mahasiswas()->sync([$mhs1->id, $mhs3->id]);
        }

        $rows = [
            [$mk2, $cpl2, $cpmks['CPMK021'], 30],
            [$mk2, $cpl2, $cpmks['CPMK022'], 30],
            [$mk2, $cpl3, $cpmks['CPMK031'], 40],
            [$mk3, $cpl3, $cpmks['CPMK032'], 20],
            [$mk3, $cpl3, $cpmks['CPMK034'], 30],
            [$mk3, $cpl2, $cpmks['CPMK023'], 50],
        ];

        foreach ($rows as [$mk, $cpl, $cpmk, $skorMaks]) {
            RumusanNilaiAkhirMk::create([
                'kurikulum_id' => $kurikulum->id,
                'cpl_id' => $cpl->id,
                'mata_kuliah_id' => $mk->id,
                'cpmk_id' => $cpmk->id,
                'skor_maks' => $skorMaks,
                'total' => $skorMaks,
            ]);
        }

        return [
            'prodi' => $prodi,
            'kurikulum' => $kurikulum,
            'cpl2' => $cpl2,
            'cpl3' => $cpl3,
            'cpmks' => $cpmks,
            'mk2' => $mk2,
            'mk3' => $mk3,
            'tahunAkademik' => $tahunAkademik,
            'dosenUser' => $dosenUser,
            'kaprodiUser' => $kaprodiUser,
            'dosen' => $dosen,
            'pengampu2' => $pengampu2,
            'pengampu3' => $pengampu3,
            'mhs1' => $mhs1,
            'mhs3' => $mhs3,
        ];
    }

    private function buatAssessmentMk2(array $d): void
    {
        $nilai = [
            $d['mhs1']->id => ['CPMK021' => 20, 'CPMK022' => 20, 'CPMK031' => 30],
            $d['mhs3']->id => ['CPMK021' => 27, 'CPMK022' => 28, 'CPMK031' => 38],
        ];

        $assessment = Assessment::create([
            'pengampu_id' => $d['pengampu2']->id,
            'status' => Assessment::STATUS_DINILAI,
            'created_by' => $d['dosenUser']->id,
        ]);

        foreach ($nilai as $mhsId => $skor) {
            foreach ($skor as $kode => $v) {
                $assessment->scores()->create([
                    'mahasiswa_id' => $mhsId,
                    'cpmk_id' => $d['cpmks'][$kode]->id,
                    'nilai' => $v,
                ]);
            }
        }
    }

    private function buatAssessmentMk3(array $d): void
    {
        $nilai = [
            $d['mhs1']->id => ['CPMK032' => 14, 'CPMK034' => 27, 'CPMK023' => 43],
            $d['mhs3']->id => ['CPMK032' => 16, 'CPMK034' => 26, 'CPMK023' => 46],
        ];

        $assessment = Assessment::create([
            'pengampu_id' => $d['pengampu3']->id,
            'status' => Assessment::STATUS_DINILAI,
            'created_by' => $d['dosenUser']->id,
        ]);

        foreach ($nilai as $mhsId => $skor) {
            foreach ($skor as $kode => $v) {
                $assessment->scores()->create([
                    'mahasiswa_id' => $mhsId,
                    'cpmk_id' => $d['cpmks'][$kode]->id,
                    'nilai' => $v,
                ]);
            }
        }
    }

    public function test_service_menghitung_nilai_mk_dan_capaian_per_mahasiswa(): void
    {
        $d = $this->buatDataDasar();
        $this->buatAssessmentMk2($d);

        $service = app(AssessmentCalculationService::class);
        $assessment = Assessment::first();

        $summary = $service->mkSummary(
            $assessment->load('pengampu.mataKuliah'),
            collect([$d['mhs1']->fresh(), $d['mhs3']->fresh()])
        );

        $this->assertEquals(100, $summary['max']);

        $byNim = collect($summary['rows'])->keyBy(fn ($r) => $r['mahasiswa']->nim);

        $this->assertEquals(70, $byNim['2025112001']['nilai']);
        $this->assertEquals(70.0, $byNim['2025112001']['capaian']);

        $this->assertEquals(93, $byNim['2025112003']['nilai']);
        $this->assertEquals(93.0, $byNim['2025112003']['capaian']);
    }

    public function test_service_menghitung_capaian_cpl_lintas_mata_kuliah(): void
    {
        $d = $this->buatDataDasar();
        $this->buatAssessmentMk2($d);
        $this->buatAssessmentMk3($d);

        $service = app(AssessmentCalculationService::class);
        $assessments = Assessment::with(['scores', 'pengampu.mataKuliah'])->get();

        $cpl = $service->cplSummary($assessments);

        // CPL02: CP0221+CPMK022+CPMK023, max 30+30+50 = 110
        $cpl2Key = $d['cpl2']->id;
        $this->assertEquals(110, $cpl[$cpl2Key]['max']);
        $this->assertSame(75.45454545, round($cpl[$cpl2Key]['per_student'][$d['mhs1']->id]['capaian'], 8));
        $this->assertEquals(92, round($cpl[$cpl2Key]['per_student'][$d['mhs3']->id]['capaian'], 0));

        // CPL03: CPMK031+CPMK032+CPMK034, max 40+20+30 = 90
        $cpl3Key = $d['cpl3']->id;
        $this->assertEquals(90, $cpl[$cpl3Key]['max']);
        $this->assertSame(78.88888889, round($cpl[$cpl3Key]['per_student'][$d['mhs1']->id]['capaian'], 8));
        $this->assertEquals(89, round($cpl[$cpl3Key]['per_student'][$d['mhs3']->id]['capaian'], 0));
    }

    public function test_dosen_dapat_menyimpan_nilai_untuk_matkul_sendiri(): void
    {
        $d = $this->buatDataDasar();
        $dosenUser = $d['dosenUser'];

        $payload = [
            'pengampu_id' => $d['pengampu2']->id,
            'scores' => [
                $d['mhs1']->id => [
                    $d['cpmks']['CPMK021']->id => 20,
                    $d['cpmks']['CPMK022']->id => 20,
                    $d['cpmks']['CPMK031']->id => 30,
                ],
                $d['mhs3']->id => [
                    $d['cpmks']['CPMK021']->id => 27,
                    $d['cpmks']['CPMK022']->id => 28,
                    $d['cpmks']['CPMK031']->id => 38,
                ],
            ],
        ];

        $this->actingAs($dosenUser)
            ->post(route('assessment.store'), $payload)
            ->assertRedirect();

        $assessment = Assessment::where('pengampu_id', $d['pengampu2']->id)->first();
        $this->assertNotNull($assessment);
        $this->assertEquals(Assessment::STATUS_DINILAI, $assessment->status);
        $this->assertSame(6, $assessment->scores()->count());

        $this->assertDatabaseHas('assessment_scores', [
            'mahasiswa_id' => $d['mhs1']->id,
            'cpmk_id' => $d['cpmks']['CPMK021']->id,
            'nilai' => 20.00,
        ]);
    }

    public function test_nilai_tidak_boleh_melebihi_bobot_cpmk(): void
    {
        $d = $this->buatDataDasar();
        $dosenUser = $d['dosenUser'];

        $payload = [
            'pengampu_id' => $d['pengampu2']->id,
            'scores' => [
                $d['mhs1']->id => [
                    $d['cpmks']['CPMK021']->id => 31, // maks 30
                    $d['cpmks']['CPMK022']->id => 20,
                    $d['cpmks']['CPMK031']->id => 30,
                ],
            ],
        ];

        $this->actingAs($dosenUser)
            ->post(route('assessment.store'), $payload)
            ->assertSessionHasErrors('scores.'.$d['mhs1']->id.'.'.$d['cpmks']['CPMK021']->id);

        $this->assertSame(0, Assessment::count());
    }

    public function test_tidak_bisa_mulai_assessment_di_luar_lingkup(): void
    {
        $d = $this->buatDataDasar();

        $prodi2 = $this->buatProdi('SI', 'Sistem Informasi');
        $kur2 = Kurikulum::create(['program_studi_id' => $prodi2->id, 'nama_kurikulum' => 'Kur2', 'tahun_berlaku' => 2024, 'beban_studi' => '108 SKS', 'deskripsi' => 'd', 'status' => 'Aktif']);
        $mk4 = MataKuliah::create(['kurikulum_id' => $kur2->id, 'kode' => 'MKX', 'nama' => 'X', 'sks_teori' => 2, 'sks_praktikum' => 0, 'semester' => 1]);
        $dosen2 = $this->buatDosen($prodi2, '44556677');
        $pengampu4 = Pengampu::create(['dosen_id' => $dosen2->dosen->id, 'mata_kuliah_id' => $mk4->id, 'tahun_akademik_id' => $d['tahunAkademik']->id, 'semester_akademik' => 'Ganjil', 'kelas' => 'B']);

        // Dosen mencoba mulai assessment untuk kelas milik dosen lain => 403.
        $this->actingAs($d['dosenUser'])
            ->post(route('lms.assessment', $pengampu4->id))
            ->assertForbidden();

        // Kaprodi prodi lain => 403.
        $this->actingAs($d['kaprodiUser'])
            ->post(route('lms.assessment', $pengampu4->id))
            ->assertForbidden();

        // Admin boleh mulai di kelas manapun.
        $this->actingAs($this->buatAdmin())
            ->post(route('lms.assessment', $pengampu4->id))
            ->assertRedirect();

        // Dosen boleh mulai kelas sendiri.
        $this->actingAs($d['dosenUser'])
            ->post(route('lms.assessment', $d['pengampu2']->id))
            ->assertRedirect();

        $this->assertDatabaseHas('assessments', ['pengampu_id' => $pengampu4->id, 'status' => 'draft']);
    }

    public function test_halaman_monitoring_rekap_dan_export_dirender(): void
    {
        $d = $this->buatDataDasar();
        $this->buatAssessmentMk2($d);

        // Dashboard monitoring: daftar + rekap/export, tanpa tombol CRUD.
        $this->actingAs($d['kaprodiUser'])
            ->get(route('assessment.index'))
            ->assertOk()
            ->assertSee('Monitoring Assessment OBE')
            ->assertSee('Rekap')
            ->assertSee('Export')
            ->assertDontSee('Isi Nilai')
            ->assertDontSee('Reset Nilai Assessment')
            ->assertDontSee('Hapus Assessment');

        $this->actingAs($d['dosenUser'])
            ->get(route('assessment.rekap'))
            ->assertOk();

        $this->actingAs($d['dosenUser'])
            ->get(route('assessment.export'))
            ->assertOk();
    }

    public function test_filter_menampilkan_kurikulum_dan_mata_kuliah_yang_ada(): void
    {
        $d = $this->buatDataDasar();

        // Dropdown mata kuliah terisi dari pengampu yang ada, bukan dari rumusan (yg bisa kosong).
        $this->actingAs($d['dosenUser'])
            ->get(route('assessment.rekap'))
            ->assertSee('MK02 — Pemrograman Dasar')
            ->assertSee('MK03 — Basis Data');

        // Dropdown kurikulum memakai kolom nama_kurikulum.
        $this->actingAs($d['kaprodiUser'])
            ->get(route('assessment.rekap'))
            ->assertSee('Kurikulum 2025');
    }

    public function test_admin_kaprodi_dan_direktur_dapat_membuka_dashboard(): void
    {
        $prodi = $this->buatProdi('SI', 'Sistem Informasi');
        $kur = Kurikulum::create(['program_studi_id' => $prodi->id, 'nama_kurikulum' => 'Kur', 'tahun_berlaku' => 2024, 'beban_studi' => '144 SKS', 'deskripsi' => 'd', 'status' => 'Aktif']);
        $mk = MataKuliah::create(['kurikulum_id' => $kur->id, 'kode' => 'MKX', 'nama' => 'X', 'sks_teori' => 2, 'sks_praktikum' => 0, 'semester' => 1]);
        $ta = $this->buatTahunAkademik();
        $du = $this->buatDosen($prodi, '55667788');
        Pengampu::create(['dosen_id' => $du->dosen->id, 'mata_kuliah_id' => $mk->id, 'tahun_akademik_id' => $ta->id, 'semester_akademik' => 'Ganjil', 'kelas' => 'A']);

        foreach ([$this->buatAdmin(), $this->buatKaprodi($prodi, '66778899'), $this->buatDirektur()] as $user) {
            $this->actingAs($user)->get(route('assessment.index'))->assertOk();
        }
    }

    public function test_kaprodi_dan_admin_dapat_reset_dan_hapus_assessment(): void
    {
        $d = $this->buatDataDasar();

        $this->actingAs($d['dosenUser'])->post(route('assessment.store'), [
            'pengampu_id' => $d['pengampu2']->id,
            'scores' => [$d['mhs1']->id => [$d['cpmks']['CPMK021']->id => 20, $d['cpmks']['CPMK022']->id => 20, $d['cpmks']['CPMK031']->id => 30]],
        ]);

        $assessment = Assessment::where('pengampu_id', $d['pengampu2']->id)->first();
        $this->assertSame(3, $assessment->scores()->count());

        // Reset nilai oleh kaprodi => skor kosong, status draft.
        $this->actingAs($d['kaprodiUser'])
            ->delete(route('assessment.reset', $assessment->id))
            ->assertRedirect();

        $assessment->fresh();
        $this->assertSame(0, $assessment->fresh()->scores()->count());
        $this->assertEquals(Assessment::STATUS_DRAFT, $assessment->fresh()->status);

        // Hapus assessment oleh admin => baris & skor hilang.
        $this->actingAs($this->buatAdmin())
            ->delete(route('assessment.destroy', $assessment->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('assessments', ['id' => $assessment->id]);
        $this->assertDatabaseMissing('assessment_scores', ['assessment_id' => $assessment->id]);
    }

    public function test_dosen_tidak_dapat_reset_atau_hapus_assessment(): void
    {
        $d = $this->buatDataDasar();

        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $d['pengampu2']->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => $d['dosenUser']->id]
        );

        $this->actingAs($d['dosenUser'])
            ->delete(route('assessment.reset', $assessment->id))
            ->assertForbidden();

        $this->actingAs($d['dosenUser'])
            ->delete(route('assessment.destroy', $assessment->id))
            ->assertForbidden();

        $this->assertDatabaseHas('assessments', ['id' => $assessment->id]);
    }

    public function test_halaman_tahun_akademik_menyediakan_link_assessment_obe(): void
    {
        $d = $this->buatDataDasar();

        $this->actingAs($this->buatAdmin())
            ->get(route('tahun-akademik.index'))
            ->assertOk()
            ->assertSee('Assessment OBE')
            ->assertSee(route('assessment.index'), false);
    }

    public function test_kaprodi_kelola_assessment_via_halaman_kelas(): void
    {
        $d = $this->buatDataDasar();

        $this->actingAs($d['dosenUser'])->post(route('assessment.store'), [
            'pengampu_id' => $d['pengampu2']->id,
            'scores' => [$d['mhs1']->id => [$d['cpmks']['CPMK021']->id => 20, $d['cpmks']['CPMK022']->id => 20, $d['cpmks']['CPMK031']->id => 30]],
        ])->assertRedirect();

        // Kaprodi prodi yang sama dapat membuka halaman kelas dan mengelola.
        $this->actingAs($d['kaprodiUser'])
            ->get(route('lms.show', $d['pengampu2']->id))
            ->assertOk()
            ->assertSee('Assessment CPMK')
            ->assertSee('Simpan Status')
            ->assertSee('Reset')
            ->assertSee('Hapus')
            ->assertSee('Simpan Nilai CPMK');

        // Dashboard monitoring tidak memuat tombol kelola.
        $this->actingAs($d['kaprodiUser'])
            ->get(route('assessment.index'))
            ->assertOk()
            ->assertDontSee('Reset Nilai Assessment')
            ->assertDontSee('Hapus Assessment');
    }

    public function test_dosen_mulai_assessment_dari_halaman_kelas(): void
    {
        $d = $this->buatDataDasar();

        // Tab assessment tampil, namun grid belum ada (belum ada assessment).
        $this->actingAs($d['dosenUser'])
            ->get(route('lms.show', $d['pengampu2']->id))
            ->assertOk()
            ->assertSee('Assessment CPMK')
            ->assertSee('Mulai Assessment CPMK');

        $this->assertNull(Assessment::where('pengampu_id', $d['pengampu2']->id)->first());

        $this->actingAs($d['dosenUser'])
            ->post(route('lms.assessment', $d['pengampu2']->id))
            ->assertRedirect();

        $this->assertDatabaseHas('assessments', ['pengampu_id' => $d['pengampu2']->id, 'status' => Assessment::STATUS_DRAFT]);

        // Setelah dibuat, grid CPMK tampil di halaman kelas.
        $this->actingAs($d['dosenUser'])
            ->get(route('lms.show', $d['pengampu2']->id))
            ->assertSee('Simpan Nilai CPMK')
            ->assertSee('CPMK021')
            ->assertSee('CPMK022')
            ->assertSee('CPMK031');
    }
}