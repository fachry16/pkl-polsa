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
            <label class="form-label">Role <span style="color: #dc2626;">*</span></label>
            @php
                $userRoles = $user->getRolesList();
            @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 1.25rem; margin-top: 0.35rem;">
                @foreach($roles ?? \App\Models\Role::all() as $roleOption)
                    @if($roleOption->kode !== 'mahasiswa' || in_array('mahasiswa', $userRoles))
                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="roles[]" value="{{ $roleOption->kode }}" {{ in_array($roleOption->kode, (array) old('roles', $userRoles)) ? 'checked' : '' }}>
                            <span>{{ $roleOption->nama }}</span>
                        </label>
                    @endif
                @endforeach
            </div>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">
                Pilih satu atau lebih role. Contoh: Dosen &amp; Kaprodi, atau Dosen &amp; Direktur.
            </span>
            @error('roles')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('roles.*')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('role')
                <p class="form-error">{{ $message }}</p>
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
