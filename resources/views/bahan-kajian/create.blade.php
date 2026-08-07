@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Bahan Kajian
</h1>

<form action="{{ route('kurikulum.bahan-kajian.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">
            Kode BK
        </label>

        <input type="text"
               name="kode_bk"
               value="{{ old('kode_bk') }}"
               placeholder="Contoh: BK-01"
               class="form-input w-full">

        @error('kode_bk')
            <small class="form-error">
                {{ $message }}
            </small>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Nama Bahan Kajian
        </label>

        <input type="text"
               name="nama_bk"
               value="{{ old('nama_bk') }}"
               class="form-input w-full">

        @error('nama_bk')
            <small class="form-error">
                {{ $message }}
            </small>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Referensi
        </label>

        <textarea name="referensi"
                  rows="4"
                  class="form-textarea w-full">{{ old('referensi') }}</textarea>

    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('kurikulum.bahan-kajian.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
