@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah CPL
</h1>

<form action="{{ route('kurikulum.cpl.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">
            Kode CPL
        </label>

        <input type="text"
               name="kode_cpl"
               value="{{ old('kode_cpl') }}"
               placeholder="Contoh: CPL-01"
               class="form-input w-full">

        @error('kode_cpl')
            <small class="form-error">
                {{ $message }}
            </small>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Deskripsi CPL
        </label>

        <textarea name="deskripsi"
                  rows="5"
                  class="form-textarea w-full">{{ old('deskripsi') }}</textarea>

        @error('deskripsi')
            <small class="form-error">
                {{ $message }}
            </small>
        @enderror

    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('kurikulum.cpl.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
