@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Mahasiswa
</h1>

<div class="card">

    <form action="{{ route('mahasiswa.store') }}"
          method="POST">

        @csrf

        <div class="form-group">
            <label class="form-label">NIM</label>

            <input type="text"
                   name="nim"
                   value="{{ old('nim') }}"
                   class="form-input">

            @error('nim')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Nama</label>

            <input type="text"
                   name="nama"
                   value="{{ old('nama') }}"
                   class="form-input">

            @error('nama')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">

            <label class="form-label">
                Program Studi
            </label>

            <select name="program_studi_id"
                    class="form-select">

                <option value="">
                    -- Pilih Program Studi --
                </option>

                @foreach($programStudis as $prodi)

                    <option value="{{ $prodi->id }}"
                        {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>

                        {{ $prodi->nama_prodi }}

                    </option>

                @endforeach

            </select>

        </div>

        <div class="form-group">

            <label class="form-label">
                Angkatan
            </label>

            <input type="number"
                   name="angkatan"
                   value="{{ old('angkatan') }}"
                   class="form-input">

        </div>

        <div class="form-group">

            <label class="form-label">
                Tahun Akademik
            </label>

            <select name="tahun_akademik_id"
                    class="form-select">

                <option value="">
                    -- Pilih Tahun Akademik --
                </option>

                @foreach($tahunAkademiks as $tahun)

                    <option value="{{ $tahun->id }}"
                        {{ old('tahun_akademik_id') == $tahun->id ? 'selected' : '' }}>

                        {{ $tahun->tahun }}
                        -
                        {{ $tahun->semester }}

                        @if($tahun->is_active)
                            (Aktif)
                        @endif

                    </option>

                @endforeach

            </select>

        </div>

        <div class="form-group">

            <label class="form-label">
                Semester
            </label>

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

        <div class="btn-group">

            <button type="submit"
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ route('mahasiswa.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
