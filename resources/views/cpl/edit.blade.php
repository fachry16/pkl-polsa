@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit CPL
</h1>

<form action="{{ route('kurikulum.cpl.update', [$kurikulum->id, $cpl->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">
            Kode CPL
        </label>

        <input type="text"
               name="kode_cpl"
               value="{{ old('kode_cpl', $cpl->kode_cpl) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">
            Deskripsi CPL
        </label>

        <textarea name="deskripsi"
                  rows="5"
                  class="form-textarea w-full">{{ old('deskripsi', $cpl->deskripsi) }}</textarea>

    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="btn btn-warning">

            Update

        </button>

        <a href="{{ route('kurikulum.cpl.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
