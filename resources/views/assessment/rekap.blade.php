@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rekap Assessment OBE
</h1>

<p class="page-subtitle" style="color: #64748b;">
    Rekap nilai CPMK, nilai mata kuliah, dan capaian CPL per mahasiswa.
</p>

@include('assessment._filters', ['filters' => $filters, 'drop' => $drop])

@if(! empty($rekap))

@php
    $mkRowsByMhs = [];
    foreach ($rekap['mata_kuliahs'] as $mks) {
        foreach ($mks['rows'] as $r) {
            $mkRowsByMhs[$r['mahasiswa']->id][$mks['mata_kuliah']->id] = $r;
        }
    }
    $jumlahCpmk = $rekap['mata_kuliahs']->sum(fn ($mks) => $mks['config']->count());
    $minWidth = 320 + $rekap['mata_kuliahs']->sum(fn ($mks) => $mks['config']->count() * 58 + 130) + count($rekap['cpls']) * 130;
@endphp

<div class="card" style="margin-bottom: 1.25rem; overflow: hidden;">

    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Rekap Nilai</h2>
        <span style="font-size: 0.8rem; color: #64748b;">
            {{ $rekap['mahasiswas']->count() }} mahasiswa · {{ $rekap['mata_kuliahs']->count() }} MK · {{ count($rekap['cpls']) }} CPL
        </span>

        <div style="margin-left: auto; display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('assessment.export', array_merge(request()->except('_token'), ['format' => 'print'])) }}"
               target="_blank" class="btn btn-sm btn-secondary">Print</a>
            <a href="{{ route('assessment.export', array_merge(request()->except('_token'), ['format' => 'pdf'])) }}"
               class="btn btn-sm btn-secondary">PDF</a>
            <a href="{{ route('assessment.export', array_merge(request()->except('_token'), ['format' => 'excel'])) }}"
               class="btn btn-sm btn-secondary">Excel</a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: {{ $minWidth }}px;">
            <thead style="position: sticky; top: 0; z-index: 5; background: #fff;">
                <tr>
                    <th rowspan="2" style="position: sticky; left: 0; background: #fff; z-index: 6; min-width: 220px;">Mahasiswa</th>

                    @foreach($rekap['mata_kuliahs'] as $mks)
                        <th colspan="{{ $mks['config']->count() + 1 }}" style="text-align: center; background: #eef2ff;">
                            {{ $mks['mata_kuliah']->kode }} (max {{ $mks['max'] }})
                        </th>
                    @endforeach

                    @foreach($rekap['cpls'] as $cplRow)
                        <th colspan="2" style="text-align: center; background: #ecfdf5;">
                            {{ $cplRow['cpl']->kode_cpl }} (max {{ $cplRow['max'] }})
                        </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($rekap['mata_kuliahs'] as $mks)
                        @foreach($mks['config'] as $cpmkId => $meta)
                            <th title="{{ $meta['cpmk']->deskripsi ?? '' }}" style="font-size: 0.72rem; text-align: center;">
                                {{ $meta['cpmk']->kode_cpmk }}
                                <span style="color: #94a3b8;">({{ $meta['bobot'] }})</span>
                            </th>
                        @endforeach
                        <th style="text-align: center; font-size: 0.75rem;">Nilai MK</th>
                    @endforeach
                    @foreach($rekap['cpls'] as $cplRow)
                        <th style="text-align: center; font-size: 0.72rem;">Nilai</th>
                        <th style="text-align: center; font-size: 0.72rem;">Capaian (%)</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($rekap['mahasiswas'] as $mhs)
                    <tr>
                        <td style="position: sticky; left: 0; background: #fff; z-index: 4;">
                            <div style="font-weight: 700; color: #1e293b;">{{ $mhs->nim }}</div>
                            <div style="font-size: 0.78rem; color: #64748b;">{{ $mhs->nama }}</div>
                        </td>

                        @foreach($rekap['mata_kuliahs'] as $mks)
                            @php $row = $mkRowsByMhs[$mhs->id][$mks['mata_kuliah']->id] ?? null; @endphp
                            @foreach($mks['config'] as $cpmkId => $meta)
                                @php $nilai = $row['scores'][$cpmkId]['nilai'] ?? null; @endphp
                                <td style="text-align: right;">
                                    {{ $nilai !== null ? number_format((float) $nilai, 1) : '—' }}
                                </td>
                            @endforeach
                            <td style="text-align: right; font-weight: 800; color: #1e293b;">
                                @if($row)
                                    {{ number_format($row['nilai'], 2) }}
                                    <div style="font-weight: 500; font-size: 0.72rem; color: {{ ($row['capaian'] ?? 0) >= 70 ? '#059669' : (($row['capaian'] ?? 0) >= 60 ? '#d97706' : '#dc2626') }};">
                                        {{ $row['capaian'] !== null ? round($row['capaian']) . '%' : '—' }}
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                        @endforeach

                        @foreach($rekap['cpls'] as $cplRow)
                            @php $ps = $cplRow['per_student'][$mhs->id] ?? null; @endphp
                            <td style="text-align: right;">
                                {{ $ps && $ps['dinilai'] ? number_format($ps['nilai'], 2) : '—' }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: {{ ($ps['capaian'] ?? 0) >= 70 ? '#059669' : (($ps['capaian'] ?? 0) >= 60 ? '#d97706' : '#dc2626') }};">
                                {{ $ps && $ps['dinilai'] && $ps['capaian'] !== null ? round($ps['capaian']) . '%' : '—' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>

            @if($rekap['mahasiswas']->isNotEmpty())
            <tfoot style="background: #f8fafc;">
                <tr>
                    <td style="position: sticky; left: 0; background: #f8fafc; font-weight: 700; color: #1e293b;">Rata-rata Capaian</td>
                    @foreach($rekap['mata_kuliahs'] as $mks)
                        @foreach($mks['config'] as $cpmkId => $meta)
                            @php
                                $avg = collect($mks['rows'])->pluck('scores.'.$cpmkId.'.nilai')
                                    ->filter(fn ($v) => $v !== null)
                                    ->avg();
                            @endphp
                            <td></td>
                        @endforeach
                        @php
                            $mkAvg = collect($mks['rows'])->pluck('capaian')->filter(fn ($v) => $v !== null)->avg();
                        @endphp
                        <td style="text-align: right; font-weight: 800; color: #1e293b;">
                            {{ $mkAvg !== null ? round($mkAvg) . '%' : '—' }}
                        </td>
                    @endforeach
                    @foreach($rekap['cpls'] as $cplRow)
                        <td style="text-align: right;">{{ $cplRow['avg_capaian'] !== null ? round($cplRow['avg_capaian'], 1) . '%' : '—' }}</td>
                        <td></td>
                    @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>

@else

<div class="card" style="padding: 2rem; text-align: center; color: #64748b;">
    Belum ada data assessment untuk filter ini. Data diinput oleh dosen melalui halaman Kelas.
    <div style="margin-top: 1rem;">
        <a href="{{ route('assessment.index') }}" class="btn btn-secondary">Kembali ke Monitoring</a>
    </div>
</div>

@endif

@endsection