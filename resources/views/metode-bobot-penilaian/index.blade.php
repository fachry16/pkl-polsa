@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Metode dan Bobot Penilaian
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">

    <a href="{{ route('kurikulum.metode-bobot-penilaian.create', $kurikulum->id) }}"
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
                <th>Partisipasi</th>
                <th>Kuis</th>
                <th>Tugas Teori (Individu)</th>
                <th>Unjuk Kerja (Presentasi)</th>
                <th>Tes Tulis (UTS)</th>
                <th>Tes Tulis (UAS)</th>
                <th>Tugas Teori (Kelompok)</th>
                <th>Tugas Praktikum</th>
                <th>Responsi</th>
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
                    {{ $item->partisipasi }}%
                </td>

                <td>
                    {{ $item->kuis }}%
                </td>

                <td>
                    {{ $item->tugas_teori_individu }}%
                </td>

                <td>
                    {{ $item->unjuk_kerja_presentasi }}%
                </td>

                <td>
                    {{ $item->tes_tulis_uts }}%
                </td>

                <td>
                    {{ $item->tes_tulis_uas }}%
                </td>

                <td>
                    {{ $item->tugas_teori_kelompok }}%
                </td>

                <td>
                    {{ $item->tugas_praktikum }}%
                </td>

                <td>
                    {{ $item->responsi }}%
                </td>

                <td class="font-medium">
                    {{ $item->total }}%
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.metode-bobot-penilaian.edit', [$kurikulum->id, $item->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.metode-bobot-penilaian.destroy', [$kurikulum->id, $item->id]) }}"
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

                <td colspan="15"
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
