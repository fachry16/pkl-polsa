@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rekap Assessment OBE
</h1>

<p class="page-subtitle" style="color: #64748b;">
    Rekap nilai CPMK, nilai mata kuliah, dan capaian CPL per mahasiswa. Target capaian: <strong>{{ $target }}%</strong>.
</p>

@include('assessment._nav', ['current' => 'rekap'])

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

{{-- Lapis 1: Rekap per mahasiswa --}}
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
                        <th colspan="{{ $mks['config']->count() + 1 }}" style="text-align: center; background: #FFF3C4;">
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
                                    <div style="font-weight: 500; font-size: 0.72rem; color: {{ ($row['capaian'] ?? 0) >= $target ? '#059669' : (($row['capaian'] ?? 0) >= $target - 10 ? '#d97706' : '#dc2626') }};">
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
                            <td style="text-align: right; font-weight: 700; color: {{ ($ps['capaian'] ?? 0) >= $target ? '#059669' : (($ps['capaian'] ?? 0) >= $target - 10 ? '#d97706' : '#dc2626') }};">
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

{{-- Lapis 2: Rekapitulasi CPMK per MK --}}
@if(! empty($cpmkRecaps))
@foreach($cpmkRecaps as $mkRecap)
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">
            Rekap CPMK &mdash; {{ $mkRecap['mata_kuliah']->kode }} {{ $mkRecap['mata_kuliah']->nama }}
        </h2>
        <span style="font-size: 0.8rem; color: #64748b;">Target {{ $target }}%</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: 950px;">
            <thead>
                <tr>
                    <th>CPMK</th>
                    <th>CPL</th>
                    <th style="text-align: right;">Bobot</th>
                    <th style="text-align: right;">Rata-rata</th>
                    <th style="text-align: center;">Tercapai</th>
                    <th style="text-align: right;">% Mahasiswa Tercapai</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Min &mdash; Max</th>
                    <th style="text-align: right;">Std Dev</th>
                    <th>Keterangan Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mkRecap['recaps'] as $c)
                    @php
                        $isOk = $c['status'] === 'Tercapai';
                        $isNull = $c['status'] === 'Belum Dinilai';
                    @endphp
                    <tr>
                        <td>
                            <span style="font-weight: 700; color: #1e293b;">{{ $c['cpmk']->kode_cpmk }}</span>
                        </td>
                        <td style="font-weight: 600;">{{ $c['cpl']->kode_cpl ?? '—' }}</td>
                        <td style="text-align: right;">{{ $c['bobot'] }}</td>
                        <td style="text-align: right; font-weight: 800; color: {{ $isOk ? '#059669' : ($isNull ? '#94a3b8' : '#dc2626') }};">
                            {{ $c['stats']['rata'] !== null ? $c['stats']['rata'] . '%' : '—' }}
                        </td>
                        <td style="text-align: center;">
                            {{ $c['tercapai'] }} <span style="color: #94a3b8;">/ {{ $c['stats']['jumlah'] }}</span>
                        </td>
                        <td style="text-align: right;">{{ $c['persen_tercapai'] }}%</td>
                        <td style="text-align: center;">
                            @if($isNull)
                                <span style="background: #f1f5f9; color: #94a3b8; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Belum Dinilai</span>
                            @elseif($isOk)
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Tercapai</span>
                            @else
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Belum Tercapai</span>
                            @endif
                        </td>
                        <td style="text-align: center; color: #475569; font-size: 0.8rem;">
                            {{ $c['stats']['terendah'] !== null ? $c['stats']['terendah'] . '% — ' . $c['stats']['tertinggi'] . '%' : '—' }}
                        </td>
                        <td style="text-align: right;">{{ $c['stats']['std_dev'] !== null ? $c['stats']['std_dev'] : '—' }}</td>
                        <td style="font-size: 0.8rem; color: #475569; max-width: 260px;">{{ $c['tindak_lanjut'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endif

{{-- Lapis 3: Rekapitulasi CPL lintas MK --}}
@if(! empty($cplRecaps))
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Rekap CPL Lintas Mata Kuliah</h2>
        <span style="font-size: 0.8rem; color: #64748b;">Target {{ $target }}% &mdash; dihitung dari penjumlahan poin CPMK, bukan rata-rata capaian MK</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: 1100px;">
            <thead>
                <tr>
                    <th>CPL</th>
                    <th>Deskripsi</th>
                    <th>MK Pendukung</th>
                    <th style="text-align: right;">Bobot Maks</th>
                    <th style="text-align: right;">Rata-rata</th>
                    <th style="text-align: center;">Tercapai</th>
                    <th style="text-align: right;">% Mahasiswa Tercapai</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Min &mdash; Max</th>
                    <th style="text-align: right;">Std Dev</th>
                    <th>Keterangan Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cplRecaps as $cpl)
                    @php
                        $isOk = $cpl['status'] === 'Tercapai';
                        $isNull = $cpl['status'] === 'Belum Dinilai';
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #1e293b;">{{ $cpl['cpl']->kode_cpl }}</td>
                        <td style="font-size: 0.8rem; color: #475569; max-width: 260px;">{{ $cpl['cpl']->deskripsi ?? '—' }}</td>
                        <td>
                            @foreach($cpl['mk_pendukung'] as $mk)
                                <span style="display: inline-block; background: #FFF3C4; color: #A16207; padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; margin: 2px 4px 2px 0;">
                                    {{ $mk->kode }}
                                </span>
                            @endforeach
                        </td>
                        <td style="text-align: right; font-weight: 600;">{{ $cpl['max'] }}</td>
                        <td style="text-align: right; font-weight: 800; color: {{ $isOk ? '#059669' : ($isNull ? '#94a3b8' : '#dc2626') }};">
                            {{ $cpl['stats']['rata'] !== null ? $cpl['stats']['rata'] . '%' : '—' }}
                        </td>
                        <td style="text-align: center;">
                            {{ $cpl['tercapai'] }} <span style="color: #94a3b8;">/ {{ $cpl['stats']['jumlah'] }}</span>
                        </td>
                        <td style="text-align: right;">{{ $cpl['persen_tercapai'] }}%</td>
                        <td style="text-align: center;">
                            @if($isNull)
                                <span style="background: #f1f5f9; color: #94a3b8; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Belum Dinilai</span>
                            @elseif($isOk)
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Tercapai</span>
                            @else
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Belum Tercapai</span>
                            @endif
                        </td>
                        <td style="text-align: center; color: #475569; font-size: 0.8rem;">
                            {{ $cpl['stats']['terendah'] !== null ? $cpl['stats']['terendah'] . '% — ' . $cpl['stats']['tertinggi'] . '%' : '—' }}
                        </td>
                        <td style="text-align: right;">{{ $cpl['stats']['std_dev'] !== null ? $cpl['stats']['std_dev'] : '—' }}</td>
                        <td style="font-size: 0.8rem; color: #475569; max-width: 280px;">{{ $cpl['tindak_lanjut'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Distribusi Grade --}}
@if(! empty($distribusi))
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Distribusi Nilai Akhir (Grade)</h2>
    </div>
    <div style="padding: 1rem 1.25rem;">
        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
            @foreach(['A', 'AB', 'B', 'BC', 'C', 'D', 'E'] as $huruf)
                @php $d = $distribusi[$huruf]; @endphp
                <div style="flex: 1 1 110px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.9rem 1rem; text-align: center;">
                    <div style="font-size: 1.3rem; font-weight: 800; color: #1e293b;">{{ $huruf }}</div>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $d['jumlah'] }} mahasiswa</div>
                    <div style="font-size: 0.78rem; font-weight: 700; color: #A16207;">{{ $d['persen'] }}%</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@else

<div class="card" style="padding: 1.5rem; margin-bottom: 1.25rem; border-left: 4px solid #D9A500; background: #FFF8E0;">
    <div style="display: flex; gap: 12px; align-items: flex-start;">
        <svg width="20" height="20" fill="none" stroke="#0A0D40" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #A16207; margin: 0 0 0.4rem 0;">
                Panduan Asesmen OBE & Rekap CPMK
            </h3>
            <p style="font-size: 0.85rem; color: #B8860B; margin: 0 0 0.75rem 0; line-height: 1.5;">
                Belum ada data nilai Asesmen OBE untuk filter yang dipilih. Agar nilai mahasiswa muncul pada Rekap Asesmen OBE, silakan pastikan alur berikut:
            </p>
            <ol style="font-size: 0.82rem; color: #B8860B; margin: 0; padding-left: 1.2rem; line-height: 1.6;">
                <li><strong>Kurikulum</strong>: Buka menu <strong>Kurikulum</strong> &rarr; pilih Kurikulum aktif &rarr; klik menu <strong>Rumusan Nilai Akhir MK</strong> untuk mengisi bobot CPMK ke CPL per Mata Kuliah.</li>
                <li><strong>Nilai LMS Kelas</strong>: Dosen menginput nilai tugas / UTS / UAS di LMS Kelas.</li>
                <li><strong>Sinkronisasi</strong>: Dosen mengklik tombol <strong>"Simpan & Kirim ke Asesmen OBE"</strong> pada tab Rekap Nilai di LMS Kelas.</li>
            </ol>
        </div>
    </div>
</div>

<div class="card" style="padding: 2rem; text-align: center; color: #64748b;">
    Belum ada data assessment untuk filter ini. Data diinput oleh dosen melalui halaman Kelas.
    <div style="margin-top: 1rem;">
        <a href="{{ route('assessment.index') }}" class="btn btn-secondary">Kembali ke Monitoring</a>
    </div>
</div>

@endif

@endsection