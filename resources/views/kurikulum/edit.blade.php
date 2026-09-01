@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Kurikulum
</h1>

<div class="card">

    <form action="{{ route('kurikulum.update', $kurikulum->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">

            <label class="form-label">
                Program Studi
            </label>

            <select name="program_studi_id"
                    class="form-select">

                @foreach($programStudis as $prodi)

                <option value="{{ $prodi->id }}"
                    {{ old('program_studi_id', $kurikulum->program_studi_id) == $prodi->id ? 'selected' : '' }}>

                    {{ $prodi->nama_prodi }}

                </option>

                @endforeach

            </select>

            @error('program_studi_id')
            <p class="form-error">{{ $message }}</p>
            @enderror

        </div>

        <div class="form-group">

            <label class="form-label">
                Nama Kurikulum
            </label>

            <input type="text"
                   name="nama_kurikulum"
                   value="{{ old('nama_kurikulum', $kurikulum->nama_kurikulum) }}"
                   class="form-input">

            @error('nama_kurikulum')
            <p class="form-error">{{ $message }}</p>
            @enderror

        </div>

        <div class="form-group">

            <label class="form-label">
                Tahun Berlaku
            </label>

            <input type="number"
                   name="tahun_berlaku"
                   value="{{ old('tahun_berlaku', $kurikulum->tahun_berlaku) }}"
                   class="form-input">

            @error('tahun_berlaku')
            <p class="form-error">{{ $message }}</p>
            @enderror

        </div>
        <div class="form-group">
            <label class="form-label">
                Beban Studi
            </label>
            <input type="number" name="beban_studi" value="{{ old('beban_studi', $kurikulum->beban_studi) }}" class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" rows="5" class="form-textarea">{{ old('deskripsi', $kurikulum->deskripsi) }}</textarea>
        </div>

        <div class="btn-group">

            <button type="submit"
                    class="btn btn-warning">

                Update

            </button>

            <a href="{{ route('program-studi.kurikulum', $kurikulum->program_studi_id) }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection
