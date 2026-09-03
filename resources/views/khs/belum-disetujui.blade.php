@extends('layouts.app')

@section('content')

<div style="max-width: 560px; margin: 3rem auto; text-align: center;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="width: 64px; height: 64px; border-radius: 16px; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <h2 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">
            KHS Belum Tersedia
        </h2>

        <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
            Kartu Hasil Studi (KHS)
            @if($tahunAkademik)
                semester <strong>{{ $tahunAkademik->tahun }} {{ ucfirst($tahunAkademik->semester) }}</strong>
            @endif
            Anda belum disetujui oleh Kaprodi. KHS akan ditampilkan setelah mendapat persetujuan.
        </p>

        @if($approval && $approval->status === 'ditolak')
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; text-align: left;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #dc2626; margin-bottom: 0.3rem;">Catatan Kaprodi:</div>
                <div style="font-size: 0.85rem; color: #7f1d1d;">{{ $approval->catatan ?? '-' }}</div>
            </div>
        @endif

        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            ? Kembali ke Dashboard
        </a>
    </div>
</div>

@endsection
