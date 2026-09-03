@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Rumusan Nilai Akhir MK
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('kurikulum.rumusan-nilai-akhir-mk.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">
            MK
        </label>

        <select name="mata_kuliah_id"
                class="form-select">

            <option value="">
                -- Pilih Mata Kuliah --
            </option>

            @foreach($mataKuliahs as $mk)

            <option value="{{ $mk->id }}"
                {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>

                {{ $mk->kode }}
                -
                {{ $mk->nama }}

            </option>

            @endforeach

        </select>

        @error('mata_kuliah_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            CPL
        </label>

        <select name="cpl_id"
                class="form-select">

            <option value="">
                -- Pilih CPL --
            </option>

            @foreach($cpls as $cpl)

            <option value="{{ $cpl->id }}"
                {{ old('cpl_id') == $cpl->id ? 'selected' : '' }}>

                {{ $cpl->kode_cpl }}
                @if($cpl->deskripsi)
                    — {{ \Illuminate\Support\Str::limit($cpl->deskripsi, 60) }}
                @endif

            </option>

            @endforeach

        </select>

        @error('cpl_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            CPMK
        </label>

        <select name="cpmk_id"
                class="form-select">

            <option value="">
                -- Pilih CPMK --
            </option>

            @foreach($cpmks as $cpmk)

            <option value="{{ $cpmk->id }}"
                {{ old('cpmk_id') == $cpmk->id ? 'selected' : '' }}>

                {{ $cpmk->kode_cpmk }}
                @if($cpmk->deskripsi)
                    — {{ \Illuminate\Support\Str::limit($cpmk->deskripsi, 60) }}
                @endif

            </option>

            @endforeach

        </select>

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

        <a href="{{ route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
