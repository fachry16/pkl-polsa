<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\EvaluasiKurikulum;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
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
            return view('assessment.rekap', compact('filters', 'drop'))->with('rekap', null);
        }

        $rekap = $this->calc->rekap($assessments);

        return view('assessment.rekap', compact('filters', 'drop', 'rekap'));
    }

    /**
     * Evaluasi kurikulum: capaian CPL lintas MK, kontribusi MK per CPL, identifikasi CPMK lemah.
     */
    public function evaluasiKurikulum(Request $request)
    {
        $this->authorizeAssessmentRead();

        $filters = $this->filters($request);
        $assessments = $this->scopedAssessments($filters, ['scores', 'pengampu.mataKuliah', 'pengampu.tahunAkademik', 'pengampu.mahasiswas'])->get();
        $drop = $this->filterDropdowns($filters);

        if ($assessments->isEmpty()) {
            return view('assessment.evaluasi-kurikulum', compact('filters', 'drop'))->with('evaluasi', null);
        }

        $cplSummary = $this->calc->cplSummary($assessments);
        $mkSummaries = $assessments->map(fn ($a) => [
            'assessment' => $a,
            'mata_kuliah' => $a->pengampu->mataKuliah,
            'summary' => $this->calc->mkSummary($a, $a->pengampu->mahasiswas),
        ]);

        $mkCplContribution = $this->buildMkCplContribution($mkSummaries, $cplSummary);
        $cpmkLemah = $this->buildCpmkLemah($mkSummaries);

        $capaianValues = collect($cplSummary)->pluck('avg_capaian')->filter(fn ($v) => $v !== null);
        $evaluasi = [
            'cpl_summary' => $cplSummary,
            'mk_summaries' => $mkSummaries,
            'mk_cpl_contribution' => $mkCplContribution,
            'cpmk_lemah' => $cpmkLemah,
            'mk_terassess' => $mkSummaries->count(),
            'rata_rata_kurikulum' => $capaianValues->isEmpty() ? null : $capaianValues->avg(),
            'cpl_tercapai' => collect($cplSummary)->filter(fn ($r) => ($r['avg_capaian'] ?? 0) >= 70)->count(),
            'cpl_belum' => collect($cplSummary)->filter(fn ($r) => ($r['avg_capaian'] ?? 0) < 70 || $r['avg_capaian'] === null)->count(),
            'total_cpl' => count($cplSummary),
        ];

        return view('assessment.evaluasi-kurikulum', compact('filters', 'drop', 'evaluasi'));
    }

    /**
     * Daftar evaluasi kurikulum (CRUD) dalam konteks assessment.
     */
    public function evaluasiList(Request $request)
    {
        $this->authorizeAssessmentRead();

        $filters = $this->filters($request);
        $drop = $this->filterDropdowns($filters);

        $query = EvaluasiKurikulum::query()
            ->with(['kurikulum.programStudi', 'tahunAkademik', 'creator'])
            ->orderByDesc('created_at');

        if ($filters['kurikulum_id']) {
            $query->where('kurikulum_id', $filters['kurikulum_id']);
        } elseif ($filters['program_studi_id']) {
            $query->whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $filters['program_studi_id']));
        }

        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isDirektur()) {
            if ($user->isKaprodi()) {
                $query->whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $user->dosen?->program_studi_id));
            }
        }

        $evaluasis = $query->get();

        return view('assessment.evaluasi-list', compact('filters', 'drop', 'evaluasis'));
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
            'cpl_id' => $request->integer('cpl_id') ?: null,
            'cpmk_id' => $request->integer('cpmk_id') ?: null,
        ];
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

    /**
     * Kontribusi rata-rata capaian per MK ke CPL-nya.
     */
    protected function buildMkCplContribution(Collection $mkSummaries, array $cplSummary): array
    {
        $result = [];

        foreach ($mkSummaries as $item) {
            $summary = $item['summary'];
            $mk = $item['mata_kuliah'];

            $rows = $summary['rows'];
            $totalMahasiswa = count($rows);
            if ($totalMahasiswa === 0) {
                continue;
            }

            $perCpl = [];
            foreach ($summary['config'] as $cpmkId => $meta) {
                $cplId = $meta['cpl']->id;
                $capaianValues = collect($rows)
                    ->map(fn ($r) => $r['scores'][$cpmkId]['nilai'] ?? null)
                    ->filter(fn ($v) => $v !== null)
                    ->values();

                $avgNilai = $capaianValues->isEmpty() ? null : $capaianValues->avg();
                $bobot = $meta['bobot'];
                $perCpl[$cplId] = [
                    'cpl_kode' => $meta['cpl']->kode_cpl,
                    'cpmk_kode' => $meta['cpmk']->kode_cpmk,
                    'bobot' => $bobot,
                    'avg_nilai' => $avgNilai,
                    'capaian' => $bobot > 0 && $avgNilai !== null ? ($avgNilai / $bobot) * 100 : null,
                ];
            }

            $maxMk = (float) $summary['max'];
            $avgCapaian = collect($rows)->pluck('capaian')->filter(fn ($v) => $v !== null)->avg();

            $result[] = [
                'mata_kuliah' => $mk,
                'max_mk' => $maxMk,
                'avg_capaian' => $avgCapaian,
                'per_cpl' => $perCpl,
            ];
        }

        usort($result, fn ($a, $b) => ($a['avg_capaian'] ?? 0) <=> ($b['avg_capaian'] ?? 0));

        return $result;
    }

    /**
     * CPMK-CPMK yang capaian avg-nya di bawah threshold.
     */
    protected function buildCpmkLemah(Collection $mkSummaries, float $threshold = 70): array
    {
        $cpmkData = [];

        foreach ($mkSummaries as $item) {
            $summary = $item['summary'];
            $mk = $item['mata_kuliah'];
            $rows = $summary['rows'];

            foreach ($summary['config'] as $cpmkId => $meta) {
                $capaianValues = collect($rows)
                    ->map(fn ($r) => $r['scores'][$cpmkId]['nilai'] ?? null)
                    ->filter(fn ($v) => $v !== null);

                if ($capaianValues->isEmpty()) {
                    continue;
                }

                $avg = $capaianValues->avg();
                $bobot = $meta['bobot'];
                $capaian = $bobot > 0 ? ($avg / $bobot) * 100 : null;

                if ($capaian !== null && $capaian < $threshold) {
                    $cpmkData[] = [
                        'mata_kuliah' => $mk,
                        'cpmk' => $meta['cpmk'],
                        'cpl' => $meta['cpl'],
                        'bobot' => $bobot,
                        'avg_nilai' => $avg,
                        'capaian' => $capaian,
                    ];
                }
            }
        }

        usort($cpmkData, fn ($a, $b) => $a['capaian'] <=> $b['capaian']);

        return $cpmkData;
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
}
