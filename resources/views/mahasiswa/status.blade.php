@extends('layouts.app')

@php
    $semesterAktif = $mahasiswa->semesterMahasiswas->sortByDesc('id')->first();
    $statusOptions = [
        'Aktif' => ['#059669', '#ecfdf5', '#a7f3d0'],
        'Cuti' => ['#b45309', '#fef3c7', '#fde68a'],
        'Lulus' => ['#B8860B', '#FFF3C4', '#FFE88F'],
        'DO' => ['#dc2626', '#fee2e2', '#fecaca'],
        'Non Aktif' => ['#64748b', '#f1f5f9', '#e2e8f0'],
    ];
    $statusMhs = $mahasiswa->status ?? 'Aktif';
    [$stFg, $stBg, $stBd] = $statusOptions[$statusMhs] ?? $statusOptions['Non Aktif'];
@endphp

@section('content')
    <h1 class="page-header">Status — {{ $mahasiswa->nama }}</h1>

    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
        @include('mahasiswa._detail-nav', ['mahasiswa' => $mahasiswa, 'current' => 'status'])

        <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 1.25rem;">

            <div class="card">
                <div style="padding: 1.1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.6rem;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Informasi Mahasiswa</span>
                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; background: {{ $stBg }}; color: {{ $stFg }}; border: 1px solid {{ $stBd }}; border-radius: 999px; padding: 0.28rem 1rem; font-size: 0.85rem; font-weight: 700;">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: {{ $stFg }}; display: inline-block; flex-shrink: 0;"></span>
                        {{ $statusMhs }}
                    </span>
                </div>
                <div style="padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">NIM</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->nim }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Program Studi</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->programStudi?->nama_prodi ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Angkatan</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->angkatan }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Semester</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $semesterAktif?->semester ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Tahun Akademik</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">
                            {{ $semesterAktif?->tahunAkademik?->tahun ?? '-' }} {{ $semesterAktif?->tahunAkademik?->semester ?? '' }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Program</div>
                        <div style="margin-top: 0.25rem;">
                            @if(($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan')
                                <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Karyawan</span>
                            @else
                                <span style="background: #FFF8E0; color: #B8860B; border: 1px solid #FFE88F; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Reguler</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div style="padding: 1.1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Chart Status</div>
                <div style="padding: 1.5rem; display: flex; gap: 2.5rem; align-items: center; flex-wrap: wrap;">
                    <div style="position: relative; width: 140px; height: 140px; flex-shrink: 0;">
                        <svg width="140" height="140" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f1f5f9" stroke-width="3.5" />
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="{{ $stFg }}" stroke-width="3.5" stroke-linecap="round" pathLength="100" />
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.15rem; text-align: center; padding: 0 1.5rem;">
                            <span style="font-size: 0.85rem; font-weight: 800; color: {{ $stFg }};">{{ $statusMhs }}</span>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 240px;">
                        <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; margin-bottom: 0.6rem;">Perbandingan Status Mahasiswa</div>
                        <div style="display: flex; gap: 0.35rem; align-items: stretch;">
                            @foreach($statusOptions as $label => $colors)
                                @php($active = $label === $statusMhs)
                                <div style="flex: 1; text-align: center;">
                                    <div style="height: 12px; border-radius: 999px; background: {{ $active ? $colors[0] : '#e2e8f0' }}; {{ $active ? 'box-shadow: 0 1px 0 rgba(0,0,0,0.08);' : '' }}"></div>
                                    <div style="font-size: 0.66rem; margin-top: 0.35rem; {{ $active ? 'color: '.$colors[0].'; font-weight: 800;' : 'color: #94a3b8;' }}">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div style="margin-top: 0.9rem; font-size: 0.76rem; color: #64748b;">
                            Status mahasiswa ini adalah
                            <strong style="color: {{ $stFg }};">{{ $statusMhs }}</strong>.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection