@extends('layouts.lms')

@section('title', 'Pengumuman')

@section('content')
<div class="page-container">
    <div class="page-header">
        Pengumuman - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
    </div>

    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
        <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Kelas</a>
    </div>

    <div style="display: grid; grid-template-columns: 360px 1fr; gap: 1.5rem; align-items: start;">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
            <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0 0 1rem;">Buat Pengumuman</h3>
            <form action="{{ route('lms.pengumuman.store', $pengampu->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Judul <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="judul" class="form-input" required value="{{ old('judul') }}">
                    @error('judul') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Pengumuman <span style="color: #dc2626;">*</span></label>
                    <textarea name="isi" class="form-textarea" style="min-height: 140px;" required>{{ old('isi') }}</textarea>
                    @error('isi') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Kirim Pengumuman</button>
            </form>
        </div>

        <div>
            @forelse($pengumumans as $pengumuman)
                @php
                    $dosenUser = $pengampu->dosen->user ?? null;
                    $canEdit = $pengumuman->created_at->diffInMinutes(now()) <= 30;
                @endphp
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div class="user-avatar-sm flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                            {{ strtoupper(substr($dosenUser->name ?? 'D', 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $dosenUser->name ?? 'Dosen' }}</span>
                                <span style="font-size: 0.7rem; color: #94a3b8;">{{ $dosenUser->email ?? '' }}</span>
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">{{ $pengumuman->published_at?->format('d M Y H:i') }}</span>
                            </div>
                            <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b; margin-top: 0.5rem;">{{ $pengumuman->judul }}</div>
                            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.35rem; line-height: 1.7; white-space: pre-wrap;">{!! linkify($pengumuman->isi) !!}</div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                                @if($canEdit)
                                    <a href="{{ route('lms.pengumuman.edit', [$pengampu->id, $pengumuman->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route('lms.pengumuman.destroy', [$pengampu->id, $pengumuman->id]) }}" method="POST" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pengumuman ini?')">Hapus</button>
                                    </form>
                                @else
                                    <span style="font-size: 0.7rem; color: #94a3b8; font-style: italic;">Batas waktu edit/hapus sudah lewat (30 menit)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                    <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada pengumuman.</p>
                </div>
            @endforelse

            <div style="margin-top: 1rem;">
                {{ $pengumumans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
