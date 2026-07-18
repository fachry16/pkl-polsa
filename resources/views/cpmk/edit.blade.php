@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit CPMK
</h1>

<form action="{{ route('kurikulum.cpmk.update', [$kurikulum->id, $cpmk->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">
            Kode CPMK
        </label>

        <input type="text"
               name="kode_cpmk"
               value="{{ old('kode_cpmk', $cpmk->kode_cpmk) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">
            Deskripsi CPMK
        </label>

        <textarea name="deskripsi"
                  rows="5"
                  class="form-textarea w-full">{{ old('deskripsi', $cpmk->deskripsi) }}</textarea>

    </div>

    <div class="flex gap-2">

        <button class="btn btn-warning">

            Update

        </button>

        <a href="{{ route('kurikulum.cpmk.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
