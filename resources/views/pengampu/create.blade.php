@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Dosen Pengampu
</h1>

<form action="{{ route('pengampu.store') }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label" for="pengampu-dosen-search">Dosen</label>

        <x-searchable-select
            id="pengampu-dosen-search"
            name="dosen_id"
            :options="$dosens->map(fn ($d) => ['value' => $d->id, 'label' => $d->user->name, 'search' => $d->nidn])"
            :value="old('dosen_id')"
            placeholder="Ketik nama atau NIDN dosen" />

    </div>

    <div class="form-group">

        <label class="form-label" for="pengampu-mk-search">Mata Kuliah</label>

        <x-searchable-select
            id="pengampu-mk-search"
            name="mata_kuliah_id"
            :options="$mataKuliahs->map(fn ($mk) => ['value' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama])"
            :value="old('mata_kuliah_id')"
            placeholder="Ketik kode atau nama mata kuliah" />

    </div>

    <div class="form-group">

        <label class="form-label" for="pengampu-ta-search">Tahun Akademik</label>

        <x-searchable-select
            id="pengampu-ta-search"
            name="tahun_akademik_id"
            :options="$tahunAkademiks->map(fn ($ta) => ['value' => $ta->id, 'label' => $ta->tahun.' '.$ta->semester])"
            :value="old('tahun_akademik_id')"
            placeholder="Ketik tahun akademik" />

    </div>

    <div class="form-group">

        <label class="form-label">Semester Akademik</label>

        <select name="semester_akademik"
                class="form-select">

            <option value="Ganjil">
                Ganjil
            </option>

            <option value="Genap">
                Genap
            </option>

        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Kelas</label>

        <input type="text"
               name="kelas"
               class="form-input">

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('pengampu.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
