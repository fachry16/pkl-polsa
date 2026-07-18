@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Pertemuan RPS
</h1>

<form action="{{ route('rps.pertemuan.update', [$rps->id, $pertemuan->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">Minggu</label>

        <input type="number"
               name="minggu"
               class="form-input w-full"
               value="{{ old('minggu', $pertemuan->minggu) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Sub CPMK</label>

        <textarea name="sub_cpmk"
                  class="form-textarea w-full"
                  rows="4">{{ old('sub_cpmk', $pertemuan->sub_cpmk) }}</textarea>

    </div>


    <div class="form-group">

        <label class="form-label">Materi Pembelajaran</label>

        <textarea name="materi"
                  class="form-textarea w-full"
                  rows="4">{{ old('materi', $pertemuan->materi) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran</label>

        <input type="text"
               name="metode"
               class="form-input w-full"
               value="{{ old('metode', $pertemuan->metode) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Pengalaman Belajar</label>

        <textarea name="pengalaman_belajar"
                  class="form-textarea w-full"
                  rows="4">{{ old('pengalaman_belajar', $pertemuan->pengalaman_belajar) }}</textarea>

    </div>
    <div class="form-group">

        <label class="form-label">Indikator</label>

        <textarea name="indikator"
                  class="form-textarea w-full"
                  rows="4">{{ old('indikator', $pertemuan->indikator) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Bobot (%)</label>

        <input type="number"
               name="bobot"
               class="form-input w-full"
               value="{{ old('bobot', $pertemuan->bobot) }}">

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Perbarui

        </button>

        <a href="{{ route('rps.pertemuan.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
