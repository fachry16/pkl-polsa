@extends('layouts.app')

@section('content')

<div class="page-header">
    Rekap Nilai Tugas
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
    <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Sinkronkan rata-rata nilai ke komponen penilaian RPS?')">Sinkronkan Nilai ke PIKOBE</button>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                @foreach($tugasList as $tugas)
                    <th style="text-align: center; font-size: 0.7rem;">{{ Str::limit($tugas->judul, 15) }}</th>
                @endforeach
                <th style="text-align: center; font-weight: 700;">Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mahasiswa)
                @php
                    $totalNilai = 0;
                    $tugasDinilai = 0;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    @foreach($tugasList as $tugas)
                        @php
                            $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                            $nilai = $submission?->nilai;
                            if ($nilai !== null) {
                                $totalNilai += $nilai;
                                $tugasDinilai++;
                            }
                        @endphp
                        <td style="text-align: center;">
                            @if($nilai !== null)
                                <span style="font-weight: 600; color: {{ $nilai >= 60 ? '#059669' : '#dc2626' }};">{{ $nilai }}</span>
                            @elseif($submission)
                                <span style="color: #d97706; font-size: 0.75rem;">Blm Dinilai</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: 700;">
                        {{ $tugasDinilai > 0 ? number_format($totalNilai / $tugasDinilai, 2) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 3 + $tugasList->count() }}" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
