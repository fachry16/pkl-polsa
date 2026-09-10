@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Evaluasi Kurikulum
</h1>

<p class="page-subtitle" style="color: #64748b;">
    Analisis capaian CPL, kontribusi mata kuliah, dan identifikasi CPMK yang perlu perbaikan.
</p>

@include('assessment._nav', ['current' => 'evaluasi'])

@if(session('success'))
<div class="alert alert-success mb-3">
    {{ session('success') }}
</div>
@endif

<x-alert type="error" :message="session('error')" />

@include('assessment._filters', ['filters' => $filters, 'drop' => $drop])

@if(! empty($evaluasi))

@php
    $threshold = 70;
@endphp

{{-- KPI Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 1.25rem;">

    <div style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75;">Rata-rata Capaian Kurikulum</div>
        <div style="font-size: 1.6rem; font-weight: 800; line-height: 1.1;">
            {{ $evaluasi['rata_rata_kurikulum'] !== null ? round($evaluasi['rata_rata_kurikulum']) . '%' : '—' }}
        </div>
        <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.2rem;">Capaian rata-rata seluruh CPL</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #10b981; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #10b981;">CPL Tercapai</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">
            {{ $evaluasi['cpl_tercapai'] }} <span style="font-size: 0.8rem; font-weight: 500; color: #64748b;">/ {{ $evaluasi['total_cpl'] }}</span>
        </div>
        <div style="font-size: 0.75rem; color: #64748b;">Capaian &ge; {{ $threshold }}%</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #ef4444; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #ef4444;">CPL Perlu Perbaikan</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ $evaluasi['cpl_belum'] }}</div>
        <div style="font-size: 0.75rem; color: #64748b;">Capaian &lt; {{ $threshold }}%</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #4f46e5; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #4f46e5;">MK Terassess</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ $evaluasi['mk_terassess'] }}</div>
        <div style="font-size: 0.75rem; color: #64748b;">Mata kuliah dengan data</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #f59e0b; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #f59e0b;">CPMK Perlu Perbaikan</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ count($evaluasi['cpmk_lemah']) }}</div>
        <div style="font-size: 0.75rem; color: #64748b;">Capaian &lt; {{ $threshold }}%</div>
    </div>

</div>

{{-- Capaian CPL --}}
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Capaian CPL per Kurikulum</h2>
        <span style="margin-left: auto; font-size: 0.8rem; color: #64748b;">Threshold: {{ $threshold }}%</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: 600px;">
            <thead>
                <tr>
                    <th style="width: 120px;">Kode CPL</th>
                    <th>Deskripsi</th>
                    <th style="text-align: right;">Max Skor</th>
                    <th style="text-align: right;">Rata-rata Skor</th>
                    <th style="text-align: right; width: 100px;">Capaian</th>
                    <th style="width: 180px;">Progress</th>
                    <th style="width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluasi['cpl_summary'] as $cplId => $row)
                    @php
                        $capaian = $row['avg_capaian'];
                        $isTercapai = $capaian !== null && $capaian >= $threshold;
                    @endphp
                    <tr>
                        <td>
                            <span style="font-weight: 700; color: #1e293b;">{{ $row['cpl']->kode_cpl }}</span>
                        </td>
                        <td style="font-size: 0.82rem; color: #475569;">{{ $row['cpl']->deskripsi ?? '—' }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ $row['max'] }}</td>
                        <td style="text-align: right;">
                            @php
                                $perStudent = collect($row['per_student'])->pluck('nilai')->filter(fn ($v) => $v !== null);
                                $avg = $perStudent->isEmpty() ? null : $perStudent->avg();
                            @endphp
                            {{ $avg !== null ? number_format($avg, 2) : '—' }}
                        </td>
                        <td style="text-align: right; font-weight: 800; color: {{ $isTercapai ? '#059669' : '#dc2626' }};">
                            {{ $capaian !== null ? round($capaian) . '%' : '—' }}
                        </td>
                        <td>
                            <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                <div style="height: 100%; width: {{ min(100, $capaian ?? 0) }}%; background: {{ $isTercapai ? '#10b981' : '#ef4444' }}; border-radius: 999px;"></div>
                            </div>
                        </td>
                        <td>
                            @if($capaian === null)
                                <span style="background: #f1f5f9; color: #94a3b8; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Belum Dinilai</span>
                            @elseif($isTercapai)
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Tercapai</span>
                            @else
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">Perlu Perbaikan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada data capaian CPL.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Kontribusi MK per CPL --}}
@if(! empty($evaluasi['mk_cpl_contribution']))
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Kontribusi Mata Kuliah ke CPL</h2>
        <p style="font-size: 0.8rem; color: #64748b; margin: 0.3rem 0 0 0;">Rata-rata capaian per mata kuliah terhadap CPL yang dihubungkan melalui CPMK.</p>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: 700px;">
            <thead>
                <tr>
                    <th style="min-width: 180px;">Mata Kuliah</th>
                    <th style="text-align: right;">Max MK</th>
                    <th style="text-align: right;">Capaian MK</th>
                    @php
                        $allCpls = [];
                        foreach ($evaluasi['mk_cpl_contribution'] as $item) {
                            foreach ($item['per_cpl'] as $cplId => $data) {
                                $allCpls[$cplId] = $data['cpl_kode'];
                            }
                        }
                    @endphp
                    @foreach($allCpls as $cplId => $kode)
                        <th style="text-align: center; font-size: 0.72rem; background: #ecfdf5;" title="{{ $kode }}">{{ $kode }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi['mk_cpl_contribution'] as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;">{{ $item['mata_kuliah']->kode }}</div>
                            <div style="font-size: 0.78rem; color: #64748b;">{{ $item['mata_kuliah']->nama }}</div>
                        </td>
                        <td style="text-align: right; font-weight: 600;">{{ $item['max_mk'] }}</td>
                        <td style="text-align: right; font-weight: 800; color: {{ ($item['avg_capaian'] ?? 0) >= $threshold ? '#059669' : '#dc2626' }};">
                            {{ $item['avg_capaian'] !== null ? round($item['avg_capaian']) . '%' : '—' }}
                        </td>
                        @foreach($allCpls as $cplId => $kode)
                            @php $pc = $item['per_cpl'][$cplId] ?? null; @endphp
                            <td style="text-align: center;">
                                @if($pc && $pc['capaian'] !== null)
                                    <span style="font-weight: 700; color: {{ $pc['capaian'] >= $threshold ? '#059669' : '#dc2626' }}; font-size: 0.82rem;">
                                        {{ round($pc['capaian']) }}%
                                    </span>
                                    <div style="font-size: 0.65rem; color: #94a3b8;">{{ $pc['cpmk_kode'] }} ({{ $pc['bobot'] }})</div>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- CPMK Lemah --}}
