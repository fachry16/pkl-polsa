@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Profil Lulusan
</h1>

@if ($errors->any())

<div class="alert alert-error">

    <ul>

        @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<form action="{{ route('kurikulum.profil-lulusan.update', [$kurikulum->id, $profilLulusan->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">
            Kode PL
        </label>

        <input type="text"
               name="kode_pl"
               value="{{ old('kode_pl', $profilLulusan->kode_pl) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">
            Nama Profil Lulusan
        </label>

        <input type="text"
               name="nama_pl"
               value="{{ old('nama_pl', $profilLulusan->nama_pl) }}"
               class="form-input w-full">

    </div>

    <div class="form-group">

        <label class="form-label">
            Profesi
        </label>

        <input type="text"
               name="profesi"
               value="{{ old('profesi', $profilLulusan->profesi) }}"
               class="form-input w-full">

    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="btn btn-warning">

            Update

        </button>

        <a href="{{ route('kurikulum.profil-lulusan.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
