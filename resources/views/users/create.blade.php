@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah User
</h1>

<div class="card">

    <form action="{{ route('users.store') }}"
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
            <label class="form-label">Password</label>

            <input type="password"
                   name="password"
                   class="form-input">

            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">

            <label class="form-label">
                Role
            </label>

            <select name="role"
                    class="form-select">

                <option value="">-- Pilih Role --</option>

                <option value="admin"
                    {{ old('role') == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="dosen"
                    {{ old('role') == 'dosen' ? 'selected' : '' }}>
                    Dosen
                </option>

                <option value="direktur"
                    {{ old('role') == 'direktur' ? 'selected' : '' }}>
                    Direktur
                </option>

            </select>

            @error('role')
                <p class="form-error">{{ $message }}</p>
            @enderror

        </div>

        <div class="btn-group">

            <button type="submit"
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ route('users.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
