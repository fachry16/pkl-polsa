<?php

namespace App\Http\Controllers;

use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class TranskripController extends Controller
{
    private const HURUF_INDEX = [
        'A' => 4.00,
        'A-' => 3.70,
        'B+' => 3.40,
        'B' => 3.00,
        'B-' => 2.70,
        'C+' => 2.40,
        'C' => 2.00,
        'D' => 1.00,
        'E' => 0.00,
    ];

    public function saya(Request $request)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        abort_unless($mahasiswa, 404);

        return view('transkrip.index', $this->dataView($request, $mahasiswa));
    }

    public static function konversiHuruf(?float $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        if ($nilai >= 80) {
            return 'A';
        }
        if ($nilai >= 75) {
            return 'A-';
        }
        if ($nilai >= 70) {
            return 'B+';
        }
        if ($nilai >= 65) {
            return 'B';
        }
        if ($nilai >= 60) {
            return 'B-';
        }
        if ($nilai >= 55) {
            return 'C+';
        }
        if ($nilai >= 50) {
            return 'C';
        }
        if ($nilai >= 40) {
            return 'D';
        }

        return 'E';
    }

    public static function konversiIndeks(?float $nilai): ?float
    {
        $huruf = self::konversiHuruf($nilai);

        return $huruf ? self::HURUF_INDEX[$huruf] : null;
    }

    protected function dataView(Request $request, Mahasiswa $mahasiswa): array
    {
        $data = $this->dataTranskrip($mahasiswa, $request);

        return [
            'mahasiswa' => $mahasiswa,
            'tahunAkademiks' => TahunAkademik::orderByDesc('tahun')->get(),
            'semesterAkademiks' => ['Ganjil', 'Genap', 'Pendek'],
        ] + $data;
    }

    protected function dataTranskrip(Mahasiswa $mahasiswa, Request $request): array
    {
        $taId = $request->query('tahun_akademik_id');
        $semesterAkademik = $request->query('semester_akademik');
        $search = trim((string) $request->query('cari'));

        $pengampus = $mahasiswa->pengampus()
            ->with(['mataKuliah', 'tahunAkademik'])
            ->whereHas('mataKuliah', function ($q) use ($search) {
                if ($search !== '') {
                    $q->where(fn ($qq) => $qq->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%"));
                }
            })
            ->when($taId, fn ($q) => $q->where('tahun_akademik_id', $taId))
            ->when($semesterAkademik, fn ($q) => $q->where('semester_akademik', $semesterAkademik))
            ->get();

        $nilaiAkhir = LmsNilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('komponen', 'akhir')
            ->pluck('nilai', 'pengampu_id');

        $semesterOrder = ['Ganjil' => 1, 'Genap' => 2, 'Pendek' => 3];

        $rows = $pengampus
            ->map(function ($p) use ($nilaiAkhir) {
                $mk = $p->mataKuliah;
                $sks = $p->total_sks;
                $nilai = isset($nilaiAkhir[$p->id]) ? (float) $nilaiAkhir[$p->id] : null;

                $indeks = self::konversiIndeks($nilai);

                return [
                    'pengampu' => $p,
                    'kode' => $mk?->kode ?? '-',
                    'nama' => $mk?->nama ?? '-',
                    'sks' => $sks,
                    'tahun' => $p->tahunAkademik?->tahun,
                    'tahun_akademik' => $p->tahunAkademik?->tahun.' '.ucfirst($p->tahunAkademik?->semester ?? ''),
                    'semester_akademik' => $p->semester_akademik,
                    'nilai_akhir' => $nilai,
                    'huruf' => self::konversiHuruf($nilai),
                    'indeks' => $indeks,
                    'sks_indeks' => $indeks !== null ? round($sks * $indeks, 2) : 0.0,
                    'is_sync' => $nilai !== null,
                ];
            })
            ->sortBy(fn ($r) => ($r['tahun'] ?? '0').'-'.($semesterOrder[$r['semester_akademik'] ?? ''] ?? 9))
            ->values();

        $terisi = $rows->where('is_sync', true);

        $totalSks = round($terisi->sum('sks'), 2);
        $totalNilai = round($terisi->sum('sks_indeks'), 2);
        $ipk = $totalSks > 0 ? round($totalNilai / $totalSks, 2) : 0.0;

        $summary = ['total_sks' => $totalSks, 'total_nilai' => $totalNilai, 'ipk' => $ipk];

        $byTa = $terisi
            ->groupBy(fn ($r) => $r['tahun_akademik'] ?? '-')
            ->map(function ($items, $label) {
                $sks = (float) $items->sum('sks');
                $mutu = (float) $items->sum('sks_indeks');
                $ips = $sks > 0 ? round($mutu / $sks, 2) : 0.0;

                return [
                    'label' => $label,
                    'jumlah_mk' => $items->count(),
                    'total_sks' => $sks,
                    'total_nilai' => $mutu,
                    'ips' => $ips,
                ];
            })
            ->values();

        $perPage = 25;
        $currentPage = Paginator::resolveCurrentPage();
        $rowsPage = new LengthAwarePaginator(
            $rows->forPage($currentPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return [
            'rows' => $rows,
            'summary' => $summary,
            'rowsPage' => $rowsPage,
            'ipk' => $ipk,
            'byTa' => $byTa,
        ];
    }
}
