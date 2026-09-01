@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Mata Kuliah
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5 flex gap-2">

    <a href="{{ route('kurikulum.mata-kuliah.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah Mata Kuliah

    </a>

</div>
@endif

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode MK</th>
                <th>Nama MK</th>
                <th>Semester</th>
                <th>Teori</th>
                <th>Praktik</th>
                <th>Total</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($mataKuliahs as $index => $mataKuliah)

            <tr>

                <td>
                    {{ $mataKuliahs->firstItem() + $index }}
                </td>

                <td>
                    {{ $mataKuliah->kode }}
                </td>

                <td>
                    {{ $mataKuliah->nama }}
                </td>

                <td>
                    {{ $mataKuliah->semester }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_teori }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_praktikum }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_teori + $mataKuliah->sks_praktikum }}
                </td>

                <td>
                    {{ $mataKuliah->jenis }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.mata-kuliah.edit', [$kurikulum->id, $mataKuliah->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.mata-kuliah.destroy', [$kurikulum->id, $mataKuliah->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus mata kuliah ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="10"
                    class="text-center">

                    Data mata kuliah belum tersedia.

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

    {{ $mataKuliahs->links() }}

</div>

@endsection
