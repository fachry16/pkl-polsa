@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Rumusan Nilai Akhir CPL
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('kurikulum.rumusan-nilai-akhir-cpl.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label" for="rncpl-cpl">
            CPL
        </label>

        <x-searchable-select
            id="rncpl-cpl"
            name="cpl_id"
            :options="$cpls->map(fn ($cpl) => ['value' => $cpl->id, 'label' => $cpl->kode_cpl.($cpl->deskripsi ? ' - '.\Illuminate\Support\Str::limit($cpl->deskripsi, 70) : '')])"
            :value="old('cpl_id')"
            placeholder="Ketik kode atau deskripsi CPL" />

        @error('cpl_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="rncpl-mk">
            MK
        </label>

        <x-searchable-select
            id="rncpl-mk"
            name="mata_kuliah_id"
            :options="$mataKuliahs->map(fn ($mk) => ['value' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama])"
            :value="old('mata_kuliah_id')"
            placeholder="Ketik kode atau nama mata kuliah" />

        @error('mata_kuliah_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="rncpl-cpmk">
            CPMK
        </label>

        <x-searchable-select
            id="rncpl-cpmk"
            name="cpmk_id"
            :options="$cpmks->map(fn ($cpmk) => ['value' => $cpmk->id, 'label' => $cpmk->kode_cpmk.($cpmk->deskripsi ? ' - '.\Illuminate\Support\Str::limit($cpmk->deskripsi, 70) : '')])"
            :value="old('cpmk_id')"
            placeholder="Ketik kode atau deskripsi CPMK" />

        @error('cpmk_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Skor Maks
        </label>

        <input type="number"
               name="skor_maks"
               value="{{ old('skor_maks', 0) }}"
               min="0"
               step="0.01"
               class="form-input w-full">

        @error('skor_maks')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Total
        </label>

        <input type="number"
               name="total"
               value="{{ old('total', 0) }}"
               min="0"
               step="0.01"
               class="form-input w-full">

        @error('total')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
