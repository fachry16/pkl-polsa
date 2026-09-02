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
            <label class="form-label">Role <span style="color: #dc2626;">*</span></label>
            <div style="display: flex; flex-wrap: wrap; gap: 1.25rem; margin-top: 0.35rem;">
                @foreach($roles ?? \App\Models\Role::whereNotIn('kode', ['mahasiswa'])->get() as $roleOption)
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="roles[]" value="{{ $roleOption->kode }}" {{ in_array($roleOption->kode, (array) old('roles', ['dosen'])) ? 'checked' : '' }}>
                        <span>{{ $roleOption->nama }}</span>
                    </label>
                @endforeach
            </div>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">
                Pilih satu atau lebih role. Contoh: Dosen &amp; Kaprodi, atau Dosen &amp; Direktur.
            </span>
            @error('roles')
                <p class="form-error">{{ $message }}</p>
            @enderror
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
