@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Bahan Kajian
</h1>

<form action="{{ route('kurikulum.bahan-kajian.update', [$kurikulum->id, $bahanKajian->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">
            Kode BK
        </label>

        <input type="text"
               name="kode_bk"
               value="{{ old('kode_bk', $bahanKajian->kode_bk) }}"
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
               value="{{ old('nama_bk', $bahanKajian->nama_bk) }}"
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
                  class="form-textarea w-full">{{ old('referensi', $bahanKajian->referensi) }}</textarea>

    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="btn btn-warning">

            Update

        </button>

        <a href="{{ route('kurikulum.bahan-kajian.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
