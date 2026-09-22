<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentApproval;
use App\Models\AssessmentScore;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\LmsNilaiMahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Notifications\NilaiDiajukan;
use App\Notifications\NilaiDirevisi;
use App\Notifications\NilaiDisetujui;
use App\Services\AssessmentCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssessmentController extends Controller
{
    public function __construct(
        private AssessmentCalculationService $calc
    ) {}

    /**
     * Dashboard ringkas Assessment OBE.
     */
    public function index(Request $request)
    {
        $this->authorizeAssessmentRead();

        $filters = $this->filters($request);
        $assessments = $this->scopedAssessments($filters, ['scores', 'pengampu.mataKuliah', 'pengampu.tahunAkademik', 'pengampu.mahasiswas'])->get();

        $kpis = $this->dashboardKpis($assessments);

        $drop = $this->filterDropdowns($filters);

        return view('assessment.index', compact('filters', 'assessments', 'kpis', 'drop'));
    }

    /**
     * Mulai assessment (draft) dari halaman kelas LMS oleh dosen pengampu.
     */
    public function mulaiDariKelas(Pengampu $pengampu)
    {
        $this->authorizeAssessmentWrite();

        $this->authorizePengampuWrite($pengampu);

        Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => auth()->id()]
        );

        return back()->with('success', 'Assessment CPMK siap diisi untuk kelas '.$pengampu->kelas.'.');
    }

    /**
     * Simpan nilai CPMK untuk sebuah assessment (mata kuliah/kelas).
     */
    public function store(Request $request)
    {
        $this->authorizeAssessmentWrite();

        $request->validate([
            'pengampu_id' => 'required|exists:pengampus,id',
            'scores' => 'nullable|array',
        ]);

        $pengampu = Pengampu::with('mataKuliah')->findOrFail($request->pengampu_id);

        $this->authorizePengampuWrite($pengampu);

        $config = $this->calc->cpmkConfigForMataKuliah($pengampu->mataKuliah);

        $rules = $this->scoreRules($config);
        $validated = $request->validate($rules);

        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => auth()->id()]
        );

        $scored = 0;

        foreach (($validated['scores'] ?? []) as $mahasiswaId => $cpmks) {
            foreach ($cpmks as $cpmkId => $nilai) {
                $bobot = $config[$cpmkId]['bobot'] ?? null;
                if ($bobot === null) {
                    continue;
                }

                if ($nilai === null || $nilai === '') {
                    AssessmentScore::where('assessment_id', $assessment->id)
                        ->where('mahasiswa_id', $mahasiswaId)
                        ->where('cpmk_id', $cpmkId)
                        ->delete();

                    continue;
                }

                $nilai = (float) $nilai;
                $scored++;

                AssessmentScore::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'mahasiswa_id' => $mahasiswaId,
                        'cpmk_id' => $cpmkId,
                    ],
                    ['nilai' => $nilai]
                );
            }
        }

        $assessment->update([
            'status' => $scored > 0 ? Assessment::STATUS_DINILAI : Assessment::STATUS_DRAFT,
        ]);

        return back()->with('success', 'Nilai CPMK berhasil disimpan ('.count($validated['scores'] ?? []).' mahasiswa dinilai).');
    }

    /**
     * Kunci/finalisasi assessment oleh Kaprodi/Admin.
     */
    public function finalize(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessmentWrite();

        abort_unless(auth()->user()->isAdmin() || auth()->user()->isKaprodi(), 403);

        $request->validate(['status' => 'required|in:dinilai,final']);

        $assessment->update(['status' => $request->status]);

        return back()->with('success', 'Status assessment diperbarui.');
    }

    /**
     * Kosongkan seluruh nilai CPMK (kembali ke draft).
     */
    public function reset(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessmentWrite();

        abort_unless(auth()->user()->isAdmin() || auth()->user()->isKaprodi(), 403);

        $assessment->scores()->delete();
        $assessment->update(['status' => Assessment::STATUS_DRAFT]);

        return back()->with('success', 'Semua nilai assessment direset. Assessment kembali ke status draft.');
    }

    /**
     * Hapus assessment beserta seluruh nilai CPMK-nya.
     */
    public function destroy(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessmentWrite();

        abort_unless(auth()->user()->isAdmin() || auth()->user()->isKaprodi(), 403);

        $pengampuId = $assessment->pengampu_id;
        $assessment->delete();

        return back()->with('success', 'Assessment berhasil dihapus (kelas '.$pengampuId.'). Nilai input dapat dimulai ulang dari halaman input.');
    }

    /**
     * Rekap capaian CPMK, MK, dan CPL.
     */
    public function rekap(Request $request)
    {
        $this->authorizeAssessmentRead();

        $filters = $this->filters($request);
        $assessments = $this->scopedAssessments($filters, ['scores', 'pengampu.mataKuliah', 'pengampu.tahunAkademik', 'pengampu.mahasiswas'])->get();

        $drop = $this->filterDropdowns($filters);

        if ($assessments->isEmpty()) {
            $target = $this->capaianTarget($filters);

            return view('assessment.rekap', compact('filters', 'drop', 'target'))->with('rekap', null);
        }

        $rekap = $this->calc->rekap($assessments);

        $target = $this->capaianTarget($filters);
        $cpmkRecaps = $this->calc->cpmkRecap($rekap['mata_kuliahs'], $target);
        $cplRecaps = $this->calc->cplRecap($rekap['cpls'], $target);
        $distribusi = $this->calc->distribusiGrade($rekap['mata_kuliahs']);

        return view('assessment.rekap', compact('filters', 'drop', 'rekap', 'target', 'cpmkRecaps', 'cplRecaps', 'distribusi'));
    }

    /**
     * Export rekap (excel / pdf / print).
     */
    public function export(Request $request)
    {
        $this->authorizeAssessmentRead();

        $filters = $this->filters($request);
        $assessments = $this->scopedAssessments($filters, ['scores', 'pengampu.mataKuliah', 'pengampu.tahunAkademik', 'pengampu.mahasiswas'])->get();
        $rekap = $assessments->isEmpty() ? null : $this->calc->rekap($assessments);

        $format = $request->query('format', 'print');
        $title = 'Rekap Asesmen OBE';

        if ($format === 'excel') {
            return $this->exportExcel($rekap, $title);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('assessment.export', compact('rekap', 'title', 'filters'));

            return $pdf->download('rekap-asesmen-obe'.now()->format('YmdHis').'.pdf');
        }

        return view('assessment.export', compact('rekap', 'title', 'filters'));
    }

    /**
     * Kumpulan assessment sesuai filter & hak akses.
     */
    protected function scopedAssessments(array $filters, array $with = []): Builder
    {
        $user = auth()->user();

        $query = Assessment::query()
            ->with($with)
            ->with('pengampu.dosen.user')
            ->join('pengampus', 'pengampus.id', '=', 'assessments.pengampu_id')
            ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'pengampus.mata_kuliah_id');

        // Program studi -> kurikulum -> mata kuliah
        if ($filters['program_studi_id']) {
            $query->whereHas('pengampu.mataKuliah.kurikulum', fn ($q) => $q->where('program_studi_id', $filters['program_studi_id']));
        }
        if ($filters['kurikulum_id']) {
            $query->where('mata_kuliahs.kurikulum_id', $filters['kurikulum_id']);
        }
        if ($filters['mata_kuliah_id']) {
            $query->where('pengampus.mata_kuliah_id', $filters['mata_kuliah_id']);
        }
        if ($filters['tahun_akademik_id']) {
            $query->where('pengampus.tahun_akademik_id', $filters['tahun_akademik_id']);
        }
        if ($filters['semester']) {
            $query->where('pengampus.semester_akademik', $filters['semester']);
        }
        if ($filters['kelas']) {
            $query->where('pengampus.kelas', $filters['kelas']);
        }
        if ($filters['pengampu_id']) {
            $query->where('assessments.pengampu_id', $filters['pengampu_id']);
        }

        // Hak akses
        if (! $user->isAdmin() && ! $user->isDirektur()) {
            if ($user->isKaprodi()) {
                $query->whereHas('pengampu.mataKuliah.kurikulum', fn ($q) => $q->where('program_studi_id', $user->dosen?->program_studi_id));
            } else { // dosen
                $query->where('pengampus.dosen_id', $user->dosen?->id);
            }
        }

        return $query->select('assessments.*')->distinct();
    }

    /**
     * Kumpulan pengampu sesuai filter & hak akses.
     */
    protected function scopedPengampus(array $filters): Builder
    {
        $user = auth()->user();

        $query = Pengampu::query()
            ->with(['mataKuliah', 'dosen.user', 'tahunAkademik', 'assessment'])
            ->when($filters['tahun_akademik_id'], fn ($q) => $q->where('tahun_akademik_id', $filters['tahun_akademik_id']))
            ->when($filters['semester'], fn ($q) => $q->where('semester_akademik', $filters['semester']))
            ->when($filters['mata_kuliah_id'], fn ($q) => $q->where('mata_kuliah_id', $filters['mata_kuliah_id']))
            ->when($filters['kelas'], fn ($q) => $q->where('kelas', $filters['kelas']))
            ->when($filters['program_studi_id'], fn ($q) => $q->whereHas('mataKuliah.kurikulum', fn ($p) => $p->where('program_studi_id', $filters['program_studi_id'])))
            ->when($filters['kurikulum_id'], fn ($q) => $q->whereHas('mataKuliah.kurikulum', fn ($p) => $p->where('id', $filters['kurikulum_id'])));

        if (! $user->isAdmin() && ! $user->isDirektur()) {
            if ($user->isKaprodi()) {
                $query->whereHas('mataKuliah.kurikulum', fn ($q) => $q->where('program_studi_id', $user->dosen?->program_studi_id));
            } else { // dosen
                $query->where('dosen_id', $user->dosen?->id);
            }
        }

        return $query->orderBy('kelas');
    }

    protected function filters(Request $request): array
    {
        return [
            'tahun_akademik_id' => $request->integer('tahun_akademik_id') ?: null,
            'semester' => $request->query('semester'),
            'program_studi_id' => $request->integer('program_studi_id') ?: null,
            'kurikulum_id' => $request->integer('kurikulum_id') ?: null,
            'mata_kuliah_id' => $request->integer('mata_kuliah_id') ?: null,
            'kelas' => $request->query('kelas'),
            'pengampu_id' => $request->integer('pengampu_id') ?: null,
            'cpl_id' => $request->integer('cpl_id') ?: null,
            'cpmk_id' => $request->integer('cpmk_id') ?: null,
        ];
    }

    /**
     * Target capaian CPL/CPMK (default 75) dari Program Studi terpilih.
     */
    protected function capaianTarget(array $filters): float
    {
        if (! empty($filters['program_studi_id'])) {
            $target = ProgramStudi::find($filters['program_studi_id'])?->target_capaian;
        }

        return (float) ($target ?? 75);
    }

    /**
     * Data dropdown untuk formulir filter.
     */
    protected function filterDropdowns(array $filters): array
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi() ? (int) ($user->dosen?->program_studi_id ?? 0) : null;

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $kurikulums = Kurikulum::query()
            ->when($filters['program_studi_id'], fn ($q) => $q->where('program_studi_id', $filters['program_studi_id']))
            ->when($kaprodiProdiId, fn ($q) => $q->where('program_studi_id', $kaprodiProdiId))
            ->orderBy('nama_kurikulum')
            ->get();

        $mataKuliahs = MataKuliah::query()
            ->whereIn('id', Pengampu::query()->select('mata_kuliah_id'))
            ->when($filters['kurikulum_id'], fn ($q) => $q->where('kurikulum_id', $filters['kurikulum_id']))
            ->when($filters['program_studi_id'], fn ($q) => $q->whereHas('kurikulum', fn ($p) => $p->where('program_studi_id', $filters['program_studi_id'])))
            ->orderBy('kode')
            ->get();

        $kelasList = $this->scopedPengampus($filters)
            ->pluck('kelas')
            ->unique()
            ->filter()
            ->sort()
            ->values();

        $semesters = ['Ganjil', 'Genap'];

        return compact('tahunAkademiks', 'programStudis', 'kurikulums', 'mataKuliahs', 'kelasList', 'semesters');
    }

    protected function scoreRules(Collection $config): array
    {
        $rules = [];
        foreach ($config as $cpmkId => $meta) {
            $rules["scores.*.$cpmkId"] = 'nullable|numeric|min:0|max:'.$meta['bobot'];
        }

        return $rules;
    }

    protected function authorizeAssessmentRead(): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || $user->isDirektur() || $user->isKaprodi() || $user->isDosen(), 403);
    }

    protected function authorizeAssessmentWrite(): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || $user->isKaprodi() || ($user->isDosen() && ! $user->isDirektur()), 403);
    }

    protected function authorizePengampuWrite(Pengampu $pengampu): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless((int) $user->dosen?->program_studi_id === (int) ($pengampu->mataKuliah->kurikulum->program_studi_id ?? 0), 403);

            return;
        }

        abort_unless((int) $user->dosen?->id === (int) $pengampu->dosen_id, 403);
    }

    protected function dashboardKpis(Collection $assessments): array
    {
        $scores = $assessments->flatMap->scores;

        $mahasiswaCount = $assessments->flatMap(function ($a) {
            $scoreIds = $a->scores->pluck('mahasiswa_id');
            $pengampuIds = $a->pengampu ? $a->pengampu->mahasiswas->pluck('id') : collect();

            return $scoreIds->concat($pengampuIds);
        })->unique()->filter()->count();
        $mkCount = $assessments->pluck('pengampu.mata_kuliah_id')->unique()->count();
        $cpmkScored = $scores->filter(fn ($s) => is_numeric($s->nilai))->count();
        $cpls = Cpl::query()->count();

        $cplCapaian = [];
        if ($assessments->isNotEmpty()) {
            $cplSummary = $this->calc->cplSummary($assessments);
            foreach ($cplSummary as $cplId => $row) {
                $cplCapaian[$cplId] = [
                    'cpl' => $row['cpl'],
                    'avg' => $row['avg_capaian'],
                ];
            }
        }

        $capaianValues = collect($cplCapaian)->pluck('avg')->filter(fn ($v) => $v !== null);
        $avgCpl = $capaianValues->isEmpty() ? null : $capaianValues->avg();
        $tertinggi = $capaianValues->isNotEmpty() ? collect($cplCapaian)->sortByDesc('avg')->first() : null;
        $terendah = $capaianValues->isNotEmpty() ? collect($cplCapaian)->sortBy('avg')->first() : null;

        // Distribusi capaian mahasiswa (per MK capaian)
        $distribusi = ['<60' => 0, '60-69' => 0, '70-79' => 0, '80-100' => 0];
        foreach ($assessments as $assessment) {
            $mk = $assessment->pengampu->mataKuliah;
            $config = $this->calc->cpmkConfigForMataKuliah($mk);
            $maxMk = (float) $config->sum('bobot');

            $byMahasiswa = $assessment->scores->groupBy('mahasiswa_id');
            foreach ($byMahasiswa as $scores) {
                $nilai = $scores->filter(fn ($s) => is_numeric($s->nilai))->sum(fn ($s) => (float) $s->nilai);
                if ($maxMk <= 0) {
                    continue;
                }
                $c = ($nilai / $maxMk) * 100;
                if ($c < 60) {
                    $distribusi['<60']++;
                } elseif ($c < 70) {
                    $distribusi['60-69']++;
                } elseif ($c < 80) {
                    $distribusi['70-79']++;
                } else {
                    $distribusi['80-100']++;
                }
            }
        }

        return compact('mahasiswaCount', 'mkCount', 'cpmkScored', 'cpls', 'avgCpl', 'tertinggi', 'terendah', 'distribusi', 'cplCapaian');
    }

    protected function exportExcel(?array $rekap, string $title)
    {
        $xml = $this->buildExcelXml($rekap, $title);

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="rekap-asesmen-obe'.now()->format('YmdHis').'.xls"',
        ]);
    }

    protected function buildExcelXml(?array $rekap, string $title): string
    {
        $e = function (string $s): string {
            return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
        };

        $rows = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $rows[] = '<?mso-application progid="Excel.Sheet"?>';
        $rows[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $rows[] = '<Worksheet ss:Name="Rekap OBE"><Table>';

        // Header
        if ($rekap) {
            $header = ['NIM', 'Nama Mahasiswa'];
            foreach ($rekap['mata_kuliahs'] as $mks) {
                foreach ($mks['config'] as $cfg) {
                    $header[] = $mks['mata_kuliah']->kode.' '.$cfg['cpmk']->kode_cpmk.' ('.$cfg['bobot'].')';
                }
                $header[] = 'Nilai '.$mks['mata_kuliah']->kode.' ('.$mks['max'].')';
            }
            foreach ($rekap['cpls'] as $cplRow) {
                $header[] = 'Nilai '.$cplRow['cpl']->kode_cpl.' ('.$cplRow['max'].')';
                $header[] = 'Capaian '.$cplRow['cpl']->kode_cpl.' (%)';
            }
            $rows[] = '<Row>'.implode('', array_map(fn ($h) => '<Cell><Data ss:Type="String">'.$e($h).'</Data></Cell>', $header)).'</Row>';
        }

        if ($rekap) {
            // Scores per mahasiswa per MK
            $mkScores = [];
            foreach ($rekap['mata_kuliahs'] as $mks) {
                foreach ($mks['rows'] as $r) {
                    $mkScores[$r['mahasiswa']->id][$mks['mata_kuliah']->id] = $r;
                }
            }

            foreach ($rekap['mahasiswas'] as $mhs) {
                $cols = [$mhs->nim, $mhs->nama];

                foreach ($rekap['mata_kuliahs'] as $mks) {
                    $mkId = $mks['mata_kuliah']->id;
                    $row = $mkScores[$mhs->id][$mkId] ?? null;
                    foreach ($mks['config'] as $cpmkId => $cfg) {
                        $val = $row['scores'][$cpmkId]['nilai'] ?? null;
                        $cols[] = $val === null ? '' : number_format($val, 2);
                    }
                    $cols[] = $row ? number_format($row['nilai'], 2) : '';
                }

                foreach ($rekap['cpls'] as $cplRow) {
                    $ps = $cplRow['per_student'][$mhs->id] ?? null;
                    $cols[] = $ps ? number_format($ps['nilai'], 2) : '';
                    $cols[] = $ps && $ps['capaian'] !== null ? number_format($ps['capaian'], 2).'%' : '';
                }

                $rows[] = '<Row>'.implode('', array_map(fn ($c) => '<Cell><Data ss:Type="String">'.$e((string) $c).'</Data></Cell>', $cols)).'</Row>';
            }
        } else {
            $rows[] = '<Row><Cell><Data ss:Type="String">Tidak ada data assessment untuk filter ini.</Data></Cell></Row>';
        }

        $rows[] = '</Table></Worksheet></Workbook>';

        return implode("\n", $rows);
    }

    /**
     * Ajukan nilai kelas ke Kaprodi (hanya pengampu kelas).
     * Hanya bisa diajukan jika seluruh mahasiswa sudah memiliki nilai akhir.
     */
    public function ajukan(Pengampu $pengampu)
    {
        $user = auth()->user();
        if (! $user->isAdmin()) {
            abort_if(! $user->dosen || $pengampu->dosen_id !== $user->dosen->id, 403, 'Anda bukan pengampu kelas ini.');
        }

        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => $user->id]
        );

        $approval = $assessment->approval;

        if ($approval && $approval->status === AssessmentApproval::STATUS_MENUNGGU) {
            return back()->with('toast_error', 'Nilai kelas ini sudah diajukan dan masih menunggu persetujuan Kaprodi.');
        }

        if ($approval && $approval->status === AssessmentApproval::STATUS_DISETUJUI && $approval->buka_kunci_at === null) {
            return back()->with('toast_error', 'Nilai kelas telah disetujui Kaprodi dan terkunci. Buka kunci oleh admin terlebih dahulu.');
        }

        $belumDinilai = $this->jumlahBelumDinilai($pengampu);

        if ($belumDinilai > 0) {
            return back()->with('toast_error', "Nilai belum dapat diajukan: {$belumDinilai} mahasiswa belum memiliki nilai akhir.");
        }

        AssessmentApproval::updateOrCreate(
            ['assessment_id' => $assessment->id],
            [
                'status' => AssessmentApproval::STATUS_MENUNGGU,
                'diajukan_oleh' => $user->id,
                'diajukan_at' => now(),
                'catatan_revisi' => null,
                'direvisi_oleh' => null,
                'direvisi_at' => null,
            ]
        );

        $pengaju = $user->name ?? 'Dosen';

        $kaprodis = Dosen::where('jabatan', 'Kaprodi')
            ->where('program_studi_id', $pengampu->mataKuliah->kurikulum->program_studi_id)
            ->with('user')
            ->get();

        foreach ($kaprodis as $kaprodi) {
            if ($kaprodi->user) {
                $kaprodi->user->notify(new NilaiDiajukan($pengampu, $pengaju));
            }
        }

        return back()->with('toast_success', 'Nilai berhasil diajukan ke Kaprodi.');
    }

    /**
     * Daftar pengajuan nilai untuk Kaprodi / Direktur (meniru pengajuan RPS).
     */
    public function pengajuan()
    {
        $user = auth()->user();

        if ($user) {
            $user->unreadNotifications()
                ->where('type', NilaiDiajukan::class)
                ->update(['read_at' => now()]);
        }

        $approvals = AssessmentApproval::query()
            ->with(['assessment.pengampu.mataKuliah', 'assessment.pengampu.dosen.user', 'assessment.pengampu.tahunAkademik', 'penyetuju'])
            ->when(! $user->isDirektur() && $user->dosen, function ($query) use ($user) {
                $query->whereHas('assessment.pengampu.mataKuliah.kurikulum', function ($q) use ($user) {
                    $q->where('program_studi_id', $user->dosen->program_studi_id);
                });
            })
            ->latest()
            ->get();

        return view('assessment.pengajuan', compact('approvals'));
    }

    public function setujui(Assessment $assessment)
    {
        $approval = $assessment->approval;

        if (! $approval || $approval->status !== AssessmentApproval::STATUS_MENUNGGU) {
            return back()->with('error', 'Hanya nilai berstatus menunggu yang dapat disetujui.');
        }

        $approval->update([
            'status' => AssessmentApproval::STATUS_DISETUJUI,
            'disetujui_oleh' => auth()->id(),
            'disetujui_at' => now(),
        ]);

        $penyetuju = auth()->user()->name ?? 'Kaprodi';
        $pengampu = $assessment->pengampu;

        if ($pengampu->dosen?->user) {
            $pengampu->dosen->user->notify(new NilaiDisetujui($pengampu, $penyetuju));
        }

        return back()->with('success', 'Nilai kelas berhasil disetujui.');
    }

    public function kunci(Pengampu $pengampu)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $approval = $pengampu->assessment?->approval;

        if (! $approval || $approval->status !== AssessmentApproval::STATUS_DISETUJUI) {
            return back()->with('toast_error', 'Hanya rekap nilai berstatus disetujui yang dapat dibuka atau dikunci kembali oleh admin.');
        }

        $terbuka = $approval->buka_kunci_at !== null;

        $approval->update([
            'buka_kunci_oleh' => $terbuka ? null : auth()->id(),
            'buka_kunci_at' => $terbuka ? null : now(),
        ]);

        return back()->with('toast_success', $terbuka
            ? 'Rekap nilai dikunci kembali. Dosen tidak dapat mengedit nilai kelas ini.'
            : 'Kunci rekap nilai dibuka. Dosen dapat mengedit nilai kelas ini kembali.');
    }

    public function revisi(Request $request, Assessment $assessment)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        $approval = $assessment->approval;

        if (! $approval || $approval->status !== AssessmentApproval::STATUS_MENUNGGU) {
            return back()->with('error', 'Hanya nilai berstatus menunggu yang dapat dikembalikan.');
        }

        $approval->update([
            'status' => AssessmentApproval::STATUS_DIREVISI,
            'catatan_revisi' => $request->catatan_revisi,
            'direvisi_oleh' => auth()->id(),
            'direvisi_at' => now(),
        ]);

        $peninjau = auth()->user()->name ?? 'Kaprodi';
        $pengampu = $assessment->pengampu;

        if ($pengampu->dosen?->user) {
            $pengampu->dosen->user->notify(new NilaiDirevisi($pengampu, $request->catatan_revisi, $peninjau));
        }

        return back()->with('success', 'Nilai dikembalikan untuk direvisi.');
    }

    /**
     * Jumlah mahasiswa aktif di kelas yang belum memiliki nilai akhir.
     */
    protected function jumlahBelumDinilai(Pengampu $pengampu): int
    {
        $mahasiswaIds = $pengampu->mahasiswas()->pluck('mahasiswas.id');

        $sudahDinilai = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->where('komponen', 'akhir')
            ->whereNotNull('nilai')
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->distinct()
            ->count('mahasiswa_id');

        return max($mahasiswaIds->count() - $sudahDinilai, 0);
    }
}
