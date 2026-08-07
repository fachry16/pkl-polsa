@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Mata Kuliah
</h1>

<form action="{{ route('kurikulum.mata-kuliah.update', [$kurikulum->id, $mataKuliah->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">Kode MK</label>

        <input type="text"
               name="kode"
               value="{{ old('kode', $mataKuliah->kode) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">Nama Mata Kuliah</label>

        <input type="text"
               name="nama"
               value="{{ old('nama', $mataKuliah->nama) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">Semester</label>

        <select name="semester"
                class="form-select w-full">

            @for($i=1; $i<=14; $i++)

                <option value="{{ $i }}"
                    {{ old('semester', $mataKuliah->semester) == $i ? 'selected' : '' }}>

                    Semester {{ $i }}

                </option>

            @endfor

        </select>

    </div>

    <div class="form-group">

        <label class="form-label">SKS Teori</label>

        <input type="number"
               name="sks_teori"
               min="0"
               value="{{ old('sks_teori', $mataKuliah->sks_teori) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">SKS Praktik</label>

        <input type="number"
               name="sks_praktikum"
               min="0"
               value="{{ old('sks_praktikum', $mataKuliah->sks_praktikum) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">Jenis</label>

        <select name="jenis"
                class="form-select w-full">

            <option value="Wajib"
                {{ old('jenis', $mataKuliah->jenis) == 'Wajib' ? 'selected' : '' }}>
                Wajib
            </option>

            <option value="Pilihan"
                {{ old('jenis', $mataKuliah->jenis) == 'Pilihan' ? 'selected' : '' }}>
                Pilihan
            </option>

        </select>

    </div>

    <div class="flex gap-2">

        <button class="btn btn-warning">
            Update
        </button>

        <a href="{{ route('kurikulum.mata-kuliah.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
