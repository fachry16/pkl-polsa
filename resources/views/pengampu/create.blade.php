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

        <label class="form-label">Dosen</label>

        <select name="dosen_id"
                class="form-select">

            @foreach($dosens as $dosen)

                <option value="{{ $dosen->id }}">
                    {{ $dosen->user->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Mata Kuliah</label>

        <select name="mata_kuliah_id"
                class="form-select">

            @foreach($mataKuliahs as $mk)

                <option value="{{ $mk->id }}">
                    {{ $mk->kode }}
                    -
                    {{ $mk->nama }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Tahun Akademik</label>

        <select name="tahun_akademik_id"
                class="form-select">

            @foreach($tahunAkademiks as $ta)

                <option value="{{ $ta->id }}">
                    {{ $ta->tahun }}
                </option>

            @endforeach

        </select>

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