@if(! empty($evaluasi['cpmk_lemah']))
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <svg width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">CPMK Perlu Perbaikan</h2>
        <span style="margin-left: auto; font-size: 0.8rem; color: #64748b;">Capaian &lt; {{ $threshold }}% — diurutkan dari terendah</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table" style="min-width: 800px;">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th>CPMK</th>
                    <th>CPL</th>
                    <th style="text-align: right;">Bobot</th>
                    <th style="text-align: right;">Rata-rata Nilai</th>
                    <th style="text-align: right;">Capaian</th>
                    <th style="width: 150px;">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi['cpmk_lemah'] as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;">{{ $item['mata_kuliah']->kode }}</div>
                            <div style="font-size: 0.78rem; color: #64748b;">{{ $item['mata_kuliah']->nama }}</div>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: #1e293b;">{{ $item['cpmk']->kode_cpmk }}</span>
                            <div style="font-size: 0.75rem; color: #64748b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['cpmk']->deskripsi ?? '' }}">{{ $item['cpmk']->deskripsi ?? '—' }}</div>
                        </td>
                        <td style="font-weight: 600;">{{ $item['cpl']->kode_cpl }}</td>
                        <td style="text-align: right;">{{ $item['bobot'] }}</td>
                        <td style="text-align: right;">{{ number_format($item['avg_nilai'], 2) }}</td>
                        <td style="text-align: right; font-weight: 800; color: #dc2626;">{{ round($item['capaian']) }}%</td>
                        <td>
                            <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                <div style="height: 100%; width: {{ min(100, $item['capaian']) }}%; background: #ef4444; border-radius: 999px;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Rekomendasi --}}
@php
    $cplLemah = collect($evaluasi['cpl_summary'])
        ->filter(fn ($r) => ($r['avg_capaian'] ?? 0) < $threshold || $r['avg_capaian'] === null)
        ->sortBy('avg_capaian')
        ->values();
@endphp

@if($cplLemah->isNotEmpty())
<div class="card" style="overflow: hidden; margin-bottom: 1.25rem; border-left: 4px solid #f59e0b;">
    <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Rekomendasi Perbaikan</h2>
    </div>
    <div style="padding: 1rem 1.25rem;">
        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.85rem; color: #475569; line-height: 1.8;">
            @foreach($cplLemah as $cplRow)
                @php
                    $linkedCpmks = collect($evaluasi['cpmk_lemah'])
                        ->filter(fn ($cp) => $cp['cpl']->id === $cplRow['cpl']->id);
                @endphp
                <li>
                    <strong style="color: #1e293b;">{{ $cplRow['cpl']->kode_cpl }}</strong>
                    — Capaian {{ $cplRow['avg_capaian'] !== null ? round($cplRow['avg_capaian']) . '%' : 'belum dinilai' }}.
                    @if($linkedCpmks->isNotEmpty())
                        Perlu perbaikan pada CPMK:
                        {{ $linkedCpmks->pluck('cpmk.kode_cpmk')->implode(', ') }}.
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@else

<div class="card" style="padding: 1.5rem; margin-bottom: 1.25rem; border-left: 4px solid #3b82f6; background: #eff6ff;">
    <div style="display: flex; gap: 12px; align-items: flex-start;">
        <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e40af; margin: 0 0 0.4rem 0;">
                Evaluasi Kurikulum
            </h3>
            <p style="font-size: 0.85rem; color: #1d4ed8; margin: 0 0 0.75rem 0; line-height: 1.5;">
                Belum ada data assessment untuk kurikulum ini. Evaluasi kurikulum membutuhkan data nilai CPMK dari minimal satu assessment yang sudah diisi.
            </p>
            <ol style="font-size: 0.82rem; color: #1e3a8a; margin: 0; padding-left: 1.2rem; line-height: 1.6;">
                <li>Pilih program studi dan kurikulum pada filter di atas.</li>
                <li>Pastikan sudah ada assessment dengan data nilai CPMK.</li>
                <li>Data akan otomatis ditampilkan setelah dipilih.</li>
            </ol>
        </div>
    </div>
</div>

@endif

@endsection
