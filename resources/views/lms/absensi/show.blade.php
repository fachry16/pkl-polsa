@extends('layouts.app')

@section('content')

<div class="page-header">
    Presensi &middot; Pertemuan {{ $sesi->rpsPertemuan->minggu }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $sesi->tanggal_aktual->format('d M Y') }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'presensi']) }}" class="btn btn-secondary btn-sm">Kembali ke Presensi</a>
</div>

@if(! $editable)
    <x-alert type="warning" :message="'Sesi ini terkunci karena sesi pertemuan berikutnya sudah dibuka.'" />
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
