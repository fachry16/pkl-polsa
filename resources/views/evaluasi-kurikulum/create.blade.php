@extends('layouts.app')

@section('content')

<h1 class="page-header">Tambah Evaluasi Kurikulum</h1>

<p class="page-subtitle" style="color: #64748b;">
    {{ $kurikulum->nama_kurikulum }} &mdash; {{ $kurikulum->programStudi->nama_prodi }}
</p>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('kurikulum.evaluasi-kurikulum.store', $kurikulum->id) }}{{ request()->query('redirect_to') ? '?redirect_to=' . request()->query('redirect_to') : '' }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">
        <label class="form-label">Judul Evaluasi</label>
        <input type="text"
               name="judul"
               class="form-input w-full"
               value="{{ old('judul') }}"
               placeholder="Contoh: Evaluasi Kurikulum Semester Ganjil 2026"
               required>
        @error('judul')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Tahun Akademik</label>
        <select name="tahun_akademik_id" class="form-select w-full">
            <option value="">Pilih Tahun Akademik</option>
            @foreach($tahunAkademiks as $ta)
                <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} — {{ ucfirst($ta->semester) }}
                </option>
            @endforeach
        </select>
        @error('tahun_akademik_id')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Catatan / Temuan</label>
        <textarea name="catatan"
                  class="form-textarea w-full"
                  rows="5"
                  placeholder="Uraian temuan, observasi, atau analisis terhadap kurikulum ini...">{{ old('catatan') }}</textarea>
        @error('catatan')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Rekomendasi</label>
        <textarea name="rekomendasi"
                  class="form-textarea w-full"
                  rows="5"
                  placeholder="Rekomendasi perbaikan berdasarkan temuan evaluasi...">{{ old('rekomendasi') }}</textarea>
        @error('rekomendasi')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="btn-group">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ request()->query('redirect_to') === 'assessment' ? route('assessment.evaluasi', ['kurikulum_id' => $kurikulum->id]) : route('kurikulum.evaluasi-kurikulum.index', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
    </div>

</form>

@endsection
