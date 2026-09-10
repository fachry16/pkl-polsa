<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\RumusanNilaiAkhirMk;
use Illuminate\Support\Collection;

/**
 * Satu-satunya sumber perhitungan Asesmen OBE.
 *
 * Konfigurasi bobot CPMK diambil dari tabel `rumusan_nilai_akhir_mks`
 * (relasi CPMK-CPL-MataKuliah beserta skor_maks/bobot), bukan di-hardcode.
 *
 * Rumus:
 *   nilai MK      = Σ nilai CPMK milik mata kuliah tersebut
 *   max MK        = Σ bobot CPMK milik mata kuliah tersebut
 *   capaian MK    = (nilai MK / max MK) × 100
 *   nilai CPL     = Σ nilai CPMK yang terhubung ke CPL tsb (lintas mata kuliah)
 *   max CPL       = Σ bobot CPMK yang terhubung ke CPL tsb
 *   capaian CPL   = (nilai CPL / max CPL) × 100
 */
class AssessmentCalculationService
{
    /**
     * Konfigurasi CPMK (cpmk -> ['cpl' => Cpl, 'bobot' => float]) untuk satu mata kuliah.
     */
    public function cpmkConfigForMataKuliah(MataKuliah $mk): Collection
    {
        return RumusanNilaiAkhirMk::query()
            ->where('mata_kuliah_id', $mk->id)
            ->with(['cpmk', 'cpl'])
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->cpmk_id => [
                    'cpmk' => $row->cpmk,
                    'cpl' => $row->cpl,
                    'bobot' => (float) $row->skor_maks,
                ]];
            });
    }

    /**
     * Daftar skor CPMK per mahasiswa untuk sebuah assessment.
     *
     * @return array<int, array> keyed by mahasiswa_id -> [cpmk_id => ?float]
     */
    public function scoresPerMahasiswa(Assessment $assessment): array
    {
        $map = [];

        foreach ($assessment->scores as $score) {
            $map[$score->mahasiswa_id][$score->cpmk_id] = is_numeric($score->nilai)
                ? (float) $score->nilai
                : null;
        }

        return $map;
    }

    /**
     * Ringkasan per mata kuliah (satu assessment):
     * per mahasiswa -> cpmk scores, nilai MK, max MK, capaian MK.
     */
    public function mkSummary(Assessment $assessment, Collection $mahasiswas): array
    {
        $mk = $assessment->pengampu->mataKuliah;
        $config = $this->cpmkConfigForMataKuliah($mk);

        $scoresMap = $this->scoresPerMahasiswa($assessment);

        $maxMk = (float) $config->sum('bobot');

        $rows = [];

        foreach ($mahasiswas as $mahasiswa) {
            $scores = $scoresMap[$mahasiswa->id] ?? [];

            $row = [
                'mahasiswa' => $mahasiswa,
                'scores' => [],
                'nilai' => 0.0,
                'max' => $maxMk,
                'capaian' => null,
                'dinilai' => false,
            ];

            foreach ($config as $cpmkId => $meta) {
                $nilai = $scores[$cpmkId] ?? null;
                $row['scores'][$cpmkId] = [
                    'cpmk' => $meta['cpmk'],
                    'cpl' => $meta['cpl'],
                    'bobot' => $meta['bobot'],
                    'nilai' => $nilai,
                ];

                if ($nilai !== null) {
                    $row['nilai'] += $nilai;
                    $row['dinilai'] = true;
                }
            }

            if ($row['dinilai'] && $maxMk > 0) {
                $row['capaian'] = ($row['nilai'] / $maxMk) * 100;
            }

            $rows[] = $row;
        }

        return [
            'mata_kuliah' => $mk,
            'config' => $config,
            'max' => $maxMk,
            'rows' => $rows,
        ];
    }

    /**
     * Nilai & capaian CPL lintas mata kuliah (beberapa assessment).
     *
     * CPL dihitung dengan menjumlahkan nilai CPMK (dari semua MK yang terpilih)
     * yang memang terhubung ke CPL tersebut.
     */
    public function cplSummary(Collection $assessments): array
    {
        $assessments = $assessments->values();

        $mataKuliahIds = $assessments
            ->pluck('pengampu.mata_kuliah_id')
            ->unique()
            ->values();

        // Kumpulkan seluruh relasi CPMK-CPL lintas MK yang terpilih.
        $configRows = RumusanNilaiAkhirMk::query()
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->with(['cpl', 'cpmk'])
            ->get();

        // Per CPL: cpmk_id -> ['cpl', 'cpmk', 'bobot']
        $byCpl = [];
        foreach ($configRows as $row) {
            $byCpl[$row->cpl_id][$row->cpmk_id] = [
                'cpl' => $row->cpl,
                'cpmk' => $row->cpmk,
                'bobot' => (float) $row->skor_maks,
            ];
        }

        // Nilai CPMK per mahasiswa lintas semua assessment.
        $studentScores = [];
        foreach ($assessments as $assessment) {
            foreach ($assessment->scores as $score) {
                if (! is_numeric($score->nilai)) {
                    continue;
                }
                $studentScores[$score->mahasiswa_id][$score->cpmk_id] = (float) $score->nilai;
            }
        }

        $mahasiswaIds = collect($studentScores)->keys();

        $result = [];

        foreach ($byCpl as $cplId => $cpmks) {
            $maxCpl = (float) collect($cpmks)->sum('bobot');

            $perStudent = [];

            foreach ($studentScores as $mahasiswaId => $scores) {
                $nilai = 0.0;
                $dinilai = false;

                foreach ($cpmks as $cpmkId => $meta) {
                    if (isset($scores[$cpmkId])) {
                        $nilai += $scores[$cpmkId];
                        $dinilai = true;
                    }
                }

                $perStudent[$mahasiswaId] = [
                    'nilai' => $nilai,
                    'max' => $maxCpl,
                    'capaian' => ($dinilai && $maxCpl > 0) ? ($nilai / $maxCpl) * 100 : null,
                    'dinilai' => $dinilai,
                ];
            }

            $result[$cplId] = [
                'cpl' => collect($cpmks)->first()['cpl'],
                'cpmks' => $cpmks,
                'max' => $maxCpl,
                'per_student' => $perStudent,
                'mahasiswa_ids' => $mahasiswaIds,
                'avg_capaian' => $this->averageCapaian($perStudent),
            ];
        }

        return $result;
    }

    /**
     * Rekap terstruktur: mahasiswa -> MK (cpmk + nilai MK) -> CPL (nilai + capaian).
     *
     * @param  Collection<int, Assessment>  $assessments
     */
    public function rekap(Collection $assessments): array
    {
        $assessments = $assessments->values();

        // Gabungkan mahasiswa yang muncul di semua assessment terpilih (baik dari skor maupun kelas pengampu).
        $mahasiswaIds = $assessments
            ->flatMap(function ($a) {
                $scoreIds = $a->scores->pluck('mahasiswa_id');
                $pengampuIds = $a->pengampu ? $a->pengampu->mahasiswas->pluck('id') : collect();

                return $scoreIds->concat($pengampuIds);
            })
            ->unique()
            ->filter()
            ->values();

        $mahasiswas = Mahasiswa::whereIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        $mkSummaries = $assessments->map(fn ($a) => $this->mkSummary($a, $mahasiswas));
        $cplSummary = $this->cplSummary($assessments);

        return [
            'mahasiswas' => $mahasiswas,
            'mata_kuliahs' => $mkSummaries,
            'cpls' => $cplSummary,
        ];
    }

    protected function averageCapaian(array $perStudent): ?float
    {
        $capaians = collect($perStudent)->pluck('capaian')->filter(fn ($v) => $v !== null);

        if ($capaians->isEmpty()) {
            return null;
        }

        return $capaians->avg();
    }
}
