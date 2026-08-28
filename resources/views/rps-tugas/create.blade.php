@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Rancangan Tugas dan Latihan
</h1>

<form action="{{ route('rps.tugas.store', $rps->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">Minggu Ke / Topik</label>

        <input type="text"
               name="minggu_topik"
               class="form-input w-full"
               value="{{ old('minggu_topik') }}"
               placeholder="Contoh: 2-3">

    </div>

    <div class="form-group">

        <label class="form-label">Nama Tugas</label>

        <input type="text"
               name="nama_tugas"
               class="form-input w-full"
               value="{{ old('nama_tugas') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Sub-CPMK</label>

        <input type="text"
               name="sub_cpmk"
               class="form-input w-full"
               value="{{ old('sub_cpmk') }}"
               placeholder="Contoh: Sub-CPMK2">

    </div>

    <div class="form-group">

        <label class="form-label">Penugasan</label>

        <input type="text"
               name="penugasan"
               class="form-input w-full"
               value="{{ old('penugasan') }}"
               placeholder="Contoh: Kelompok (3-4 mahasiswa)">

    </div>

    <div class="form-group">

        <label class="form-label">Ruang Lingkup</label>

        <textarea name="ruang_lingkup"
                  class="form-textarea w-full"
                  rows="4">{{ old('ruang_lingkup') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Cara Pengerjaan</label>

        <textarea name="cara_pengerjaan"
                  class="form-textarea w-full"
                  rows="4">{{ old('cara_pengerjaan') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Batas Waktu</label>

        <input type="text"
               name="batas_waktu"
               class="form-input w-full"
               value="{{ old('batas_waktu') }}"
               placeholder="Contoh: Minggu 4">

    </div>

    <div class="form-group">

        <label class="form-label">Luaran Tugas yang Dihasilkan</label>

        <textarea name="luaran_tugas"
                  class="form-textarea w-full"
                  rows="4">{{ old('luaran_tugas') }}</textarea>

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('rps.tugas.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection