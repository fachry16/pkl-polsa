@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Monitoring Assessment OBE
</h1>

<p class="page-subtitle" style="color: #64748b;">
    Pemantauan capaian CPMK, nilai mata kuliah, dan capaian CPL mahasiswa. Input nilai dilakukan oleh dosen melalui halaman Kelas.
</p>

@include('assessment._nav', ['current' => 'monitoring'])

@if(session('success'))
<div class="alert alert-success mb-3">
    {{ session('success') }}
</div>
@endif

<x-alert type="error" :message="session('error')" />

@include('assessment._filters', ['filters' => $filters, 'drop' => $drop])

{{-- KPI Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 1.25rem;">

    <div style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75;">Rata-rata Capaian CPL</div>
        <div style="font-size: 1.6rem; font-weight: 800; line-height: 1.1;">
            {{ $kpis['avgCpl'] !== null ? round($kpis['avgCpl']) . '%' : '—' }}
        </div>
        <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.2rem;">Capaian pembelajaran lulusan</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #4f46e5; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #4f46e5;">Mahasiswa Dinilai</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ number_format($kpis['mahasiswaCount']) }}</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #0ea5e9; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #0ea5e9;">Mata Kuliah Dinilai</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ number_format($kpis['mkCount']) }}</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #10b981; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #10b981;">Sel CPMK Dinilai</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ number_format($kpis['cpmkScored']) }}</div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-top: 3px solid #f59e0b; border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <div style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; color: #f59e0b;">Total CPL</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ number_format($kpis['cpls']) }}</div>
    </div>

</div>

@if($assessments->isEmpty())
    <div class="card" style="padding: 2rem; text-align: center; color: #64748b;">
        Belum ada data assessment untuk filter ini. Dosen mengisi nilai dari halaman Kelas masing-masing.
    </div>
@else

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 12px; margin-bottom: 1.25rem;">

        {{-- Capaian per CPL --}}
        <div class="card" style="overflow: hidden;">
            <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b;">
                Capaian CPL per Mata Kuliah Terpilih
            </div>
            <div style="padding: 1rem 1.25rem;">
                @forelse($kpis['cplCapaian'] as $cplId => $row)
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px;">
                            <span style="font-weight: 700; color: #1e293b;">{{ $row['cpl']->kode_cpl }}</span>
                            <span style="color: #475569;">{{ $row['avg'] !== null ? round($row['avg']) . '%' : '—' }}</span>
                        </div>
                        <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min(100, $row['avg'] ?? 0) }}%; background: {{ ($row['avg'] ?? 0) >= 70 ? '#10b981' : (($row['avg'] ?? 0) >= 60 ? '#f59e0b' : '#ef4444') }}; border-radius: 999px;"></div>
                        </div>
                    </div>
                @empty
                    <div style="color: #94a3b8; font-size: 0.85rem;">Belum ada capaian CPL.</div>
                @endforelse
            </div>
        </div>

        {{-- Distribusi capaian MK --}}
        <div class="card" style="overflow: hidden;">
            <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b;">
                Distribusi Capaian Mahasiswa per Mata Kuliah
            </div>
            <div style="padding: 1rem 1.25rem;">
                @foreach($kpis['distribusi'] as $label => $jumlah)
                    @php
                        $total = array_sum($kpis['distribusi']);
                        $persen = $total > 0 ? ($jumlah / $total) * 100 : 0;
                    @endphp
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px;">
                            <span style="font-weight: 600; color: #1e293b;">{{ $label }} (%)</span>
                            <span style="color: #475569;">{{ $jumlah }} mahasiswa</span>
                        </div>
                        <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $persen }}%; background: #4f46e5; border-radius: 999px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CPL Tertinggi & Terendah --}}
        <div class="card" style="overflow: hidden;">
            <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b;">
                Capaian CPL
            </div>
            <div style="padding: 1rem 1.25rem;">
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Tertinggi</div>
                    @if($kpis['tertinggi'])
                        <div style="font-weight: 800; color: #059669; font-size: 1.1rem;">{{ $kpis['tertinggi']['cpl']->kode_cpl }}</div>
                        <div style="font-size: 0.8rem; color: #475569;">{{ round($kpis['tertinggi']['avg']) }}%</div>
                    @else
                        <div style="color: #94a3b8; font-size: 0.85rem;">—</div>
                    @endif
                </div>
                <div>
                    <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Terendah</div>
                    @if($kpis['terendah'])
                        <div style="font-weight: 800; color: #dc2626; font-size: 1.1rem;">{{ $kpis['terendah']['cpl']->kode_cpl }}</div>
                        <div style="font-size: 0.8rem; color: #475569;">{{ round($kpis['terendah']['avg']) }}%</div>
                    @else
                        <div style="color: #94a3b8; font-size: 0.85rem;">—</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Daftar Assessment --}}
    <div class="card" style="overflow: hidden;">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Daftar Assessment</h2>
            <span style="margin-left: auto; font-size: 0.8rem; color: #64748b;">{{ $assessments->count() }} kelas</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table" style="min-width: 1000px;">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen</th>
                        <th>TA / Semester</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $assessment)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;">{{ $assessment->pengampu->mataKuliah->kode }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $assessment->pengampu->mataKuliah->nama }}</div>
                            </td>
                            <td>{{ $assessment->pengampu->kelas }}</td>
                            <td>{{ optional($assessment->pengampu->dosen->user)->name ?? $assessment->pengampu->dosen->nama }}</td>
                            <td>
                                <div>{{ $assessment->pengampu->tahunAkademik->tahun ?? '' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ ucfirst($assessment->pengampu->semester_akademik ?? '') }}</div>
                            </td>
                            <td>
                                @php
                                    $warna = $assessment->status === 'final' ? '#059669' : ($assessment->status === 'dinilai' ? '#2563eb' : '#94a3b8');
                                    $bg = $assessment->status === 'final' ? '#d1fae5' : ($assessment->status === 'dinilai' ? '#dbeafe' : '#f1f5f9');
                                @endphp
                                <span style="background: {{ $bg }}; color: {{ $warna }}; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700;">
                                    {{ $assessment->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('assessment.rekap', array_filter(['mata_kuliah_id' => $assessment->pengampu->mata_kuliah_id] + request()->query())) }}" class="btn btn-sm btn-secondary">Rekap</a>
                                <a href="{{ route('assessment.export', array_filter(['mata_kuliah_id' => $assessment->pengampu->mata_kuliah_id, 'format' => 'print'] + request()->query())) }}" class="btn btn-sm btn-secondary">Export</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif

@endsection