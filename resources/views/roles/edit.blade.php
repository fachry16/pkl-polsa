@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Role &amp; Jabatan
</h1>

<div class="card">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nama Role / Jabatan <span style="color: #dc2626;">*</span></label>
            <input type="text"
                   name="nama"
                   value="{{ old('nama', $role->nama) }}"
                   class="form-input"
                   required>
            @error('nama')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Kode (Slug) <span style="color: #dc2626;">*</span></label>
            <input type="text"
                   name="kode"
                   value="{{ old('kode', $role->kode) }}"
                   class="form-input"
                   required>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; display: block;">
                Kode unik untuk sistem.
            </span>
            @error('kode')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                      rows="3"
                      class="form-input">{{ old('deskripsi', $role->deskripsi) }}</textarea>
            @error('deskripsi')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-warning">
                Update
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </form>
</div>

@endsection