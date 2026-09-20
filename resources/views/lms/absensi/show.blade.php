@extends('layouts.app')

@section('content')

<div class="page-header">
    Presensi &middot; Pertemuan {{ $sesi->rpsPertemuan->minggu }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $sesi->tanggal_aktual->format('d M Y') }}
    </span>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'presensi']) }}" class="btn btn-secondary btn-sm">Kembali ke Presensi</a>
    @if($editable && !$mahasiswas->isEmpty())
        <form action="{{ route('lms.absensi.hadir-semua', [$pengampu->id, $sesi->id]) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-success btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                Hadirkan Semua Mahasiswa
            </button>
        </form>
    @endif
</div>

@if(Auth::user()->isAdmin())
    <x-alert type="info" :message="'Mode pratinjau (read-only): Admin tidak dapat mengubah data presensi.'" />
@endif

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
        {{ $sesi->rpsPertemuan->materi }}
    </div>

    @if($mahasiswas->isEmpty())
        <div style="padding: 2rem; text-align: center; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</div>
    @else
        <form action="{{ route('lms.absensi.store', [$pengampu->id, $sesi->id]) }}" method="POST">
            @csrf
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th style="text-align: center;">Hadir</th>
                            <th style="text-align: center;">Sakit</th>
                            <th style="text-align: center;">Izin</th>
                            <th style="text-align: center;">Alpa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0]; @endphp
                        @foreach($mahasiswas as $mahasiswa)
                            @php
                                $status = $absensis->get($mahasiswa->id)?->status ?? 'hadir';
                                $total[$status]++;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mahasiswa->nim }}</td>
                                <td>{{ $mahasiswa->nama }}</td>
                                @foreach(['hadir', 'sakit', 'izin', 'alpa'] as $opsi)
                                    <td style="text-align: center;">
                                        <input type="radio" name="status[{{ $mahasiswa->id }}]" value="{{ $opsi }}"
                                               {{ $status === $opsi ? 'checked' : '' }}
                                               {{ $editable ? '' : 'disabled' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: 600;">Jumlah</td>
                            <td style="text-align: center; font-weight: 600;">{{ $total['hadir'] }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $total['sakit'] }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $total['izin'] }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $total['alpa'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($editable)
                <div style="padding: 1rem 1.25rem; text-align: right;">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Presensi</button>
                </div>
            @endif
        </form>
    @endif
</div>

@endsection
