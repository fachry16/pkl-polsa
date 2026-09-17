<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Assessment;
use App\Models\Kurikulum;
use App\Models\TahunAkademik;
use App\Services\AssessmentCalculationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EvaluasiKurikulumController extends Controller
{
    use AuthorizesKurikulum;

    public function __construct(
        private AssessmentCalculationService $calc
    ) {}

    /**
     * Analisis capaian CPL lintas MK, kontribusi MK per CPL, dan identifikasi CPMK lemah.
     */
    public function analisis(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $filters = [
            'tahun_akademik_id' => $request->integer('tahun_akademik_id') ?: null,
            'mata_kuliah_id' => $request->integer('mata_kuliah_id') ?: null,
        ];

        $assessments = $this->analisisAssessments($kurikulum, $filters)->get();
        $drop = $this->analisisDropdowns($kurikulum);
        $target = $this->capaianTarget($kurikulum);

        if ($assessments->isEmpty()) {
            return view('evaluasi-kurikulum.analisis', compact('kurikulum', 'filters', 'drop', 'target'))->with('evaluasi', null);
        }

        $cplSummary = $this->calc->cplSummary($assessments);
        $mkSummaries = $assessments->map(fn ($a) => [
            'assessment' => $a,
            'mata_kuliah' => $a->pengampu->mataKuliah,
            'summary' => $this->calc->mkSummary($a, $a->pengampu->mahasiswas),
        ]);

        $mkCplContribution = $this->buildMkCplContribution($mkSummaries, $cplSummary);
        $cpmkLemah = $this->buildCpmkLemah($mkSummaries, $target);

        $capaianValues = collect($cplSummary)->pluck('avg_capaian')->filter(fn ($v) => $v !== null);
        $evaluasi = [
            'cpl_summary' => $cplSummary,
            'mk_summaries' => $mkSummaries,
            'mk_cpl_contribution' => $mkCplContribution,
            'cpmk_lemah' => $cpmkLemah,
            'mk_terassess' => $mkSummaries->count(),
            'rata_rata_kurikulum' => $capaianValues->isEmpty() ? null : $capaianValues->avg(),
            'cpl_tercapai' => collect($cplSummary)->filter(fn ($r) => ($r['avg_capaian'] ?? 0) >= $target)->count(),
            'cpl_belum' => collect($cplSummary)->filter(fn ($r) => ($r['avg_capaian'] ?? 0) < $target || $r['avg_capaian'] === null)->count(),
            'total_cpl' => count($cplSummary),
        ];

        return view('evaluasi-kurikulum.analisis', compact('kurikulum', 'filters', 'drop', 'target', 'evaluasi'));
    }

    /**
     * Assessment yang termasuk ke dalam sebuah kurikulum beserta filter tahun akademik & MK.
     */
    protected function analisisAssessments(Kurikulum $kurikulum, array $filters): Builder
    {
        return Assessment::query()
            ->with(['scores', 'pengampu.mataKuliah', 'pengampu.tahunAkademik', 'pengampu.mahasiswas'])
            ->whereHas('pengampu.mataKuliah', fn ($q) => $q->where('kurikulum_id', $kurikulum->id))
            ->when($filters['tahun_akademik_id'], fn ($q) => $q->whereHas('pengampu', fn ($p) => $p->where('tahun_akademik_id', $filters['tahun_akademik_id'])))
            ->when($filters['mata_kuliah_id'], fn ($q) => $q->whereHas('pengampu', fn ($p) => $p->where('mata_kuliah_id', $filters['mata_kuliah_id'])));
    }

    protected function analisisDropdowns(Kurikulum $kurikulum): array
    {
        return [
            'tahunAkademiks' => TahunAkademik::orderByDesc('tahun')->get(),
            'mataKuliahs' => $kurikulum->mataKuliahs()->orderBy('kode')->get(),
        ];
    }

    /**
     * Target capaian CPL/CPMK (default 75) dari Program Studi Kurikulum.
     */
    protected function capaianTarget(Kurikulum $kurikulum): float
    {
        return (float) ($kurikulum->programStudi?->target_capaian ?? 75);
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
}
