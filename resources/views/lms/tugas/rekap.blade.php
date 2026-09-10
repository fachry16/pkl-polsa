@extends('layouts.app')

@section('content')

<div class="page-header">
    Rekap Nilai Perkuliahan &amp; LMS
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
        <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Hitung Ulang Nilai</button>
        </form>
    </div>
</div>

<div class="table-container" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
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
                <th style="text-align: center; font-weight: 700; background: #f8fafc;">Nilai Angka</th>
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
                    <td style="text-align: center; font-weight: 700; background: #f8fafc;">
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

@if($cpmkConfig->isNotEmpty() && !Auth::user()->isAdmin())
<div style="margin-top: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">Pemetaan Instrumen → CPMK</h3>
    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">Konfigurasi komponen penilaian yang berkontribusi ke tiap CPMK. Jika belum dikonfigurasi, sync akan menggunakan nilai akhir secara proporsional.</p>

    @foreach($cpmkConfig as $cpmkId => $meta)
        <div style="padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
            <div style="font-size: 0.82rem; font-weight: 600; color: #374151;">{{ $meta['cpmk']->kode_cpmk }} — {{ $meta['cpmk']->deskripsi }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.3rem;">Bobot: {{ $meta['bobot'] }} | CPL: {{ $meta['cpl']->kode_cpl }}</div>
            @php $mapped = $instrumenCpmk->where('cpmk_id', $cpmkId); @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center;">
                @foreach($mapped as $map)
                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.5rem; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 6px; font-size: 0.75rem; color: #4338ca;">
                        {{ ucfirst($map->komponen) }} ({{ $map->bobot_kontribusi }}%)
                        <form action="{{ route('lms.tugas.instrumen-cpmk.hapus', [$pengampu->id, $map->id]) }}" method="POST" style="margin:0; display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; cursor:pointer; color:#dc2626; font-size:0.7rem; padding:0;">x</button>
                        </form>
                    </span>
                @endforeach
                <form action="{{ route('lms.tugas.instrumen-cpmk', $pengampu->id) }}" method="POST" style="display: inline-flex; gap: 0.3rem; align-items: center;">
                    @csrf
                    <input type="hidden" name="cpmk_id" value="{{ $cpmkId }}">
                    <select name="komponen" required style="font-size: 0.75rem; padding: 0.2rem 0.4rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                        <option value="">Komponen</option>
                        @foreach(['tugas','quiz','uts','uas','praktikum','project'] as $k)
                            <option value="{{ $k }}">{{ ucfirst($k) }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="bobot_kontribusi" value="100" min="0" max="100" required style="width: 50px; font-size: 0.75rem; padding: 0.2rem 0.4rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <button type="submit" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; background: #4f46e5; color: #fff; border: none; border-radius: 4px; cursor: pointer;">+</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif

<div style="margin-top: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <a href="{{ route('assessment.index', ['pengampu_id' => $pengampu->id]) }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Asesmen OBE Kelas
    </a>
    @if(!Auth::user()->isAdmin())
    <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
        @csrf
        <button type="submit" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.5rem 1.25rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan &amp; Kirim ke Asesmen OBE
        </button>
    </form>
    @endif
</div>

@endsection
