@extends('layouts.app')

@section('content')

<div class="flex-header">
    <h1 class="page-header" style="margin: 0;">Tambah KRS</h1>
    <a href="{{ route('krs.index') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

<x-alert type="success" :message="session('success')" />

<form method="POST" action="{{ route('krs.store') }}" class="form-card">
    @csrf

    <div class="form-group">
        <label class="form-label">Program Studi</label>
        <select name="program_studi_id" class="form-select" required>
            <option value="">Pilih Program Studi</option>
            @foreach($programStudis as $prodi)
                <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                    {{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}
                </option>
            @endforeach
        </select>
        @error('program_studi_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Tahun Akademik</label>
        <select name="tahun_akademik_id" class="form-select" required>
            <option value="">Pilih Tahun Akademik</option>
            @foreach($tahunAkademiks as $ta)
                <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} {{ $ta->semester }}
                </option>
            @endforeach
        </select>
        @error('tahun_akademik_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Mata Kuliah</label>
        <select name="mata_kuliah_id" class="form-select" required>
            <option value="">Pilih Mata Kuliah</option>
            @foreach($mataKuliahs as $mk)
                <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                    {{ $mk->kode }} - {{ $mk->nama }}
                </option>
            @endforeach
        </select>
        @error('mata_kuliah_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Dosen Pengampu</label>
        <select name="dosen_id" class="form-select" required>
            <option value="">Pilih Dosen</option>
            @foreach($dosens as $d)
                <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                    {{ $d->user->name }}
                </option>
            @endforeach
        </select>
        @error('dosen_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Kelas</label>
        <input type="text" name="kelas" class="form-input" value="{{ old('kelas') }}" placeholder="Contoh: A, B, Reguler Pagi" maxlength="10" required>
        @error('kelas')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('krs.index') }}" class="btn btn-secondary">Batal</a>
    </div>

</form>

<style>
.form-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    max-width: 480px;
    margin-top: 1rem;
}
.form-group {
    margin-bottom: 1rem;
}
.form-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.35rem;
}
.form-input, .form-select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.85rem;
    box-sizing: border-box;
}
.form-error {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.25rem;
}
.form-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.5rem;
}
.flex-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}
</style>

@endsection
