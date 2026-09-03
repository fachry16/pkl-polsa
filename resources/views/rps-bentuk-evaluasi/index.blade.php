@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rancangan Evaluasi
</h1>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.bentuk-evaluasi.create', $rps->id) }}" class="btn btn-primary">Tambah Bentuk Evaluasi</a>
</div>

<x-alert type="success" :message="session('success')" />

@if($bentukEvaluasis->count())

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>Bentuk Evaluasi</th>
                <th>Sub-CPMK</th>
                <th>Formatif / Sumatif</th>
                <th>Instrumen Penilaian</th>
                <th>Tagihan (Bukti)</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @foreach($bentukEvaluasis as $bentuk)

            <tr>

                <td class="font-semibold">
                    {{ $bentuk->bentuk_evaluasi }}
                </td>

                <td class="text-sm">
                    {{ $bentuk->sub_cpmk ?? '-' }}
                </td>

                <td class="text-sm text-center">
                    @if($bentuk->formatif)
                        <span class="badge badge-draft">Formatif</span>
                    @endif
                    @if($bentuk->sumatif)
                        <span class="badge badge-disetujui">Sumatif</span>
                    @endif
                    @if(! $bentuk->formatif && ! $bentuk->sumatif)
                        -
                    @endif
                </td>

                <td class="text-sm">
                    <div>{{ $bentuk->instrumen ?? '-' }}</div>
                    @if($bentuk->frekuensi)
                        <div class="table-sub">{{ $bentuk->frekuensi }}</div>
                    @endif
                </td>

                <td class="text-sm">
                    {{ $bentuk->tagihan ?? '-' }}
                </td>

                <td class="text-center text-sm font-bold">
                    {{ $bentuk->bobot }}%
                </td>

                <td class="text-center">

                    <div class="btn-group">

                        <a href="{{ route('rps.bentuk-evaluasi.edit', [$rps->id, $bentuk->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('rps.bentuk-evaluasi.destroy', [$rps->id, $bentuk->id]) }}"
                            method="DELETE"
                            title="Hapus Bentuk Evaluasi"
                            message="Hapus bentuk evaluasi ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />

                    </div>

                </td>

            </tr>

        @endforeach

        </tbody>

        <tfoot>

            <tr>
                <td colspan="5" class="text-right font-bold">Total Bobot</td>
                <td class="text-center font-bold">{{ $totalBobot }}%</td>
                <td></td>
            </tr>

        </tfoot>

    </table>

</div>

@else

<div class="card">
    <p class="mb-4">Belum ada rancangan evaluasi.</p>
    <a href="{{ route('rps.bentuk-evaluasi.create', $rps->id) }}" class="btn btn-primary">Tambah Bentuk Evaluasi</a>
</div>

@endif

<div class="mt-5">
    <a href="{{ route('mata-kuliah.rps.index', $rps->mata_kuliah_id) }}"
       class="btn btn-secondary">
        Kembali
    </a>
</div>

@endsection
