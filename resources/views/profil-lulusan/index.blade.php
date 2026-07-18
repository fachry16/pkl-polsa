@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Profil Lulusan
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route(
        'kurikulum.profil-lulusan.create',
        $kurikulum->id
    ) }}"
    class="btn btn-primary">

        Tambah Profil Lulusan

    </a>

</div>
@endif

<table class="detail-table">

    <thead>

        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Profesi</th>
            <th>Aksi</th>
        </tr>

    </thead>

    <tbody>

        @foreach($profilLulusans as $pl)

        <tr>

            <td>
                {{ $pl->kode_pl }}
            </td>

            <td>
                {{ $pl->nama_pl }}
            </td>

            <td>
                {{ $pl->profesi }}
            </td>

            <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.profil-lulusan.edit', [$kurikulum->id, $pl->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.profil-lulusan.destroy', [$kurikulum->id, $pl->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus profil lulusan ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

        @endforeach

    </tbody>

</table>
    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>
    </div>

@endsection
