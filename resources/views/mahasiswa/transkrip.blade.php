@extends('layouts.app')

@section('content')
    <h1 class="page-header">Transkrip Akademik — {{ $mahasiswa->nama }}</h1>

    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
        @include('mahasiswa._detail-nav', ['mahasiswa' => $mahasiswa, 'current' => 'transkrip'])

        <div style="flex: 1; min-width: 0;">
            <div class="card" style="margin-bottom: 1.25rem; padding: 1.25rem; display: flex; gap: 2.5rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div style="display: flex; gap: 2.5rem; flex-wrap: wrap; align-items: center;">
                    <div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">IPK Kumulatif</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">
                            {{ $ipk !== null ? number_format($ipk, 2) : '-' }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total SKS</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">{{ $totalSks }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Jumlah Semester</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">{{ count($perSemester) }}</div>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Cetak
                    </button>
                    <a href="{{ route('mahasiswa.transkrip.export', $mahasiswa->id) }}" class="btn btn-danger btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            @forelse($perSemester as $semester => $mks)
                @php
                    $rowsSem = $mks['rows'];
                    $totalSksSem = $mks['total_sks'];
                    $ips = $mks['ips'];
                @endphp
                <div class="card" style="margin-bottom: 1.25rem;">
                    <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">{{ $semester }}</div>
                        <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.75rem;">
                            <span style="color: #64748b; font-weight: 600;">SKS: <span style="color: #1e293b;">{{ $totalSksSem }}</span></span>
                            <span style="color: #64748b; font-weight: 600;">IPS: <span style="color: #1e293b; font-weight: 800;">{{ $ips !== null ? number_format($ips, 2) : '-' }}</span></span>
                        </div>
                    </div>

                    <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Mata Kuliah</th>
                                    <th style="text-align:center;">SKS</th>
                                    <th style="text-align:right;">Nilai</th>
                                    <th style="text-align:center;">Huruf</th>
                                    <th style="text-align:right;">Bobot Mutu</th>
                                    <th style="text-align:right;">SKS × Mutu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rowsSem as $index => $mk)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td style="color: #64748b;">{{ $mk['mata_kuliah']->kode ?? '-' }}</td>
                                        <td style="font-weight: 600; color: #1e293b;">{{ $mk['mata_kuliah']->nama ?? '-' }}</td>
                                        <td style="text-align:center;">{{ $mk['sks'] }}</td>
                                        <td style="text-align:right;">{{ number_format($mk['nilai'], 2) }}</td>
                                        <td style="text-align:center;">
                                            @php
                                                $colorMatch = match($mk['huruf'] ?? '') {
                                                    'A', 'B+' => ['bg' => '#ecfdf5', 'fg' => '#059669', 'bd' => '#a7f3d0'],
                                                    'B', 'C+' => ['bg' => '#eff6ff', 'fg' => '#1d4ed8', 'bd' => '#bfdbfe'],
                                                    default    => ['bg' => '#fef3c7', 'fg' => '#b45309', 'bd' => '#fde68a'],
                                                };
                                            @endphp
                                            <span style="display:inline-block; background:{{ $colorMatch['bg'] }}; color:{{ $colorMatch['fg'] }}; border:1px solid {{ $colorMatch['bd'] }}; border-radius:999px; padding:0.15rem 0.6rem; font-size:0.72rem; font-weight:700;">
                                                {{ $mk['huruf'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['bobot_mutu'], 2) : '-' }}</td>
                                        <td style="text-align:right; font-weight: 600;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['sks'] * $mk['bobot_mutu'], 2) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card" style="padding: 2.5rem; text-align: center;">
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data transkrip</div>
                    <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Transkrip akan terisi setelah penilaian akhir proses pembelajaran dilakukan.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection