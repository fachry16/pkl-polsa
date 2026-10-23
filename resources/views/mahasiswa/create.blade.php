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

            <label class="form-label" for="mahasiswa-prodi-search">
                Program Studi
            </label>

            <x-searchable-select
                id="mahasiswa-prodi-search"
                name="program_studi_id"
                :options="$programStudis->map(fn ($prodi) => ['value' => $prodi->id, 'label' => $prodi->nama_prodi])"
                :value="old('program_studi_id')"
                placeholder="Ketik nama program studi" />

        </div>

        <div class="form-group">

            <label class="form-label">
                Angkatan
            </label>

            <input type="number"
                   name="angkatan"
                   value="{{ old('angkatan', date('Y')) }}"
                   class="form-input">

        </div>

        <div class="form-group">
            <label class="form-label">
                Program / Jenis Kelas
            </label>
            <select name="jenis_kelas" class="form-select">
                <option value="Reguler" {{ old('jenis_kelas', 'Reguler') === 'Reguler' ? 'selected' : '' }}>
                    Reguler (Kelas A / Pagi)
                </option>
                <option value="Karyawan" {{ old('jenis_kelas') === 'Karyawan' ? 'selected' : '' }}>
                    Karyawan (Kelas B / Sore-Malam)
                </option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">
                Status
            </label>
            <select name="status" class="form-select">
                @foreach(['Aktif', 'Cuti', 'DO', 'Lulus', 'Non Aktif'] as $st)
                    <option value="{{ $st }}" {{ old('status', 'Aktif') === $st ? 'selected' : '' }}>
                        {{ $st }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">

            <label class="form-label" for="mahasiswa-ta-search">
                Tahun Akademik
            </label>

            <x-searchable-select
                id="mahasiswa-ta-search"
                name="tahun_akademik_id"
                :options="$tahunAkademiks->map(fn ($tahun) => ['value' => $tahun->id, 'label' => $tahun->tahun.' - '.$tahun->semester.($tahun->is_active ? ' (Aktif)' : '')])"
                :value="old('tahun_akademik_id')"
                placeholder="Ketik tahun akademik" />

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
