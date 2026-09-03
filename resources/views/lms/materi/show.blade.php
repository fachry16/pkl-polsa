@extends('layouts.app')

@section('content')

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Tombol Kembali --}}
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">
            Kembali ke Tugas Kelas
        </a>
    </div>

    {{-- Card Detail Materi --}}
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1.25rem; align-items: flex-start;">
            <div style="width: 3rem; height: 3rem; border-radius: 50%; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $materi->judul }}</h1>
                    @if($materi->canBeModified())
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('lms.materi.edit', [$pengampu->id, $materi->id]) }}" class="btn btn-secondary btn-sm">Edit Materi</a>
                        </div>
                    @endif
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span>{{ $pengampu->dosen?->user?->name ?? 'Dosen' }}</span>
                    &middot;
                    <span>{{ $materi->created_at->format('d M Y, H:i') }}</span>
                    @if($materi->rpsPertemuan)
                        &middot;
                        <span style="background: #f1f5f9; color: #475569; padding: 0.1rem 0.5rem; border-radius: 4px; font-weight: 600;">Pertemuan {{ $materi->rpsPertemuan->minggu_ke }}</span>
                    @endif
                </div>

                {{-- Deskripsi --}}
                @if($materi->deskripsi)
                    <div style="font-size: 0.9rem; color: #334155; margin-top: 1.5rem; line-height: 1.7; border-top: 1px solid #f1f5f9; padding-top: 1rem; white-space: pre-wrap;">{!! linkify($materi->deskripsi) !!}</div>
                @endif

                {{-- File Lampiran --}}
                @if($materi->file_path)
                    <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                        <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Lampiran Materi:</div>
                        <x-file-link :file="$materi->file_path" :href="route('lms.file', ['materi', $materi->id])" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Komentar Kelas (Class Comments) --}}
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Komentar Kelas ({{ $materi->komentars->where('is_private', false)->count() }})
        </h3>

        {{-- Form Tambah Komentar --}}
        <form action="{{ route('lms.topik.komentar.store', $pengampu->id) }}" method="POST" style="margin-bottom: 1.5rem;">
            @csrf
            <input type="hidden" name="tipe_topik" value="materi">
            <input type="hidden" name="topik_id" value="{{ $materi->id }}">
            <input type="hidden" name="is_private" value="0">
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" name="pesan" class="form-input" placeholder="Tambahkan komentar kelas..." required style="flex: 1; font-size: 0.85rem;">
                <button type="submit" class="btn btn-primary btn-sm">Kirim</button>
            </div>
        </form>

        {{-- List Komentar --}}
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($materi->komentars->where('is_private', false)->sortBy('created_at') as $komentar)
                <div style="display: flex; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #f8fafc;">
                    <div style="width: 2rem; height: 2rem; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                        {{ substr($komentar->user->name ?? '?', 0, 2) }}
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $komentar->user->name ?? '-' }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $komentar->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: 0.85rem; color: #334155; margin-top: 0.2rem;">{{ $komentar->pesan }}</div>
                        <div style="margin-top: 0.25rem;">
                            <form action="{{ route('lms.topik.komentar.destroy', [$pengampu->id, $komentar->id]) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?');" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-xs" style="color: #ef4444; padding: 0; font-size: 0.7rem; text-decoration: none;">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; text-align: center; padding: 1rem 0;">Belum ada komentar kelas.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection