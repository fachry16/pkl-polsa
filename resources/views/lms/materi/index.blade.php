@extends('layouts.app')

@section('content')

<div class="page-header">
    Materi - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
</div>

<div style="display: grid; grid-template-columns: 360px 1fr; gap: 1.5rem; align-items: start;">

    {{-- Card Pengarah RPS Materi --}}
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #1e293b;">Manajemen Materi LMS</h3>
            <span style="font-size: 0.7rem; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;">Berbasis RPS</span>
        </div>

        <p style="font-size: 0.82rem; color: #475569; line-height: 1.6; margin-bottom: 1.25rem;">
            Penyusunan modul, sub-CPMK, dan materi pembelajaran dilakukan secara terpusat melalui dokumen <strong>RPS Pertemuan Mata Kuliah</strong>.
        </p>

        @if($pengampu->mataKuliah?->rps)
            <a href="{{ route('mata-kuliah.rps.show', [$pengampu->mata_kuliah_id, $pengampu->mataKuliah->rps->id]) }}" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Kelola Pertemuan RPS
            </a>
        @endif

        <a href="{{ route('mata-kuliah.rps.index', $pengampu->mata_kuliah_id) }}" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Buka RPS Mata Kuliah
        </a>

        <div style="font-size: 0.75rem; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px; margin-top: 1.25rem; line-height: 1.5;">
            💡 <strong>Panduan:</strong> Materi pembelajaran terhubung langsung dengan minggu pertemuan RPS mata kuliah.
        </div>
    </div>

    {{-- Daftar Materi LMS yang Sudah Diunggah --}}
    <div>
        @forelse($materis as $materi)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $materi->judul }}</span>
                            <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">{{ $materi->created_at->format('d M Y, H:i') }}</span>
                            @if($materi->rps_pertemuan_id)
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Minggu {{ $materi->rpsPertemuan->minggu ?? '?' }}</span>
                            @endif
                        </div>
                        @if($materi->deskripsi)
                            <p style="font-size: 0.85rem; color: #475569; margin-top: 0.35rem; line-height: 1.6; white-space: pre-wrap;">{!! linkify($materi->deskripsi) !!}</p>
                        @endif
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; align-items: center;">
                            @if($materi->file_path)
                                <x-file-link :file="$materi->file_path" :href="route('lms.file', ['materi', $materi->id])" />
                            @endif
                            <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
                                @if($materi->canBeModified())
                                    <a href="{{ route('lms.materi.edit', [$pengampu->id, $materi->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route('lms.materi.destroy', ['pengampu' => $pengampu->id, 'materi' => $materi->id]) }}" method="POST" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus materi ini?')">Hapus</button>
                                    </form>
                                @else
                                    <span style="font-size: 0.72rem; color: #94a3b8;">Terkunci (lewat 1x24 jam)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada materi yang diunggah ke kelas ini.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $materis->links() }}
        </div>
    </div>
</div>

@endsection
