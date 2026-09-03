@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Role &amp; Jabatan
</h1>

<div class="card">
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Nama Role / Jabatan <span style="color: #dc2626;">*</span></label>
            <input type="text"
                   name="nama"
                   value="{{ old('nama') }}"
                   class="form-input"
                   placeholder="Contoh: Dosen Pembina"
                   required>
            @error('nama')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Kode (Slug) <span style="color: #dc2626;">*</span></label>
            <input type="text"
                   name="kode"
                   value="{{ old('kode') }}"
                   class="form-input"
                   placeholder="Contoh: dosen_pembina"
                   required>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; display: block;">
                Kode unik untuk sistem (huruf kecil, tanpa spasi, gunakan underscore).
            </span>
            @error('kode')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                      rows="3"
                      class="form-input"
                      placeholder="Keterangan tugas atau fungsi role/jabatan...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </form>
</div>

@endsection