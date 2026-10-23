@extends('layouts.app')

@section('content')

@php
    $programStudiOptions = $programStudis->map(fn ($prodi) => [
        'value' => $prodi->id,
        'label' => $prodi->kode_prodi.' - '.$prodi->nama_prodi,
    ]);

    $tahunAkademikOptions = $tahunAkademiks->map(fn ($ta) => [
        'value' => $ta->id,
        'label' => $ta->tahun.' '.$ta->semester,
    ]);

    $mataKuliahOptions = $mataKuliahs->map(fn ($mk) => [
        'value' => $mk->id,
        'label' => $mk->kode.' - '.$mk->nama,
    ]);

    $dosenOptions = $dosens->map(fn ($d) => [
        'value' => $d->id,
        'label' => $d->user->name,
        'search' => $d->nidn,
    ]);

    $activeTahunAkademikId = $tahunAkademiks->firstWhere('is_active', true)?->id;
@endphp

<div class="flex-header">
    <h1 class="page-header" style="margin: 0;">Tambah KRS</h1>
    <a href="{{ route('krs.index') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

<x-alert type="success" :message="session('success')" />

<form method="POST" action="{{ route('krs.store') }}" class="form-card">
    @csrf

    <div class="form-group">
        <label class="form-label" for="program-studi-search">Program Studi</label>
        <x-searchable-select
            id="program-studi-search"
            name="program_studi_id"
            :options="$programStudiOptions"
            :value="old('program_studi_id')"
            placeholder="Ketik kode atau nama program studi"
            required />
        @error('program_studi_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="tahun-akademik-search">Tahun Akademik</label>
        <x-searchable-select
            id="tahun-akademik-search"
            name="tahun_akademik_id"
            :options="$tahunAkademikOptions"
            :value="old('tahun_akademik_id', $activeTahunAkademikId)"
            placeholder="Ketik tahun akademik"
            required />
        @error('tahun_akademik_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="mata-kuliah-search">Mata Kuliah</label>
        <x-searchable-select
            id="mata-kuliah-search"
            name="mata_kuliah_id"
            :options="$mataKuliahOptions"
            :value="old('mata_kuliah_id')"
            placeholder="Ketik kode atau nama mata kuliah"
            required />
        @error('mata_kuliah_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="dosen-search">Dosen Pengampu</label>
        <x-searchable-select
            id="dosen-search"
            name="dosen_id"
            :options="$dosenOptions"
            :value="old('dosen_id')"
            placeholder="Ketik nama atau NIDN dosen"
            required />
        @error('dosen_id')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Kelas</label>
        <p class="form-text">Kelas dibuat otomatis sesuai program studi dan semester mata kuliah (contoh: TI 3, lalu dipecah menjadi TI 3A / TI 3B saat mahasiswa dilampirkan).</p>
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
.form-text {
    font-size: 0.8rem;
    color: #6b7280;
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
