@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Pertemuan RPS
</h1>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.pertemuan.create', $rps->id) }}" class="btn btn-primary">Tambah Pertemuan</a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>Minggu</th>
                <th>Sub CPMK</th>
                <th>Materi Pembelajaran</th>
                <th>Metode</th>
                <th>Pengalaman Belajar</th>
                <th>Indikator</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($pertemuans as $pertemuan)

            <tr>

                <td class="text-center font-bold">
                    {{ $pertemuan->minggu }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->sub_cpmk }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->materi }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->metode ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->pengalaman_belajar ?? '-' }}
                </td>
                <td class="text-sm">
                    {{ $pertemuan->indikator ?? '-' }}
                </td>

                <td class="text-center text-sm">
                    {{ $pertemuan->bobot ? $pertemuan->bobot . '%' : '-' }}
                </td>

                <td class="text-center">

                    <div class="btn-group">

                        <a href="{{ route('rps.pertemuan.edit', [$rps->id, $pertemuan->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('rps.pertemuan.destroy', [$rps->id, $pertemuan->id]) }}"
                            method="DELETE"
                            title="Hapus Pertemuan"
                            message="Hapus pertemuan ini?"
                            sub-message="Data penilaian dan absensi terkait akan ikut terhapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="11"
                    class="text-center">

                    Belum ada data pertemuan.

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
        {{ $pertemuans->links() }}
    </div>
</div>

@endsection
