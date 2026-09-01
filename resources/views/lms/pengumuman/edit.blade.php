@extends('layouts.app')

@section('content')

<div class="page-header">
    Edit Pengumuman - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.pengumuman.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Pengumuman</a>
</div>


<div style="max-width: 720px;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <form action="{{ route('lms.pengumuman.update', [$pengampu->id, $pengumuman->id]) }}" method="POST">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Judul <span style="color: #dc2626;">*</span></label>
                <input type="text" name="judul" class="form-input" required value="{{ old('judul', $pengumuman->judul) }}">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Isi Pengumuman <span style="color: #dc2626;">*</span></label>
                <textarea name="isi" class="form-textarea" style="min-height: 140px;" required>{{ old('isi', $pengumuman->isi) }}</textarea>
                @error('isi') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection
