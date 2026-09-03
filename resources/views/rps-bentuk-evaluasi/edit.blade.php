@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Bentuk Evaluasi
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('rps.bentuk-evaluasi.update', [$rps->id, $bentukEvaluasi->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">Bentuk Evaluasi</label>

        <input type="text"
               name="bentuk_evaluasi"
               class="form-input w-full"
               value="{{ old('bentuk_evaluasi', $bentukEvaluasi->bentuk_evaluasi) }}"
               placeholder="Contoh: Kuis, Tugas Individu, Tes Tulis (UTS), Proyek Akhir">

        @error('bentuk_evaluasi')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">Sub-CPMK</label>

        <input type="text"
               name="sub_cpmk"
               class="form-input w-full"
               value="{{ old('sub_cpmk', $bentukEvaluasi->sub_cpmk) }}"
               placeholder="Contoh: Sub-CPMK1-5">

    </div>

    <div class="form-group">

        <label class="form-label">Formatif / Sumatif</label>

        <div class="flex gap-6 mt-2">

            <label class="inline-flex items-center">
                <input type="checkbox"
                       name="formatif"
                       value="1"
                       {{ old('formatif', $bentukEvaluasi->formatif) ? 'checked' : '' }}
                       class="mr-2">
                Formatif
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox"
                       name="sumatif"
                       value="1"
                       {{ old('sumatif', $bentukEvaluasi->sumatif) ? 'checked' : '' }}
                       class="mr-2">
                Sumatif
            </label>

        </div>

    </div>

    <div class="form-group">

        <label class="form-label">Instrumen Penilaian</label>

        <textarea name="instrumen"
                  class="form-textarea w-full"
                  rows="3"
                  placeholder="Contoh: Umpan balik tugas, Rubrik penilaian tugas kelompok">{{ old('instrumen', $bentukEvaluasi->instrumen) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Frekuensi</label>

        <input type="text"
               name="frekuensi"
               class="form-input w-full"
               value="{{ old('frekuensi', $bentukEvaluasi->frekuensi) }}"
               placeholder="Contoh: 3 kali; tiap pertemuan; 1 kali">

    </div>

    <div class="form-group">

        <label class="form-label">Tagihan (Bukti)</label>

        <textarea name="tagihan"
                  class="form-textarea w-full"
                  rows="3"
                  placeholder="Contoh: Lembar jawaban UTS, dokumen PMP, screenshot Jira/Trello">{{ old('tagihan', $bentukEvaluasi->tagihan) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Bobot (%)</label>

        <input type="number"
               name="bobot"
               value="{{ old('bobot', $bentukEvaluasi->bobot) }}"
               min="0"
               max="100"
               step="0.01"
               class="form-input w-full">

        @error('bobot')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Perbarui

        </button>

        <a href="{{ route('rps.bentuk-evaluasi.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection