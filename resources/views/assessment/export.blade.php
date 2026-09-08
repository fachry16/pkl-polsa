<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; margin: 24px; color: #1e293b; font-size: 11px; }
    h1 { font-size: 18px; margin: 0 0 4px; }
    .subtitle { color: #64748b; font-size: 12px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 10px; }
    th { background: #f1f5f9; text-align: center; }
    td.nilai { text-align: right; }
    .stick { text-align: left; white-space: nowrap; }
    .mk-head { background: #eef2ff; }
    .cpl-head { background: #ecfdf5; }
    .strong { font-weight: 800; }
    .green { color: #059669; }
    .amber { color: #d97706; }
    .red { color: #dc2626; }
    @media print {
        body { margin: 8mm; }
    }
</style>
</head>
<body>

<h1>{{ $title }}</h1>
<div class="subtitle">
    Periode:
    {{ $filters['tahun_akademik_id'] ? \App\Models\TahunAkademik::find($filters['tahun_akademik_id'])?->tahun : 'Semua Tahun' }}
    @if($filters['semester']) — {{ ucfirst($filters['semester']) }} @endif
    @if($filters['mata_kuliah_id']) — {{ \App\Models\MataKuliah::find($filters['mata_kuliah_id'])?->kode }} @endif
    &nbsp;|&nbsp; Dicetak: {{ now()->format('d-m-Y H:i') }}
</div>

@if(empty($rekap))

<table>
    <tr><td style="border: none; text-align: center; padding: 24px;">Tidak ada data assessment untuk filter ini.</td></tr>
</table>

@else

@php
    $mkRowsByMhs = [];
    foreach ($rekap['mata_kuliahs'] as $mks) {
        foreach ($mks['rows'] as $r) {
            $mkRowsByMhs[$r['mahasiswa']->id][$mks['mata_kuliah']->id] = $r;
        }
    }
@endphp

<table>
    <thead>
        <tr>
            <th class="stick">Mahasiswa</th>
            @foreach($rekap['mata_kuliahs'] as $mks)
                <th class="mk-head" colspan="{{ $mks['config']->count() + 1 }}">
                    {{ $mks['mata_kuliah']->kode }} (max {{ $mks['max'] }})
                </th>
            @endforeach
            @foreach($rekap['cpls'] as $cplRow)
                <th class="cpl-head" colspan="2">{{ $cplRow['cpl']->kode_cpl }} (max {{ $cplRow['max'] }})</th>
            @endforeach
        </tr>
        <tr>
            <th class="stick"></th>
            @foreach($rekap['mata_kuliahs'] as $mks)
                @foreach($mks['config'] as $cpmkId => $meta)
                    <th>{{ $meta['cpmk']->kode_cpmk }} ({{ $meta['bobot'] }})</th>
                @endforeach
                <th>Nilai MK</th>
            @endforeach
            @foreach($rekap['cpls'] as $_x)
                <th>Nilai</th>
                <th>Capaian</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rekap['mahasiswas'] as $mhs)
            <tr>
                <td class="stick"><strong>{{ $mhs->nim }}</strong><br>{{ $mhs->nama }}</td>

                @foreach($rekap['mata_kuliahs'] as $mks)
                    @php $row = $mkRowsByMhs[$mhs->id][$mks['mata_kuliah']->id] ?? null; @endphp
                    @foreach($mks['config'] as $cpmkId => $meta)
                        @php $nilai = $row['scores'][$cpmkId]['nilai'] ?? null; @endphp
                        <td class="nilai">{{ $nilai !== null ? number_format((float) $nilai, 1) : '' }}</td>
                    @endforeach
                    <td class="nilai strong">
                        {{ $row ? number_format($row['nilai'], 2) : '' }}
                        @if($row && $row['capaian'] !== null)
                            <br>
                            <span class="{{ $row['capaian'] >= 70 ? 'green' : ($row['capaian'] >= 60 ? 'amber' : 'red') }}">
                                {{ round($row['capaian']) }}%
                            </span>
                        @endif
                    </td>
                @endforeach

                @foreach($rekap['cpls'] as $cplRow)
                    @php $ps = $cplRow['per_student'][$mhs->id] ?? null; @endphp
                    <td class="nilai">{{ ($ps['dinilai'] ?? false) ? number_format($ps['nilai'], 2) : '' }}</td>
                    <td class="nilai">
                        @if(($ps['dinilai'] ?? false) && $ps['capaian'] !== null)
                            <span class="{{ $ps['capaian'] >= 70 ? 'green' : ($ps['capaian'] >= 60 ? 'amber' : 'red') }}">
                                {{ round($ps['capaian']) }}%
                            </span>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="stick strong">Rata-rata Capaian</td>
            @foreach($rekap['mata_kuliahs'] as $mks)
                @foreach($mks['config'] as $cpmkId => $meta)
                    <td></td>
                @endforeach
                @php $mkAvg = collect($mks['rows'])->pluck('capaian')->filter(fn ($v) => $v !== null)->avg(); @endphp
                <td class="nilai strong {{ ($mkAvg ?? 0) >= 70 ? 'green' : (($mkAvg ?? 0) >= 60 ? 'amber' : 'red') }}">
                    {{ $mkAvg !== null ? round($mkAvg) . '%' : '' }}
                </td>
            @endforeach
            @foreach($rekap['cpls'] as $cplRow)
                <td class="nilai {{ ($cplRow['avg_capaian'] ?? 0) >= 70 ? 'green' : (($cplRow['avg_capaian'] ?? 0) >= 60 ? 'amber' : 'red') }}">
                    {{ $cplRow['avg_capaian'] !== null ? round($cplRow['avg_capaian']) . '%' : '' }}
                </td>
                <td></td>
            @endforeach
        </tr>
    </tfoot>
</table>

<div style="margin-top: 24px; font-size: 10px; color: #64748b;">
    Catatan: Nilai MK = Σ nilai CPMK mata kuliah. Nilai CPL = Σ nilai CPMK yang terhubung ke CPL lintas mata kuliah terpilih.
    Capaian (%) = nilai / skor maksimal × 100.
</div>

@endif

</body>
</html>