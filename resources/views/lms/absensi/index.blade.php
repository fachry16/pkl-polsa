@extends('layouts.app')

@section('content')

<div class="page-header">
    Presensi Kehadiran
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Kelas</a>
</div>

@forelse($pertemuans as $pertemuan)
    @php
        $sesi = $sesis->get($pertemuan->id);
        $counts = $sesi ? $sesi->absensis->groupBy('status')->map->count() : collect();
    @endphp
    <div class="card" style="margin-bottom: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <div style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">
                Pertemuan {{ $pertemuan->minggu }}
            </div>
            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem;">
                {{ $pertemuan->materi }}
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem;">
                @if($sesi)
                    Dilaksanakan {{ $sesi->tanggal_aktual->format('d M Y') }} &middot;
                    Hadir {{ $counts->get('hadir', 0) }} / Sakit {{ $counts->get('sakit', 0) }} /
                    Izin {{ $counts->get('izin', 0) }} / Alpa {{ $counts->get('alpa', 0) }}
                @else
                    Belum dibuka
                @endif
            </div>
        </div>
        <div>
            @if($sesi)
                <a href="{{ route('lms.absensi.show', [$pengampu->id, $sesi->id]) }}" class="btn btn-secondary btn-sm">Isi &amp; Ubah</a>
            @else
                <form action="{{ route('lms.absensi.buka', $pengampu->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="rps_pertemuan_id" value="{{ $pertemuan->id }}">
                    <button type="submit" class="btn btn-primary btn-sm">Buka Sesi</button>
                </form>
            @endif
        </div>
    </div>
@empty
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
        <p style="color: #94a3b8; font-size: 0.9rem;">RPS belum memiliki daftar pertemuan. Tambahkan pertemuan di menu RPS terlebih dahulu.</p>
    </div>
@endforelse

@endsection
