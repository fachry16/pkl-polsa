@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Pertemuan RPS
</h1>

<form action="{{ route('rps.pertemuan.store', $rps->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">Minggu</label>

        <input type="number"
               name="minggu"
               class="form-input w-full"
               value="{{ old('minggu') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Sub CPMK</label>

        <textarea name="sub_cpmk"
                  class="form-textarea w-full"
                  rows="4">{{ old('sub_cpmk') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Materi Pembelajaran</label>

        <textarea name="materi"
                  class="form-textarea w-full"
                  rows="4">{{ old('materi') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran</label>

        <input type="text"
               name="metode"
               class="form-input w-full"
               value="{{ old('metode') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Pengalaman Belajar</label>

        <textarea name="pengalaman_belajar"
                  class="form-textarea w-full"
                  rows="4">{{ old('pengalaman_belajar') }}</textarea>

    </div>
    <div class="form-group">

        <label class="form-label">Indikator</label>

        <textarea name="indikator"
                  class="form-textarea w-full"
                  rows="4">{{ old('indikator') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">CPMK Induk (Sub-CPMK)</label>

        <input type="text"
               name="cpmk_induk"
               class="form-input w-full"
               value="{{ old('cpmk_induk') }}"
               placeholder="Contoh: CPMK07.08">

    </div>

    <div class="form-group">

        <label class="form-label">Penilaian - Teknik &amp; Kriteria</label>

        <textarea name="teknik_kriteria"
                  class="form-textarea w-full"
                  rows="3">{{ old('teknik_kriteria') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran Daring (Online)</label>

        <textarea name="metode_daring"
                  class="form-textarea w-full"
                  rows="3">{{ old('metode_daring') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran Luring (Offline)</label>

        <textarea name="metode_luring"
                  class="form-textarea w-full"
                  rows="3">{{ old('metode_luring') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Bobot (%)</label>

        <input type="number"
               name="bobot"
               class="form-input w-full"
               value="{{ old('bobot') }}">

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('rps.pertemuan.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
