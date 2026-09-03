@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rumusan Nilai Akhir CPL
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route('kurikulum.rumusan-nilai-akhir-cpl.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah Data

    </a>

</div>
@endif

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>CPL</th>
                <th>MK</th>
                <th>CPMK</th>
                <th>Skor Maks</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($items as $index => $item)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    <div>{{ $item->cpl->kode_cpl }}</div>
                    @if($item->cpl->deskripsi)
                    <div class="table-sub">{{ $item->cpl->deskripsi }}</div>
                    @endif
                </td>

                <td>
                    <div>{{ $item->mataKuliah->kode }}</div>
                    <div class="table-sub">{{ $item->mataKuliah->nama }}</div>
                </td>

                <td>
                    <div>{{ $item->cpmk->kode_cpmk }}</div>
                    @if($item->cpmk->deskripsi)
                    <div class="table-sub">{{ $item->cpmk->deskripsi }}</div>
                    @endif
                </td>

                <td>
                    {{ $item->skor_maks }}
                </td>

                <td>
                    {{ $item->total }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.rumusan-nilai-akhir-cpl.edit', [$kurikulum->id, $item->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.rumusan-nilai-akhir-cpl.destroy', [$kurikulum->id, $item->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus data ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7"
                    class="text-center">

                    Data belum tersedia.

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

@endsection
