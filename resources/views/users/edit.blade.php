@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit User
</h1>

<div class="card">

    <form action="{{ route('users.update', $user->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">
                Nama
            </label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $user->name) }}"
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
                   value="{{ old('email', $user->email) }}"
                   class="form-input">

            @error('email')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Password <span class="text-sm" style="color: #6b7280;">(kosongkan jika tidak diubah)</span>
            </label>

            <input type="password"
                   name="password"
                   class="form-input">

            @error('password')
                <p class="form-error">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="form-group">

            <label class="form-label">
                Role
            </label>

            <select name="role"
                    class="form-select">

                <option value="admin"
                    {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="dosen"
                    {{ old('role', $user->role) == 'dosen' ? 'selected' : '' }}>
                    Dosen
                </option>

                <option value="direktur"
                    {{ old('role', $user->role) == 'direktur' ? 'selected' : '' }}>
                    Direktur
                </option>

            </select>

            @error('role')
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

            <a href="{{ route('users.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
