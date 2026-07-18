@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Tahun Akademik
</h1>

<div class="card">

    <form action="{{ route('tahun-akademik.update', $tahunAkademik->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">

            <label class="form-label">
                Tahun Akademik
            </label>

            <input type="text"
                   name="tahun"
                   value="{{ old('tahun', $tahunAkademik->tahun) }}"
                   placeholder="Contoh: 2025/2026"
                   class="form-input">

            @error('tahun')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <div class="form-group">

            <label class="form-label">
                Semester
            </label>

            <select name="semester"
                    class="form-select">

                <option value="">
                    -- Pilih Semester --
                </option>

                <option value="Ganjil"
                    {{ old('semester', $tahunAkademik->semester) == 'Ganjil' ? 'selected' : '' }}>

                    Ganjil

                </option>

                <option value="Genap"
                    {{ old('semester', $tahunAkademik->semester) == 'Genap' ? 'selected' : '' }}>

                    Genap

                </option>

            </select>

            @error('semester')
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

            <a href="{{ route('tahun-akademik.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
