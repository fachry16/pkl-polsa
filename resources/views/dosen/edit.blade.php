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
            <label class="form-label">Role &amp; Jabatan <span style="color: #dc2626;">*</span></label>
            @php
                $dosenRoles = $dosen->user ? $dosen->user->getRolesList() : ['dosen'];
            @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 1.25rem; margin-top: 0.35rem;">
                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                    <input type="checkbox" name="roles[]" value="dosen" checked onclick="return false;">
                    <span style="font-weight: 500;">Dosen</span> <span style="color: #94a3b8; font-size: 0.8rem;">(Utama)</span>
                </label>
                @foreach($roles ?? \App\Models\Role::whereNotIn('kode', ['dosen', 'admin', 'mahasiswa'])->get() as $roleOption)
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="roles[]" value="{{ $roleOption->kode }}" {{ in_array($roleOption->kode, (array) old('roles', $dosenRoles)) || strtolower($dosen->jabatan) === $roleOption->kode ? 'checked' : '' }}>
                        <span>{{ $roleOption->nama }}</span>
                    </label>
                @endforeach
            </div>
            <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">
                Pilih role / jabatan tambahan jika dosen ini juga menjabat sebagai Kaprodi atau Direktur.
            </span>
            @error('roles')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('jabatan')
                <p class="form-error">{{ $message }}</p>
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
