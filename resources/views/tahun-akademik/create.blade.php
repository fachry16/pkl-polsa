@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Tahun Akademik
</h1>

<div class="card">

    <form action="{{ route('tahun-akademik.store') }}"
          method="POST">

        @csrf

        <div class="form-group">

            <label class="form-label">
                Tahun Akademik
            </label>

            <input type="text"
                   name="tahun"
                   value="{{ old('tahun') }}"
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
                    {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>

                    Ganjil

                </option>

                <option value="Genap"
                    {{ old('semester') == 'Genap' ? 'selected' : '' }}>

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
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ route('tahun-akademik.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
