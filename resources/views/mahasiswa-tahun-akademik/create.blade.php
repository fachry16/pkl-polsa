@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Mahasiswa Aktif
</h1>

<form action="{{ route('tahun-akademik.mahasiswa.store', $tahunAkademik->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">Mahasiswa</label>

        <select name="mahasiswa_id"
                class="form-select">

            <option value="">
                -- Pilih Mahasiswa --
            </option>

            @foreach($mahasiswas as $mahasiswa)

                <option value="{{ $mahasiswa->id }}"
                    {{ old('mahasiswa_id') == $mahasiswa->id ? 'selected' : '' }}>

                    {{ $mahasiswa->nim }}
                    -
                    {{ $mahasiswa->nama }}

                </option>

            @endforeach

        </select>

        @error('mahasiswa_id')
            <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">Semester</label>

        <select name="semester"
                class="form-select">

            @for($i = 1; $i <= 14; $i++)

                <option value="{{ $i }}"
                    {{ old('semester') == $i ? 'selected' : '' }}>

                    Semester {{ $i }}

                </option>

            @endfor

        </select>

    </div>

    <div class="form-group">

        <label class="form-label">Status</label>

        <select name="status"
                class="form-select">

            <option value="Aktif">Aktif</option>
            <option value="Cuti">Cuti</option>
            <option value="Lulus">Lulus</option>
            <option value="DO">DO</option>

        </select>

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('tahun-akademik.mahasiswa.index', $tahunAkademik->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
