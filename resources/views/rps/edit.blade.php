@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit RPS
</h1>

<form action="{{ route('mata-kuliah.rps.update', [$mataKuliah, $rps]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">Kode Mata Kuliah</label>

        <input type="text"
               class="form-input w-full"
               value="{{ $mataKuliah->kode }}"
               readonly>

    </div>

    <div class="form-group">

        <label class="form-label">Nama Mata Kuliah</label>

        <input type="text"
               class="form-input w-full"
               value="{{ $mataKuliah->nama }}"
               readonly>

    </div>

    <div class="form-group">

        <label class="form-label">Kode RPS</label>

        <input type="text"
               name="kode_rps"
               class="form-input w-full"
               value="{{ old('kode_rps', $rps->kode_rps) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Semester</label>

        <input type="number"
               name="semester"
               class="form-input w-full"
               value="{{ old('semester', $rps->semester) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Dosen Pengampu</label>

        <select name="dosen_pengampu_id" class="form-select w-full" required>
            <option value="">Pilih dosen pengampu</option>
            @foreach ($options['pengampus'] as $dosen)
                <option value="{{ $dosen->id }}" @selected(old('dosen_pengampu_id', $rps->dosen_pengampu_id) == $dosen->id)>{{ $dosen->user->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Deskripsi Mata Kuliah</label>

        <textarea name="deskripsi_mata_kuliah"
                  rows="4"
                  class="form-textarea w-full">{{ old('deskripsi_mata_kuliah', $rps->deskripsi_mata_kuliah) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Rumpun MK (RMK)</label>

        <select name="rumpun_mk_id" class="form-select w-full">
            <option value="">Belum ditentukan</option>
            @foreach ($options['rumpunMks'] as $rumpun)
                <option value="{{ $rumpun->id }}" @selected(old('rumpun_mk_id', $rps->rumpun_mk_id) == $rumpun->id)>{{ $rumpun->nama }}</option>
            @endforeach
        </select>

    </div>

    <div class="form-group">

        <label class="form-label">MK yang Menjadi Prasyarat</label>

        <textarea name="mk_prasyarat"
                  class="form-textarea w-full"
                  rows="3">{{ old('mk_prasyarat', $rps->mk_prasyarat) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Menjadi Prasyarat untuk MK</label>

        <textarea name="prasyarat_untuk"
                  class="form-textarea w-full"
                  rows="3">{{ old('prasyarat_untuk', $rps->prasyarat_untuk) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Integrasi Antar MK</label>

        <textarea name="integrasi_antar_mk"
                  class="form-textarea w-full"
                  rows="3">{{ old('integrasi_antar_mk', $rps->integrasi_antar_mk) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Tautan Kelas Daring</label>

        <input type="url"
               name="tautan_daring"
               class="form-input w-full"
               value="{{ old('tautan_daring', $rps->tautan_daring) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Daftar Pustaka</label>

        <textarea name="daftar_pustaka"
                  class="form-textarea w-full"
                  rows="5"
                  placeholder="Satu entri per baris.">{{ old('daftar_pustaka', $rps->daftar_pustaka) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Dosen Pengembang RPS</label>

        <select name="dosen_pengembang_rps_id" class="form-select w-full">
            <option value="">Belum ditentukan</option>
            @foreach ($options['dosens'] as $dosen)
                <option value="{{ $dosen->id }}" @selected(old('dosen_pengembang_rps_id', $rps->dosen_pengembang_rps_id) == $dosen->id)>{{ $dosen->user->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Koordinator RMK</label>

        <select name="koordinator_rmk_id" class="form-select w-full">
            <option value="">Belum ditentukan</option>
            @foreach ($options['dosens'] as $dosen)
                <option value="{{ $dosen->id }}" @selected(old('koordinator_rmk_id', $rps->koordinator_rmk_id) == $dosen->id)>{{ $dosen->user->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Ketua Program Studi</label>

        <select name="ketua_prodi_id" class="form-select w-full">
            <option value="">Belum ditentukan</option>
            @foreach ($options['kaprodis'] as $dosen)
                <option value="{{ $dosen->id }}" @selected(old('ketua_prodi_id', $rps->ketua_prodi_id) == $dosen->id)>{{ $dosen->user->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Update

        </button>

        <a href="{{ route('mata-kuliah.rps.index', $mataKuliah) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
