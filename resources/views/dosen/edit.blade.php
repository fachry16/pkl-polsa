@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Dosen
</h1>

<div class="card">

    <form action="{{ route('dosen.update', $dosen->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">
                Nama
            </label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $dosen->user->name) }}"
                   class="form-input">

            @error('name')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Email
            </label>

            <input type="email"
                   name="email"
                   value="{{ old('email', $dosen->user->email) }}"
                   class="form-input">

            @error('email')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                NIDN
            </label>

            <input type="text"
                   name="nidn"
                   value="{{ old('nidn', $dosen->nidn) }}"
                   class="form-input">

            @error('nidn')
                <p class="form-error">
                    {{ $message }}
                </p>
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
                        {{ old('program_studi_id', $dosen->program_studi_id) == $prodi->id ? 'selected' : '' }}>

                        {{ $prodi->nama_prodi }}

                    </option>

                @endforeach

            </select>

            @error('program_studi_id')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Jabatan
            </label>

            <select name="jabatan"
                    class="form-select">

                <option value="dosen"
                    {{ old('jabatan', $dosen->jabatan) == 'dosen' ? 'selected' : '' }}>

                    Dosen

                </option>

                <option value="kaprodi"
                    {{ old('jabatan', $dosen->jabatan) == 'kaprodi' ? 'selected' : '' }}>

                    Kaprodi

                </option>

            </select>

            @error('jabatan')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="btn-group">

            <button type="submit"
                    class="btn btn-warning">

                Update

            </button>

            <a href="{{ route('dosen.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
