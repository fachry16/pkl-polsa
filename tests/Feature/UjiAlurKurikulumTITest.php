<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentApproval;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UjiAlurKurikulumTITest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Uji alur ini hanya dijalankan terhadap database MySQL live (pikobe_polsa).');
        }
    }

    public function test_alur_kurikulum_ti_diisi_ulang_hingga_penilaian_disetujui_kaprodi(): void
    {
        $prodi = ProgramStudi::where('kode_prodi', 'TI')->firstOrFail();
        $kaprodi = Dosen::where('program_studi_id', $prodi->id)->where('jabatan', 'Kaprodi')->firstOrFail();
        $tahun = TahunAkademik::where('is_active', true)->firstOrFail();

        $dosenBunadi = Dosen::whereHas('user', fn ($q) => $q->where('email', 'bunadi@polsa.ac.id'))->firstOrFail();

        $mahasiswaTI = Mahasiswa::where('program_studi_id', $prodi->id)->orderBy('id')->get();
        $this->assertCount(10, $mahasiswaTI, 'Harus ada 10 mahasiswa prodi TI di database live.');

        // ===== Reset: hapus seluruh kurikulum TI (cascade ke seluruh turunan) =====
        Kurikulum::where('program_studi_id', $prodi->id)->delete();
        $this->assertDatabaseMissing('kurikulums', ['program_studi_id' => $prodi->id]);

        // ===== 1. Kurikulum (istilah persis DemoPresentationSeeder) =====
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.store'), [
                'program_studi_id' => $prodi->id,
                'nama_kurikulum' => 'Kurikulum OBE D4 TRPL 2024',
                'tahun_berlaku' => 2024,
                'beban_studi' => 144,
                'deskripsi' => 'Kurikulum berbasis Outcome-Based Education (OBE) untuk D4 TRPL.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $kurikulum = Kurikulum::where('program_studi_id', $prodi->id)->latest()->firstOrFail();
        $this->assertSame('Draft', $kurikulum->status);

        $this->actingAs($kaprodi->user)
            ->patch(route('kurikulum.aktifkan', $kurikulum->id))
            ->assertSessionHas('success');
        $this->assertSame('Aktif', $kurikulum->fresh()->status);

        // ===== 2. Profil Lulusan (PL) — persis seeder =====
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.profil-lulusan.store', $kurikulum->id), [
                'kode_pl' => 'PL-01',
                'nama_pl' => 'Software Engineer / Fullstack Developer Utama',
                'profesi' => 'Fullstack Developer, Software Engineer, Web Developer',
            ])
            ->assertSessionHasNoErrors();
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.profil-lulusan.store', $kurikulum->id), [
                'kode_pl' => 'PL-02',
                'nama_pl' => 'System Analyst & Software Architect',
                'profesi' => 'System Analyst, Solution Architect, IT Consultant',
            ])
            ->assertSessionHasNoErrors();
        $pl = collect($kurikulum->profilLulusans()->orderBy('kode_pl')->get());

        // ===== 3. CPL — persis seeder =====
        $cplRows = [
            ['CPL-01', 'Mampu merancang, mengimplementasikan, dan menguji perangkat lunak berbasis web & mobile sesuai standar industri.'],
            ['CPL-02', 'Mampu menguasai arsitektur basis data relasional/NoSQL, keamanan informasi, serta optimasi query sistem.'],
            ['CPL-03', 'Mampu menerapkan metodologi Agile/Scrum dan manajemen proyek rekayasa perangkat lunak secara profesional.'],
        ];
        foreach ($cplRows as [$kode, $deskripsi]) {
            $this->actingAs($kaprodi->user)
                ->post(route('kurikulum.cpl.store', $kurikulum->id), [
                    'kode_cpl' => $kode, 'deskripsi' => $deskripsi,
                ])
                ->assertSessionHasNoErrors();
        }
        $cpl = collect($kurikulum->cpls()->orderBy('kode_cpl')->get());

        // ===== 4. CPMK — persis seeder =====
        $cpmkRows = [
            ['CPMK-01', 'Mahasiswa mampu membangun backend REST API terstruktur menggunakan Laravel 12 & Eloquent ORM.'],
            ['CPMK-02', 'Mahasiswa mampu membangun antarmuka web reaktif Blade Templates, Alpine.js, dan Tailwind CSS.'],
            ['CPMK-03', 'Mahasiswa mampu merancang dan melakukan optimasi Query & Relationship pada Basis Data Relasional MySQL.'],
        ];
        foreach ($cpmkRows as [$kode, $deskripsi]) {
            $this->actingAs($kaprodi->user)
                ->post(route('kurikulum.cpmk.store', $kurikulum->id), [
                    'kode_cpmk' => $kode, 'deskripsi' => $deskripsi,
                ])
                ->assertSessionHasNoErrors();
        }
        $cpmk = collect($kurikulum->cpmks()->orderBy('kode_cpmk')->get());

        // ===== 5. Bahan Kajian (BK) — persis seeder =====
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.bahan-kajian.store', $kurikulum->id), [
                'kode_bk' => 'BK-WEB',
                'nama_bk' => 'Rekayasa Perangkat Lunak & Arsitektur Web',
                'referensi' => 'Buku Acuan RPL & Standar IEEE',
            ])
            ->assertSessionHasNoErrors();
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.bahan-kajian.store', $kurikulum->id), [
                'kode_bk' => 'BK-DB',
                'nama_bk' => 'Sistem Basis Data & Manajerial Informasi',
                'referensi' => 'Database System Concepts (Silberschatz)',
            ])
            ->assertSessionHasNoErrors();
        $bk = collect($kurikulum->bahanKajians()->orderBy('kode_bk')->get());

        // ===== 6. Mata Kuliah — nama & kode persis seeder =====
        $definisiMk = [
            ['TRPL301', 'Pemrograman Web Lanjut', 1, 2, 3],
            ['TRPL302', 'Basis Data Lanjut', 1, 2, 3],
            ['TI101', 'Algoritma & Pemrograman', 1, 2, 1],
            ['TRPL401', 'Pemrograman Mobile Modern', 1, 2, 4],
            ['TRPL601', 'Manajemen Proyek Perangkat Lunak', 2, 1, 6],
        ];
        $mk = collect([]);
        foreach ($definisiMk as [$kode, $nama, $sksTeori, $sksPraktikum, $semester]) {
            $this->actingAs($kaprodi->user)
                ->post(route('kurikulum.mata-kuliah.store', $kurikulum->id), [
                    'kode' => $kode,
                    'nama' => $nama,
                    'sks_teori' => $sksTeori,
                    'sks_praktikum' => $sksPraktikum,
                    'semester' => $semester,
                    'jenis' => 'Wajib',
                ])
                ->assertSessionHasNoErrors();
            $mk->push($kurikulum->mataKuliahs()->where('kode', $kode)->firstOrFail());
        }

        $mkWebLanjut = $mk->firstWhere('kode', 'TRPL301');

        // ===== 7. Matriks (menggunakan istilah seeder) =====
        $mapCplPl = [
            $cpl->get(0)->id => [$pl->get(0)->id],
            $cpl->get(1)->id => [$pl->get(1)->id],
            $cpl->get(2)->id => [$pl->get(0)->id],
        ];
        $this->actingAs($kaprodi->user)
            ->put(route('kurikulum.cpl-pl.update', $kurikulum->id), ['cpl' => $mapCplPl])
            ->assertSessionHas('success');

        $mapBkMk = [
            $mk->get(0)->id => [$bk->firstWhere('kode_bk', 'BK-WEB')->id], // TRPL301
            $mk->get(1)->id => [$bk->firstWhere('kode_bk', 'BK-DB')->id],  // TRPL302
            $mk->get(2)->id => [$bk->firstWhere('kode_bk', 'BK-WEB')->id], // TI101
            $mk->get(3)->id => [$bk->firstWhere('kode_bk', 'BK-WEB')->id], // TRPL401
            $mk->get(4)->id => [$bk->firstWhere('kode_bk', 'BK-WEB')->id], // TRPL601
        ];
        $this->actingAs($kaprodi->user)
            ->put(route('kurikulum.bk-mk.update', $kurikulum->id), ['mataKuliah' => $mapBkMk])
            ->assertSessionHas('success');

        $mapMkCpmk = [
            $mk->get(0)->id => [$cpmk->get(0)->id, $cpmk->get(1)->id], // TRPL301: CPMK-01 + CPMK-02
            $mk->get(1)->id => [$cpmk->get(2)->id],                    // TRPL302: CPMK-03
            $mk->get(2)->id => [$cpmk->get(0)->id],                    // TI101:   CPMK-01
            $mk->get(3)->id => [$cpmk->get(1)->id],                    // TRPL401: CPMK-02
            $mk->get(4)->id => [$cpmk->get(0)->id],                    // TRPL601: CPMK-01
        ];
        $this->actingAs($kaprodi->user)
            ->put(route('kurikulum.mk-cpmk.update', $kurikulum->id), ['mataKuliah' => $mapMkCpmk])
            ->assertSessionHas('success');

        $this->assertSame(2, $mkWebLanjut->cpmks()->count());

        $idBkWeb = $bk->firstWhere('kode_bk', 'BK-WEB')->id;
        $idBkDb = $bk->firstWhere('kode_bk', 'BK-DB')->id;
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.cpl-bk-mk.store', $kurikulum->id), [
                'mata_kuliah_id' => $mkWebLanjut->id,
                'mapping' => [
                    "{$cpl->get(0)->id}-{$idBkWeb}" => true,
                    "{$cpl->get(1)->id}-{$idBkWeb}" => true,
                    "{$cpl->get(2)->id}-{$idBkWeb}" => true,
                    "{$cpl->get(1)->id}-{$idBkDb}" => true,
                ],
            ])
            ->assertSessionHas('success');

        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.cpl-cpmk-mk.store', $kurikulum->id), [
                'mapping' => [
                    "{$cpl->get(0)->id}-{$cpmk->get(0)->id}-3" => true,
                    "{$cpl->get(0)->id}-{$cpmk->get(1)->id}-3" => true,
                    "{$cpl->get(1)->id}-{$cpmk->get(2)->id}-3" => true,
                ],
            ])
            ->assertSessionHas('success');

        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.pemenuhan-cpl.store', $kurikulum->id), [
                'mapping' => [
                    "{$cpl->get(0)->id}-3" => true,
                    "{$cpl->get(1)->id}-3" => true,
                    "{$cpl->get(2)->id}-3" => true,
                ],
            ])
            ->assertSessionHas('success');

        // ===== 8. Metode & Bobot Penilaian + Rumusan Nilai Akhir (TRPL301) =====
        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.metode-bobot-penilaian.store', $kurikulum->id), [
                'cpl_id' => $cpl->get(0)->id,
                'mata_kuliah_id' => $mkWebLanjut->id,
                'cpmk_id' => $cpmk->get(0)->id,
                'kuis' => 10,
                'tugas_teori_individu' => 20,
                'unjuk_kerja_presentasi' => 0,
                'tes_tulis_uts' => 30,
                'tes_tulis_uas' => 40,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('metode_bobot_penilaians', [
            'kurikulum_id' => $kurikulum->id,
            'mata_kuliah_id' => $mkWebLanjut->id,
            'total' => 100,
        ]);

        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.rumusan-nilai-akhir-mk.store', $kurikulum->id), [
                'cpl_id' => $cpl->get(0)->id,
                'mata_kuliah_id' => $mkWebLanjut->id,
                'cpmk_id' => $cpmk->get(0)->id,
                'skor_maks' => 100,
                'total' => 90,
            ])
            ->assertSessionHas('success');

        $this->actingAs($kaprodi->user)
            ->post(route('kurikulum.rumusan-nilai-akhir-cpl.store', $kurikulum->id), [
                'cpl_id' => $cpl->get(0)->id,
                'mata_kuliah_id' => $mkWebLanjut->id,
                'cpmk_id' => $cpmk->get(0)->id,
                'skor_maks' => 100,
                'total' => 90,
            ])
            ->assertSessionHas('success');

        // ===== 9. RPS lengkap Pemrograman Web Lanjut (TRPL301) — persis seeder =====
        $this->buatRpsWebLanjut($dosenBunadi, $kaprodi->user, $mkWebLanjut, $tahun, $mahasiswaTI);

        // ===== 10. Flows LMS→Asesmen OBE =====
        $this->jalankanLmsPenilaian($dosenBunadi, $kaprodi->user, $mkWebLanjut, $mahasiswaTI);

        // ===== 11. Verifikasi akhir =====
        $rps = Rps::where('mata_kuliah_id', $mkWebLanjut->id)->firstOrFail();
        $this->assertSame('Disetujui', $rps->fresh()->status);
        $this->assertSame('Aktif', Kurikulum::findOrFail($kurikulum->id)->status);

        $pengampuWebLanjut = Pengampu::where('mata_kuliah_id', $mkWebLanjut->id)->firstOrFail();
        $assessment = Assessment::where('pengampu_id', $pengampuWebLanjut->id)->firstOrFail();
        $this->assertSame(Assessment::STATUS_DINILAI, $assessment->status);

        $approval = AssessmentApproval::where('assessment_id', $assessment->id)->firstOrFail();
        $this->assertSame(AssessmentApproval::STATUS_DISETUJUI, $approval->status);
        $this->assertSame($kaprodi->user->id, $approval->disetujui_oleh);

        $this->assertSame(
            10,
            LmsNilaiMahasiswa::where('pengampu_id', $pengampuWebLanjut->id)->where('komponen', 'akhir')->whereNotNull('nilai')->distinct()->count('mahasiswa_id')
        );
    }

    /**
     * Rencana Pembelajaran Semester "Pemrograman Web Lanjut" persis DemoPresentationSeeder.
     */
    private function buatRpsWebLanjut(
        Dosen $dosen,
        User $kaprodiUser,
        MataKuliah $mataKuliah,
        TahunAkademik $tahun,
        $mahasiswaTI
    ) {
        $aktor = $dosen->user;

        // 1. KRS 3A + Pengampu + mahasiswa (dosen harus pengampu agar dapat membuat RPS)
        $this->actingAs($kaprodiUser)
            ->post(route('krs.store'), [
                'program_studi_id' => $mataKuliah->kurikulum->program_studi_id,
                'mata_kuliah_id' => $mataKuliah->id,
                'dosen_id' => $dosen->id,
                'tahun_akademik_id' => $tahun->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $pengampu = Pengampu::where('mata_kuliah_id', $mataKuliah->id)->firstOrFail();

        $this->actingAs($kaprodiUser)
            ->post(route('krs.mahasiswa.store', $pengampu->krs->id), [
                'mahasiswa_id' => $mahasiswaTI->pluck('id')->all(),
            ])
            ->assertSessionHas('success');

        $this->assertCount(10, $pengampu->mahasiswas()->get());

        // 2. RPS (status awal Draft) — metadata persis seeder
        $this->actingAs($aktor)
            ->post(route('mata-kuliah.rps.store', $mataKuliah->id), [
                'kode_rps' => 'RPS-TRPL301-2025',
                'semester' => 3,
                'dosen_pengampu' => 'Budi Santoso, M.Kom',
                'deskripsi_mata_kuliah' => 'Mata kuliah ini membahas pengembangan aplikasi web modern fullstack dengan Laravel 12, REST API, Blade Component, Alpine.js, Tailwind CSS, dan integrasi cloud storage.',
                'rumpun_mk' => 'Rekayasa Perangkat Lunak',
                'mk_prasyarat' => 'Pemrograman Web Dasar',
                'dosen_pengembang_rps' => 'Budi Santoso, M.Kom',
                'koordinator_rmk' => 'Budi Santoso, M.Kom',
                'ketua_prodi' => 'Dr. Eng. Kaprodi TRPL, M.Kom',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $rps = Rps::where('mata_kuliah_id', $mataKuliah->id)->firstOrFail();
        $this->assertSame('Draft', $rps->status);

        // 3. Pertemuan 1-16 — topik persis seeder
        $topikPertemuan = [
            1 => 'Pengenalan Arsitektur Laravel 12 & Konsep MVC Modern',
            2 => 'Database Migration, Seeder & Eloquent Model',
            3 => 'Controller, Resourceful Routing & Request Lifecycle',
            4 => 'Blade Templating Engine, Component & Directive',
            5 => 'Interaktivitas Frontend dengan Alpine.js & Tailwind CSS',
            6 => 'Pengembangan RESTful API & JSON Response Standard',
            7 => 'Otentikasi & Otorisasi Sistem (Breeze & Multi-Role Middleware)',
            8 => 'Ujian Tengah Semester (UTS) - Praktikum Coding REST API',
            9 => 'Manajemen Upload File & Integrasi Storage Backend Cloud (Google Drive)',
            10 => 'Keamanan Web: CSRF Protection, XSS Sanitization & Rate Limiting',
            11 => 'Form Request Validation & Custom Validation Rules',
            12 => 'Eloquent Relationship Lanjut: One-to-Many & Many-to-Many',
            13 => 'Pengujian Otomatis (Unit & Feature Testing dengan PHPUnit)',
            14 => 'Optimasi Performa Query & Caching Strategi',
            15 => 'Persiapan Capstone Project & Code Review',
            16 => 'Ujian Akhir Semester (UAS) - Presentasi & Demo Web Fullstack',
        ];
        foreach ($topikPertemuan as $minggu => $topik) {
            $this->actingAs($aktor)
                ->post(route('rps.pertemuan.store', $rps->id), [
                    'minggu' => $minggu,
                    'sub_cpmk' => "Sub-CPMK Minggu {$minggu}",
                    'materi' => $topik,
                    'metode' => 'Kuliah Teori & Praktikum Lab (2 SKS)',
                    'pengalaman_belajar' => 'Diskusi kelompok & Koding Mandiri',
                    'indikator' => "Ketepatan implementasi sintaks dan logika {$topik}",
                    'bobot' => 5.00,
                    'cpmk_induk' => 'CPMK-01',
                    'teknik_kriteria' => 'Rubrik Penilaian Koding',
                    'metode_daring' => 'E-Learning POLSA LMS & Zoom',
                    'metode_luring' => 'Tatap Muka di Lab Komputer 1',
                ])
                ->assertSessionHasNoErrors();
        }
        $this->assertCount(16, RpsPertemuan::where('rps_id', $rps->id)->get());

        // 4. Tugas — persis seeder (auto-sync ke LMS setelah pengampu ada)
        $tugasSeeder = [
            [
                'minggu_topik' => '6',
                'nama_tugas' => 'Tugas 1: Implementasi REST API & Form Validation',
                'sub_cpmk' => 'Sub-CPMK 1',
                'penugasan' => 'Membuat RESTful API CRUD menggunakan Laravel 12',
                'deskripsi' => 'Buatlah RESTful API CRUD menggunakan Laravel 12 dengan menerapkan Form Request Validation, JSON Resource Response, dan autentikasi Bearer Token.',
                'ruang_lingkup' => 'Backend API Development',
                'cara_pengerjaan' => 'Individu',
                'batas_waktu' => '7 Hari',
                'luaran_tugas' => 'Laporan PDF & Repository GitHub',
                'deadline' => now()->addDays(7)->toDateString(),
                'bobot_nilai' => 20,
            ],
            [
                'minggu_topik' => '12',
                'nama_tugas' => 'Tugas 2: Aplikasi Fullstack Web LMS dengan Alpine.js',
                'sub_cpmk' => 'Sub-CPMK 2',
                'penugasan' => 'Kembangkan aplikasi web interaktif fullstack',
                'deskripsi' => 'Kembangkan aplikasi web interaktif fullstack memanfaatkan Blade Component, Alpine.js untuk reactive UI, dan Tailwind CSS.',
                'ruang_lingkup' => 'Fullstack Web Application',
                'cara_pengerjaan' => 'Kelompok 2 Orang',
                'batas_waktu' => '14 Hari',
                'luaran_tugas' => 'Aplikasi Web Active & Source Code',
                'deadline' => now()->addDays(14)->toDateString(),
                'bobot_nilai' => 30,
            ],
        ];
        foreach ($tugasSeeder as $t) {
            $this->actingAs($aktor)
                ->post(route('rps.tugas.store', $rps->id), array_merge($t, ['kategori_komponen' => 'tugas']))
                ->assertSessionHasNoErrors();
        }
        $this->assertCount(2, Rps::find($rps->id)->tugas()->get());
        $this->assertCount(2, LmsTugas::where('pengampu_id', $pengampu->id)->get());

        // 5. Penilaian — persis seeder (total 100)
        $this->actingAs($aktor)
            ->post(route('rps.penilaian.store', $rps->id), [
                'tugas' => 20, 'quiz' => 10, 'uts' => 30, 'uas' => 40,
                'praktikum' => 0, 'project' => 0, 'absensi' => 0, 'keaktifan' => 0, 'etika' => 0,
            ])
            ->assertSessionHasNoErrors();

        // 6. Bentuk evaluasi — persis seeder
        $bentukEvaluasiSeeder = [
            [
                'bentuk_evaluasi' => 'Tugas & Praktikum',
                'sub_cpmk' => 'Sub-CPMK 1 & 2',
                'instrumen' => 'Rubrik Koding REST API',
                'frekuensi' => '2 Kali',
                'tagihan' => 'Laporan PDF & Repository GitHub',
                'bobot' => 20,
                'formatif' => true,
                'sumatif' => true,
            ],
            [
                'bentuk_evaluasi' => 'Ujian Tengah Semester (UTS)',
                'sub_cpmk' => 'Sub-CPMK 1',
                'instrumen' => 'Ujian Praktikum Lab',
                'frekuensi' => '1 Kali',
                'tagihan' => 'Source Code Project',
                'bobot' => 30,
                'formatif' => false,
                'sumatif' => true,
            ],
            [
                'bentuk_evaluasi' => 'Ujian Akhir Semester (UAS)',
                'sub_cpmk' => 'Sub-CPMK 2 & 3',
                'instrumen' => 'Presentasi & Live Demo App',
                'frekuensi' => '1 Kali',
                'tagihan' => 'Aplikasi Fullstack Web',
                'bobot' => 40,
                'formatif' => false,
                'sumatif' => true,
            ],
        ];
        foreach ($bentukEvaluasiSeeder as $b) {
            $this->actingAs($aktor)
                ->post(route('rps.bentuk-evaluasi.store', $rps->id), $b)
                ->assertSessionHasNoErrors();
        }

        // 7. Ajukan oleh dosen pengampu
        $this->actingAs($aktor)
            ->patch(route('rps.ajukan', $rps->id))
            ->assertSessionHasNoErrors();
        $this->assertSame('Diajukan', $rps->fresh()->status);

        // 8. Setujui oleh Kaprodi
        $this->actingAs($kaprodiUser)
            ->patch(route('rps.setujui', $rps->id))
            ->assertSessionHasNoErrors();
        $this->assertSame('Disetujui', $rps->fresh()->status);

        return $rps;
    }

    private function jalankanLmsPenilaian(
        Dosen $dosen,
        User $kaprodiUser,
        MataKuliah $mataKuliah,
        $mahasiswaTI
    ): void {
        $pengampu = Pengampu::where('mata_kuliah_id', $mataKuliah->id)->firstOrFail();
        $tugas = LmsTugas::where('pengampu_id', $pengampu->id)->orderBy('id')->firstOrFail();
        $this->assertFalse((bool) $tugas->is_active);

        // 1. Publikasi tugas 1
        $this->actingAs($dosen->user)
            ->post(route('lms.tugas.tugaskan', [$pengampu->id, $tugas->id]), [
                'deadline' => now()->addDays(5)->toDateTimeString(),
            ])
            ->assertSessionHas('toast_success');
        $this->assertTrue((bool) $tugas->fresh()->is_active);

        // 2. Mahasiswa submit
        foreach ($mahasiswaTI as $i => $mhs) {
            $this->actingAs($mhs->user)
                ->post(route('mahasiswa.lms.tugas.kumpul', $tugas->id), [
                    'catatan_mahasiswa' => 'Tugas REST API telah selesai dikerjakan sesuai petunjuk modul.',
                ])
                ->assertRedirect();
        }
        $this->assertCount(10, LmsSubmission::where('lms_tugas_id', $tugas->id)->get());

        // 3. Dosen menilai tiap submission
        foreach (LmsSubmission::where('lms_tugas_id', $tugas->id)->get() as $i => $sub) {
            $this->actingAs($dosen->user)
                ->patch(route('lms.submission.nilai', $sub->id), ['nilai' => 70 + ($i % 30)])
                ->assertSessionHas('toast_success');
        }

        // 4. Isi komponen quiz / UTS / UAS
        $nilaiKomponen = [];
        foreach ($mahasiswaTI as $i => $mhs) {
            $nilaiKomponen[$mhs->id] = [
                'quiz' => 60 + ($i % 40),
                'uts' => 70,
                'uas' => 80,
                'praktikum' => '',
                'project' => '',
            ];
        }
        $this->actingAs($dosen->user)
            ->post(route('lms.tugas.komponen', $pengampu->id), ['nilai' => $nilaiKomponen])
            ->assertSessionHas('toast_success');

        // 5. Hitung ulang + sync ke Asesmen OBE
        $this->actingAs($dosen->user)
            ->get(route('lms.tugas.sync', $pengampu->id))
            ->assertRedirect();

        foreach ($mahasiswaTI as $mhs) {
            $nilaiAkhir = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                ->where('mahasiswa_id', $mhs->id)
                ->where('komponen', 'akhir')
                ->value('nilai');
            $this->assertNotNull($nilaiAkhir, "Nilai akhir mahasiswa {$mhs->nim} kosong.");
        }

        $assessment = Assessment::where('pengampu_id', $pengampu->id)->firstOrFail();
        $this->assertSame(Assessment::STATUS_DINILAI, $assessment->status);

        // 6. Dosen mengajukan nilai ke Kaprodi
        $this->actingAs($dosen->user)
            ->patch(route('lms.nilai.ajukan', $pengampu->id))
            ->assertSessionHas('toast_success');

        $approval = AssessmentApproval::where('assessment_id', $assessment->id)->firstOrFail();
        $this->assertSame(AssessmentApproval::STATUS_MENUNGGU, $approval->status);

        // 7. Kaprodi menyetujui nilai
        $this->actingAs($kaprodiUser)
            ->patch(route('assessment.setujui', $assessment->id))
            ->assertSessionHas('success');

        $this->assertSame(AssessmentApproval::STATUS_DISETUJUI, $approval->fresh()->status);
        $this->assertSame($kaprodiUser->id, $approval->fresh()->disetujui_oleh);
    }
}
