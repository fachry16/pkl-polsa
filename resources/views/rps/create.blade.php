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

    <div class="form-group">

        <label class="form-label">Rumpun MK (RMK)</label>

        <input type="text"
               name="rumpun_mk"
               class="form-input w-full"
               value="{{ old('rumpun_mk') }}">

    </div>

    <div class="form-group">

        <label class="form-label">MK yang Menjadi Prasyarat</label>

        <textarea name="mk_prasyarat"
                  class="form-textarea w-full"
                  rows="3">{{ old('mk_prasyarat') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Menjadi Prasyarat untuk MK</label>

        <textarea name="prasyarat_untuk"
                  class="form-textarea w-full"
                  rows="3">{{ old('prasyarat_untuk') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Integrasi Antar MK</label>

        <textarea name="integrasi_antar_mk"
                  class="form-textarea w-full"
                  rows="3">{{ old('integrasi_antar_mk') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Tautan Kelas Daring</label>

        <input type="url"
               name="tautan_daring"
               class="form-input w-full"
               value="{{ old('tautan_daring') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Daftar Pustaka</label>

        <textarea name="daftar_pustaka"
                  class="form-textarea w-full"
                  rows="5"
                  placeholder="Satu entri per baris.">{{ old('daftar_pustaka') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Dosen Pengembang RPS</label>

        <input type="text"
               name="dosen_pengembang_rps"
               class="form-input w-full"
               value="{{ old('dosen_pengembang_rps', auth()->user()->dosen?->user->name ?? '') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Koordinator RMK</label>

        <input type="text"
               name="koordinator_rmk"
               class="form-input w-full"
               value="{{ old('koordinator_rmk') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Ketua Program Studi</label>

        <input type="text"
               name="ketua_prodi"
               class="form-input w-full"
               value="{{ old('ketua_prodi') }}">

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
