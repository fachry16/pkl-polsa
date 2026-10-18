@extends('layouts.app')

@section('content')
    <h1 class="page-header">KHS — {{ $mahasiswa->nama }}</h1>

    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
        @include('mahasiswa._detail-nav', ['mahasiswa' => $mahasiswa, 'current' => 'nilai'])

        <div style="flex: 1; min-width: 0;">
            <div class="card" style="margin-bottom: 1.25rem; padding: 0.85rem 1.25rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem;">
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak
                </button>
                <a href="{{ route('mahasiswa.khs.export', $mahasiswa->id) }}" class="btn btn-danger btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export PDF
                </a>
            </div>

            @forelse($semesters as $semester => $rows)
                <div class="card" style="margin-bottom: 1.25rem;">
                    <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                        {{ $semester }}
                    </div>

                    <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Kelas</th>
                                    <th>Nilai Komponen</th>
                                    <th>Nilai Akhir</th>
                                    <th>Huruf</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: #1e293b;">{{ $row['pengampu']->mataKuliah?->nama ?? '-' }}</div>
                                            <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem;">{{ $row['pengampu']->mataKuliah?->kode ?? '-' }}</div>
                                        </td>
                                        <td>{{ $row['pengampu']->nama_dosen }}</td>
                                        <td>{{ $row['pengampu']->nama_kelas }}</td>
                                        <td>
                                            @forelse($row['komponen'] as $komponen => $nilai)
                                                <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; border-radius: 999px; padding: 0.12rem 0.55rem; font-size: 0.68rem; font-weight: 600; margin: 0.1rem 0.15rem 0.1rem 0;">
                                                    {{ ucfirst($komponen) }}: {{ number_format($nilai, 2) }}
                                                </span>
                                            @empty
                                                <span style="font-size: 0.75rem; color: #94a3b8;">Belum dinilai</span>
                                            @endforelse
                                        </td>
                                        <td style="font-weight: 600;">
                                            {{ $row['akhir'] !== null ? number_format($row['akhir'], 2) : '-' }}
                                        </td>
                                        <td>
                                            @if($row['huruf'])
                                                @php
                                                    $colorMatch = match($row['huruf']) {
                                                        'A', 'B+' => ['bg' => '#ecfdf5', 'fg' => '#059669', 'bd' => '#a7f3d0'],
                                                        'B', 'C+' => ['bg' => '#FFF8E0', 'fg' => '#B8860B', 'bd' => '#FFE88F'],
                                                        default    => ['bg' => '#fef3c7', 'fg' => '#b45309', 'bd' => '#fde68a'],
                                                    };
                                                @endphp
                                                <span style="display: inline-block; background: {{ $colorMatch['bg'] }}; color: {{ $colorMatch['fg'] }}; border: 1px solid {{ $colorMatch['bd'] }}; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.72rem; font-weight: 700;">
                                                    {{ $row['huruf'] }}
                                                </span>
                                            @else
                                                <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card" style="padding: 2.5rem; text-align: center;">
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data nilai</div>
                    <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Nilai akan muncul setelah proses penilaian dilakukan.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection