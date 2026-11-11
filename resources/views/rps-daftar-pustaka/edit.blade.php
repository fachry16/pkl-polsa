@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Daftar Pustaka
</h1>

<x-alert type="success" :message="session('success')" />

<div class="card">

    <form action="{{ route('rps.daftar-pustaka.update', $rps) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">

            <label class="form-label" for="daftar_pustaka">Daftar Pustaka (5 Tahun Terakhir)</label>

            <textarea id="daftar_pustaka"
                      name="daftar_pustaka"
                      class="form-textarea w-full"
                      rows="10"
                      placeholder="Satu entri per baris.">{{ old('daftar_pustaka', $rps->daftar_pustaka) }}</textarea>

            @error('daftar_pustaka')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <div class="btn-group">

            <button class="btn btn-primary">
                Simpan
            </button>

            <a href="{{ route('mata-kuliah.rps.index', $rps->mata_kuliah_id) }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </div>

    </form>

</div>

@endsection
