@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah RPS
</h1>

<form action="{{ route('mata-kuliah.rps.store', $mataKuliah) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">Kode RPS</label>

        <input type="text"
               name="kode_rps"
               class="form-input w-full"
               value="{{ old('kode_rps') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Semester</label>

        <input type="number"
               name="semester"
               class="form-input w-full"
               value="{{ old('semester') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Dosen Pengampu</label>

        <input type="text"
               name="dosen_pengampu"
               class="form-input w-full"
               value="{{ old('dosen_pengampu', auth()->user()->dosen?->user->name ?? '') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Deskripsi Mata Kuliah</label>

        <textarea name="deskripsi_mata_kuliah"
                  class="form-textarea w-full"
                  rows="4">{{ old('deskripsi_mata_kuliah') }}</textarea>

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('mata-kuliah.rps.index', $mataKuliah) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
