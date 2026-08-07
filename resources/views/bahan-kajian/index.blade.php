@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Bahan Kajian
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route('kurikulum.bahan-kajian.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah Bahan Kajian

    </a>

</div>
@endif

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode BK</th>
                <th>Nama BK</th>
                <th>Referensi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($bahanKajians as $index => $bahanKajian)

            <tr>

                <td>
                    {{ $bahanKajians->firstItem() + $index }}
                </td>

                <td>
                    {{ $bahanKajian->kode_bk }}
                </td>

                <td>
                    {{ $bahanKajian->nama_bk }}
                </td>

                <td>
                    {{ $bahanKajian->referensi ?? '-' }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.bahan-kajian.edit', [$kurikulum->id, $bahanKajian->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.bahan-kajian.destroy', [$kurikulum->id, $bahanKajian->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus bahan kajian ini?"
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

                    Data bahan kajian belum tersedia.

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

    {{ $bahanKajians->links() }}

</div>

@endsection
