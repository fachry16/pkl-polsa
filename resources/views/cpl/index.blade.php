@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data CPL
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route('kurikulum.cpl.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah CPL

    </a>

</div>
@endif

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode CPL</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($cpls as $index => $cpl)

            <tr>

                <td>
                    {{ $cpls->firstItem() + $index }}
                </td>

                <td>
                    {{ $cpl->kode_cpl }}
                </td>

                <td>
                    {{ $cpl->deskripsi }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.cpl.edit', [$kurikulum->id, $cpl->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.cpl.destroy', [$kurikulum->id, $cpl->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus CPL ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="4"
                    class="text-center">

                    Data CPL belum tersedia.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>
    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>
    </div>
<div class="mt-5">

    {{ $cpls->links() }}

</div>

@endsection
