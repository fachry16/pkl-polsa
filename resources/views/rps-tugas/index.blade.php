@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rancangan Tugas dan Latihan
</h1>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.tugas.create', $rps->id) }}" class="btn btn-primary">Tambah Tugas</a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>Minggu Ke / Topik</th>
                <th>Nama Tugas</th>
                <th>Sub-CPMK</th>
                <th>Penugasan</th>
                <th>Ruang Lingkup</th>
                <th>Cara Pengerjaan</th>
                <th>Batas Waktu</th>
                <th>Luaran Tugas</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($tugas as $item)

            <tr>

                <td class="text-center font-bold">
                    {{ $item->minggu_topik }}
                </td>

                <td class="text-sm">
                    {{ $item->nama_tugas }}
                </td>

                <td class="text-sm">
                    {{ $item->sub_cpmk ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->penugasan ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->ruang_lingkup ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->cara_pengerjaan ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->batas_waktu ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->luaran_tugas ?? '-' }}
                </td>

                <td class="text-center">

                    <div class="btn-group">

                        <a href="{{ route('rps.tugas.edit', [$rps->id, $item->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('rps.tugas.destroy', [$rps->id, $item->id]) }}"
                            method="DELETE"
                            title="Hapus Tugas"
                            message="Hapus rancangan tugas ini?"
                            sub-message="Data tugas ini akan ikut terhapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="9"
                    class="text-center">

                    Belum ada data rancangan tugas.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-5 flex justify-between items-center">
    <a href="{{ route('mata-kuliah.rps.index', $rps->mata_kuliah_id) }}"
       class="btn btn-secondary">
        Kembali
    </a>

    <div class="pagination">
        {{ $tugas->links() }}
    </div>
</div>

@endsection