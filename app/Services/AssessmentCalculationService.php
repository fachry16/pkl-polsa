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
            ->with(['cpl', 'cpmk', 'mataKuliah'])
            ->get();

        // Per CPL: cpmk_id -> ['cpl', 'cpmk', 'mata_kuliah', 'bobot']
        $byCpl = [];
        foreach ($configRows as $row) {
            $byCpl[$row->cpl_id][$row->cpmk_id] = [
                'cpl' => $row->cpl,
                'cpmk' => $row->cpmk,
                'mata_kuliah' => $row->mataKuliah,
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

    /**
     * Statistik per kolom capaian: rata-rata, tertinggi, terendah, standar deviasi (populasi).
     *
     * @param  Collection<int, float>  $values
     * @return array{rata: ?float, tertinggi: ?float, terendah: ?float, std_dev: ?float, jumlah: int}
     */
    public function statsOf(Collection $values): array
    {
        $clean = $values->filter(fn ($v) => $v !== null)
            ->values()
            ->map(fn ($v) => (float) $v);

        if ($clean->isEmpty()) {
            return ['rata' => null, 'tertinggi' => null, 'terendah' => null, 'std_dev' => null, 'jumlah' => 0];
        }

        $mean = $clean->avg();
        $stdDev = $clean->count() === 1
            ? 0.0
            : sqrt($clean->map(fn ($v) => ($v - $mean) ** 2)->avg());

        return [
            'rata' => round($mean, 2),
            'tertinggi' => round($clean->max(), 2),
            'terendah' => round($clean->min(), 2),
            'std_dev' => round($stdDev, 2),
            'jumlah' => $clean->count(),
        ];
    }

    /**
     * Konversi capaian (0-100) ke huruf mutu sesuai standar prodi.
     * A >= 85 | AB >= 80 | B >= 70 | BC >= 65 | C >= 60 | D >= 55 | E < 55.
     */
    public function gradeOf(?float $capaian): string
    {
        if ($capaian === null) {
            return '—';
        }

        return match (true) {
            $capaian >= 85 => 'A',
            $capaian >= 80 => 'AB',
            $capaian >= 70 => 'B',
            $capaian >= 65 => 'BC',
            $capaian >= 60 => 'C',
            $capaian >= 55 => 'D',
            default => 'E',
        };
    }

    /**
     * Distribusi huruf mutu seluruh mahasiswa×MK berdasarkan capaian MK.
     *
     * @return array<string, array{jumlah: int, persen: float}> keyed by huruf (A, AB, B, BC, C, D, E)
     */
    public function distribusiGrade(Collection $mkSummaries): array
    {
        $grades = ['A' => 0, 'AB' => 0, 'B' => 0, 'BC' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        $totalRow = 0;

        foreach ($mkSummaries as $mks) {
            foreach ($mks['rows'] as $row) {
                if ($row['capaian'] === null) {
                    continue;
                }
                $grades[$this->gradeOf($row['capaian'])]++;
                $totalRow++;
            }
        }

        return collect($grades)
            ->map(fn ($jumlah) => [
                'jumlah' => $jumlah,
                'persen' => $totalRow > 0 ? round(($jumlah / $totalRow) * 100, 1) : 0.0,
            ])
            ->toArray();
    }

    /**
     * Rekapitulasi CPMK per MK (tingkat kelas).
     *
     * Untuk setiap CPMK: capaian per mahasiswa = (nilai / bobot_maks) × 100;
     * lalu statistik kelas, jumlah/% tercapai, status, dan keterangan tindak lanjut.
     *
     * @return array<int, array> daftar per MK -> per CPMK
     */
    public function cpmkRecap(Collection $mkSummaries, float $target): array
    {
        $recaps = [];

        foreach ($mkSummaries as $mks) {
            $mkRecaps = [];

            foreach ($mks['config'] as $cpmkId => $meta) {
                $bobot = $meta['bobot'];
                $capaians = [];

                foreach ($mks['rows'] as $row) {
                    $nilai = $row['scores'][$cpmkId]['nilai'] ?? null;

                    if ($nilai === null || $bobot <= 0) {
                        continue;
                    }

                    $capaians[] = ($nilai / $bobot) * 100;
                }

                $capaians = collect($capaians);
                $stats = $this->statsOf($capaians);
                $tercapai = $capaians->filter(fn ($c) => $c >= $target)->count();

                $mkRecaps[] = [
                    'cpmk' => $meta['cpmk'],
                    'cpl' => $meta['cpl'],
                    'bobot' => $bobot,
                    'target' => $target,
                    'stats' => $stats,
                    'tercapai' => $tercapai,
                    'persen_tercapai' => $stats['jumlah'] > 0 ? round(($tercapai / $stats['jumlah']) * 100, 1) : 0.0,
                    'status' => $this->recapStatus($stats['rata'], $target),
                    'tindak_lanjut' => $this->tindakLanjut($stats['rata'], $target),
                ];
            }

            $recaps[] = [
                'mata_kuliah' => $mks['mata_kuliah'],
                'max' => $mks['max'],
                'recaps' => $mkRecaps,
            ];
        }

        return $recaps;
    }

    /**
     * Rekapitulasi CPL lintas MK (tingkat kelas).
     *
     * CPL dihitung dari CAPAIAN PER MAHASISWA hasil cplSummary (bukan rata-rata capaian MK),
     * termasuk daftar MK pendukung dan total bobot maksimum CPL.
     *
     * @return array<int, array> daftar per CPL
     */
    public function cplRecap(array $cplSummary, float $target): array
    {
        $recaps = [];

        foreach ($cplSummary as $cplId => $row) {
            $capaians = collect($row['per_student'])->pluck('capaian');
            $stats = $this->statsOf($capaians);
            $tercapai = $capaians->filter(fn ($c) => $c !== null && $c >= $target)->count();

            $mkPendukung = collect($row['cpmks'])
                ->pluck('mata_kuliah')
                ->unique('id')
                ->values();

            $recaps[] = [
                'cpl' => $row['cpl'],
                'mk_pendukung' => $mkPendukung,
                'cpmks' => $row['cpmks'],
                'max' => $row['max'],
                'target' => $target,
                'stats' => $stats,
                'tercapai' => $tercapai,
                'persen_tercapai' => $stats['jumlah'] > 0 ? round(($tercapai / $stats['jumlah']) * 100, 1) : 0.0,
                'status' => $this->recapStatus($stats['rata'], $target),
                'tindak_lanjut' => $this->tindakLanjut($stats['rata'], $target),
            ];
        }

        return $recaps;
    }

    /**
     * Status tercapai/belum berdasarkan rata-rata kelas vs target.
     */
    protected function recapStatus(?float $rata, float $target): string
    {
        if ($rata === null) {
            return 'Belum Dinilai';
        }

        return $rata >= $target ? 'Tercapai' : 'Belum Tercapai';
    }

    /**
     * Keterangan tindak lanjut otomatis bila belum tercapai.
     */
    protected function tindakLanjut(?float $rata, float $target): string
    {
        if ($rata === null) {
            return 'Belum ada data penilaian untuk diukur.';
        }

        if ($rata >= $target) {
            return 'Pertahankan capaian; siapkan pengayaan bagi mahasiswa yang sanggup melampaui target.';
        }

        $selisih = round($target - $rata, 1);

        if ($rata < $target * 0.5) {
            return "Rata-rata kelas {$rata}% jauh di bawah target {$target}% (selisih {$selisih}%). Lakukan remedial menyeluruh, revisi metode ajar, dan evaluasi kesesuaian soal/materi.";
        }

        return "Rata-rata kelas {$rata}% di bawah target {$target}% (selisih {$selisih}%). Berikan remedial/pengayaan pada mahasiswa yang belum mencapai CPMK/CPL terkait dan tinjau metode ajar.";
    }
}
