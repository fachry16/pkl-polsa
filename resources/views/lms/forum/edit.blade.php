@extends('layouts.app')

@section('content')

<div class="page-header">
    Edit Pesan Forum - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.forum.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Forum</a>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.85rem; margin-bottom: 1rem;">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; max-width: 720px;">
    <form action="{{ route('lms.forum.update', [$pengampu->id, $diskusi->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        @if($diskusi->parent_id)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.9rem; font-size: 0.8rem; color: #64748b; margin-bottom: 1rem;">
                Balasan dari diskusi induk
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">Pesan <span style="color: #dc2626;">*</span></label>
            <textarea name="pesan" class="form-textarea" style="min-height: 140px;" required>{{ old('pesan', $diskusi->pesan) }}</textarea>
            @error('pesan') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Lampiran File</label>
            @if($diskusi->file_path)
                <div style="font-size: 0.8rem; color: #475569; margin-bottom: 0.5rem;">
                    File saat ini: <a href="{{ route('lms.file', ['forum', $diskusi->id]) }}" target="_blank" style="color: #4f46e5;">{{ basename($diskusi->file_path) }}</a>
                </div>
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: #dc2626; margin-bottom: 0.5rem;">
                    <input type="checkbox" name="remove_file" value="1">
                    Hapus file saat ini
                </label>
            @endif
            <input type="file" name="file" class="form-input">
            @error('file') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('lms.forum.index', $pengampu->id) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
