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
        <button type="submit" class="btn btn-primary btn-sm">Hitung Ulang Nilai</button>
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
                <th style="text-align: center; font-weight: 700;">Nilai Tugas</th>
                @foreach($bobot as $komponen => $persen)
                    @if($komponen !== 'tugas' && $persen > 0)
                        <th style="text-align: center; font-size: 0.7rem;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small></th>
                    @endif
                @endforeach
                <th style="text-align: center; font-weight: 700;">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mahasiswa)
                @php
                    $nilaiTugas = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'tugas')?->nilai;
                    $nilaiAkhir = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    @foreach($tugasList as $tugas)
                        @php
                            $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                            $nilaiSub = $submission?->nilai;
                        @endphp
                        <td style="text-align: center;">
                            @if($nilaiSub !== null)
                                <span style="font-weight: 600; color: {{ $nilaiSub >= 60 ? '#059669' : '#dc2626' }};">{{ $nilaiSub }}</span>
                            @elseif($submission)
                                <span style="color: #d97706; font-size: 0.75rem;">Blm Dinilai</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: 700;">
                        {{ $nilaiTugas !== null ? number_format($nilaiTugas, 2) : '-' }}
                    </td>
                    @foreach($bobot as $komponen => $persen)
                        @if($komponen !== 'tugas' && $persen > 0)
                            @php
                                $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                            @endphp
                            <td style="text-align: center;">
                                {{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2) : '-' }}
                            </td>
                        @endif
                    @endforeach
                    <td style="text-align: center; font-weight: 700;">
                        {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + $tugasList->count() + collect($bobot)->except('tugas')->filter()->count() }}" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($mahasiswas->isNotEmpty())
<div class="card" style="margin-top: 1.5rem;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
        Input Nilai Komponen (Quiz / UTS / UAS / Praktikum / Project)
    </div>
    <form action="{{ route('lms.tugas.komponen', $pengampu->id) }}" method="POST">
        @csrf
        <div class="table-container" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($bobot as $komponen => $persen)
                            @if($komponen !== 'tugas' && $persen > 0)
                                <th style="text-align: center;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small></th>
                            @endif
                        @endforeach
                        <th style="text-align: center; font-weight: 700;">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mahasiswa)
                        @php
                            $nilaiAkhirForm = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $mahasiswa->nim }}</td>
                            <td>{{ $mahasiswa->nama }}</td>
                            @foreach($bobot as $komponen => $persen)
                                @if($komponen !== 'tugas' && $persen > 0)
                                    @php
                                        $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                                    @endphp
                                    <td style="text-align: center;">
                                        <input type="number" name="nilai[{{ $mahasiswa->id }}][{{ $komponen }}]"
                                               value="{{ $nilaiKomponen !== null ? $nilaiKomponen : '' }}"
                                               min="0" max="100" step="0.01"
                                               style="width: 80px; text-align: center;">
                                    </td>
                                @endif
                            @endforeach
                            <td style="text-align: center; font-weight: 700;">
                                {{ $nilaiAkhirForm !== null ? number_format($nilaiAkhirForm, 2) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 1rem 1.25rem; text-align: right;">
            <button type="submit" class="btn btn-primary btn-sm">Simpan Nilai Komponen</button>
        </div>
    </form>
</div>
@endif

@endsection
