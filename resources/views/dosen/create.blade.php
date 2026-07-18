@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Dosen
</h1>

<div class="card">

    <form action="{{ route('dosen.store') }}"
          method="POST">

        @csrf

        <div class="form-group">
            <label class="form-label">Nama</label>

            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-input">

            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>

            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-input">

            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">NIDN</label>

            <input type="text"
                   name="nidn"
                   value="{{ old('nidn') }}"
                   class="form-input">

            @error('nidn')
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

            @error('program_studi_id')
                <p class="form-error">{{ $message }}</p>
            @enderror

        </div>

        <div class="form-group">

            <label class="form-label">
                Jabatan
            </label>

            <select name="jabatan"
                    class="form-select">

                <option value="dosen"
                    {{ old('jabatan') == 'dosen' ? 'selected' : '' }}>

                    Dosen

                </option>

                <option value="kaprodi"
                    {{ old('jabatan') == 'kaprodi' ? 'selected' : '' }}>

                    Kaprodi

                </option>

            </select>

        </div>

        <div class="btn-group">

            <button type="submit"
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ route('dosen.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
