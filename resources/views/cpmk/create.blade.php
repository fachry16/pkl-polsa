@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah CPMK
</h1>

<form action="{{ route('kurikulum.cpmk.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">
            Kode CPMK
        </label>

        <input type="text"
               name="kode_cpmk"
               value="{{ old('kode_cpmk') }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">
            Deskripsi CPMK
        </label>

        <textarea name="deskripsi"
                  rows="5"
                  class="form-textarea w-full">{{ old('deskripsi') }}</textarea>

    </div>

    <div class="flex gap-2">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('kurikulum.cpmk.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
