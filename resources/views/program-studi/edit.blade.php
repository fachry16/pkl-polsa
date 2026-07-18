@extends('layouts.app')

@section('content')

<div class="page-header">
    Edit Program Studi
</div>

<div class="card">
    <form action="{{ route('program-studi.update', $programStudi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Kode Program Studi</label>
            <input type="text" name="kode_prodi" value="{{ old('kode_prodi', $programStudi->kode_prodi) }}" class="form-input" placeholder="Contoh: IF">
            @error('kode_prodi')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Nama Program Studi</label>
            <input type="text" name="nama_prodi" value="{{ old('nama_prodi', $programStudi->nama_prodi) }}" class="form-input" placeholder="Contoh: Informatika">
            @error('nama_prodi')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Jenjang</label>
            <select name="jenjang" class="form-select">
                <option value="">-- Pilih Jenjang --</option>
                <option value="D3" {{ old('jenjang', $programStudi->jenjang) == 'D3' ? 'selected' : '' }}>D3</option>
                <option value="D4" {{ old('jenjang', $programStudi->jenjang) == 'D4' ? 'selected' : '' }}>D4</option>
                <option value="S1" {{ old('jenjang', $programStudi->jenjang) == 'S1' ? 'selected' : '' }}>S1</option>
                <option value="S2" {{ old('jenjang', $programStudi->jenjang) == 'S2' ? 'selected' : '' }}>S2</option>
            </select>
            @error('jenjang')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Akreditasi</label>
            <select name="akreditasi" class="form-select">
                <option value="Baik" {{ old('akreditasi', $programStudi->akreditasi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                <option value="Baik Sekali" {{ old('akreditasi', $programStudi->akreditasi) == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                <option value="Unggul" {{ old('akreditasi', $programStudi->akreditasi) == 'Unggul' ? 'selected' : '' }}>Unggul</option>
            </select>
            @error('akreditasi')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('program-studi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

    </form>
</div>

@endsection
