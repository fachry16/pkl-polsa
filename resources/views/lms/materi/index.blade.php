@extends('layouts.app')

@section('content')

<div class="page-header">
    Materi - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
</div>


<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0 0 1rem;">Tambah Materi</h3>
        <form action="{{ route('lms.materi.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul Materi <span style="color: #dc2626;">*</span></label>
                <input type="text" name="judul" class="form-input" required value="{{ old('judul') }}">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea" style="min-height: 80px;">{{ old('deskripsi') }}</textarea>
            </div>
            @if($pertemuans->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">RPS Pertemuan</label>
                    <select name="rps_pertemuan_id" class="form-input">
                        <option value="">-- Pilih Pertemuan (opsional) --</option>
                        @foreach($pertemuans as $pertemuan)
                            <option value="{{ $pertemuan->id }}" @selected(old('rps_pertemuan_id') == $pertemuan->id)>
                                Minggu {{ $pertemuan->minggu }} - {{ Str::limit($pertemuan->sub_cpmk, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.mp4,.jpg,.png">
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Materi</button>
        </form>
    </div>

    <div>
        @forelse($materis as $materi)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $materi->judul }}</span>
                            <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">{{ $materi->created_at->format('d M Y') }}</span>
                            @if($materi->rps_pertemuan_id)
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Minggu {{ $materi->rpsPertemuan->minggu ?? '?' }}</span>
                            @endif
                        </div>
                        @if($materi->deskripsi)
                            <p style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem; line-height: 1.6;">{!! linkify($materi->deskripsi) !!}</p>
                        @endif
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
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
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada materi. Silakan upload materi pertama.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $materis->links() }}
        </div>
    </div>
</div>

@endsection
