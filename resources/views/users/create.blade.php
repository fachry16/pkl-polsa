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

        <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.1rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div style="font-size: 0.85rem; color: #1e3a8a; line-height: 1.4;">
                <strong>Petunjuk Pendaftaran Akun:</strong><br>
                Manajemen User digunakan untuk membuat akun Administrator. Untuk membuat akun <strong>Dosen, Kaprodi, Direktur, atau Mahasiswa</strong>, silakan gunakan menu <a href="{{ route('dosen.index') }}" style="color: #2563eb; font-weight: 600; text-decoration: underline;">Master Data Dosen</a> atau <a href="{{ route('mahasiswa.index') }}" style="color: #2563eb; font-weight: 600; text-decoration: underline;">Master Data Mahasiswa</a> agar NIDN/NIM terdaftar dengan lengkap.
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Role <span style="color: #dc2626;">*</span></label>
            <div style="display: flex; flex-wrap: wrap; gap: 1.25rem; margin-top: 0.35rem;">
                @foreach($roles ?? \App\Models\Role::whereNotIn('kode', ['mahasiswa', 'dosen', 'kaprodi', 'direktur'])->get() as $roleOption)
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="roles[]" value="{{ $roleOption->kode }}" {{ in_array($roleOption->kode, (array) old('roles', ['admin'])) ? 'checked' : '' }}>
                        <span>{{ $roleOption->nama }}</span>
                    </label>
                @endforeach
            </div>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">
                Pilih role akun pengguna.
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
