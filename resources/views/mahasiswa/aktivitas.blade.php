@extends('layouts.app')

@section('content')
    <h1 class="page-header">Aktivitas Perkuliahan — {{ $mahasiswa->nama }}</h1>

    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
        @include('mahasiswa._detail-nav', ['mahasiswa' => $mahasiswa, 'current' => 'aktivitas'])

        <div style="flex: 1; min-width: 0;">
            @forelse($rows as $row)
                @php
                    $p = $row['pengampu'];
                @endphp
                <div class="card" style="margin-bottom: 1.25rem;">
                    <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">{{ $p->mataKuliah?->nama ?? '-' }} · Kelas {{ $p->nama_kelas }}</div>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.1rem;">{{ $p->label_semester }} · {{ $p->nama_dosen }}</div>
                        </div>
                        @if($row['persen_absensi'] !== null)
                            @php
                                $persen = $row['persen_absensi'];
                                $absBg = $persen >= 75 ? '#ecfdf5' : ($persen >= 50 ? '#fef3c7' : '#fee2e2');
                                $absFg = $persen >= 75 ? '#059669' : ($persen >= 50 ? '#b45309' : '#dc2626');
                                $absBd = $persen >= 75 ? '#a7f3d0' : ($persen >= 50 ? '#fde68a' : '#fecaca');
                            @endphp
                            <span style="display: inline-block; background: {{ $absBg }}; color: {{ $absFg }}; border: 1px solid {{ $absBd }}; border-radius: 999px; padding: 0.18rem 0.7rem; font-size: 0.72rem; font-weight: 700;">
                                Kehadiran {{ number_format($persen, 1) }}%
                            </span>
                        @endif
                    </div>

                    <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Pertemuan</th>
                                    <th style="text-align:center;">Hadir</th>
                                    <th style="text-align:center;">Sakit</th>
                                    <th style="text-align:center;">Izin</th>
                                    <th style="text-align:center;">Alpa</th>
                                    <th>Tugas Dikumpulkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600;">{{ $row['pertemuan'] }} pertemuan</td>
                                    <td style="text-align:center;">
                                        <span style="display:inline-block; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; border-radius:999px; padding:0.12rem 0.55rem; font-size:0.72rem; font-weight:700;">{{ $row['hadir'] }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span style="display:inline-block; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:999px; padding:0.12rem 0.55rem; font-size:0.72rem; font-weight:700;">{{ $row['sakit'] }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span style="display:inline-block; background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:999px; padding:0.12rem 0.55rem; font-size:0.72rem; font-weight:700;">{{ $row['izin'] }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span style="display:inline-block; background:{{ $row['alpa'] > 0 ? '#fee2e2' : '#f8fafc' }}; color:{{ $row['alpa'] > 0 ? '#dc2626' : '#94a3b8' }}; border:1px solid {{ $row['alpa'] > 0 ? '#fecaca' : '#e2e8f0' }}; border-radius:999px; padding:0.12rem 0.55rem; font-size:0.72rem; font-weight:700;">{{ $row['alpa'] }}</span>
                                    </td>
                                    <td style="font-weight: 600;">{{ $row['tugas_dikumpulkan'] }} tugas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card" style="padding: 2.5rem; text-align: center;">
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data aktivitas perkuliahan</div>
                    <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Data presensi dan pengumpulan tugas akan muncul setelah kelas aktif.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection