@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data CPMK
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route('kurikulum.cpmk.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah CPMK

    </a>

</div>
@endif

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode CPMK</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($cpmks as $index => $cpmk)

            <tr>

                <td>
                    {{ $cpmks->firstItem() + $index }}
                </td>

                <td>
                    {{ $cpmk->kode_cpmk }}
                </td>

                <td>
                    {{ $cpmk->deskripsi }}
                </td>
                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.cpmk.edit', [$kurikulum->id, $cpmk->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.cpmk.destroy', [$kurikulum->id, $cpmk->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus CPMK ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center">

                    Data CPMK belum tersedia.

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

    {{ $cpmks->links() }}

</div>

@endsection
