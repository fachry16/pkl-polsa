@extends('layouts.app')

@section('content')

<h1 class="page-header">
    RPS - {{ $mataKuliah->nama }}
</h1>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@if($rps)

<div class="card">

    <table class="detail-table">

        <tr>
            <td class="font-semibold">Kode RPS</td>
            <td>{{ $rps->kode_rps }}</td>
        </tr>

        <tr>
            <td class="font-semibold">Semester</td>
            <td>{{ $rps->semester }}</td>
        </tr>

        <tr>
            <td class="font-semibold">Dosen Pengampu</td>
            <td>{{ $rps->dosen_pengampu }}</td>
        </tr>

        <tr>
            <td class="font-semibold">Deskripsi</td>
            <td>{{ $rps->deskripsi_mata_kuliah }}</td>
        </tr>

        <tr>
            <td class="font-semibold">Status</td>
            <td>

                @if($rps->status == 'Draft')
                    <span class="badge badge-draft">Draft</span>

                @elseif($rps->status == 'Diajukan')
                    <span class="badge badge-diajukan">Diajukan</span>

                @elseif($rps->status == 'Revisi')
                    <span class="badge badge-revisi">Revisi</span>

                @elseif($rps->status == 'Disetujui')
                    <span class="badge badge-disetujui">Disetujui</span>
                @endif

            </td>
        </tr>

    </table>

    @if($rps->status == 'Revisi' && $rps->catatan_revisi)

    <div class="revision-box mt-5">
        <strong>Catatan Revisi dari Kaprodi:</strong>
        <p class="mt-2">{{ $rps->catatan_revisi }}</p>
    </div>

    @endif

    @if($rps->status == 'Disetujui' && $rps->disetujuiOleh)

    <div class="approval-box mt-5">
        <strong>Disetujui oleh:</strong>
        <p class="mt-1">{{ $rps->disetujuiOleh->name }}
            @if($rps->tanggal_disetujui)
                pada {{ $rps->tanggal_disetujui->format('d/m/Y H:i') }}
            @endif
        </p>
    </div>

    @endif

    <div class="btn-group mt-5">

        <a href="{{ route('rps.pertemuan.index', $rps) }}"
           class="btn btn-primary">
            Pertemuan
        </a>

        <a href="{{ route('rps.penilaian.index', $rps) }}"
           class="btn btn-success">
            Penilaian
        </a>

        @if($rps->status == 'Disetujui')

        <a href="{{ route('rps.ekstrak-pdf', $rps) }}"
           class="btn btn-primary">
            Ekstrak PDF
        </a>

        @endif

        @if(in_array($rps->status, ['Draft', 'Revisi']))

        <form action="{{ route('rps.ajukan', $rps) }}" method="POST">

            @csrf
            @method('PATCH')

            <button class="btn btn-primary">
                {{ $rps->status == 'Revisi' ? 'Ajukan Ulang' : 'Ajukan ke Kaprodi' }}
            </button>

        </form>

        @endif

        @if($rps->status != 'Disetujui')

        <a href="{{ route('mata-kuliah.rps.edit', [$mataKuliah, $rps]) }}"
           class="btn btn-warning">
            Edit
        </a>

        <x-confirm
            action="{{ route('mata-kuliah.rps.destroy', [$mataKuliah, $rps]) }}"
            method="DELETE"
            title="Hapus RPS"
            message="Hapus RPS ini?"
            sub-message="Semua data pertemuan dan penilaian terkait akan ikut terhapus."
            buttonText="Hapus"
            buttonClass="btn btn-danger"
            confirmText="Ya, Hapus"
        />

        @endif

    </div>

</div>

@else

<div class="card">

    <p class="mb-4">RPS belum tersedia.</p>

    <a href="{{ route('mata-kuliah.rps.create', $mataKuliah) }}"
       class="btn btn-primary">
        Buat RPS
    </a>

</div>

@endif

<div class="mt-5">

    @if(auth()->user()->role === 'dosen' && auth()->user()->dosen && strtolower(auth()->user()->dosen->jabatan) !== 'kaprodi')
        <a href="{{ route('dosen.self.riwayat') }}"
           class="btn btn-secondary">
            Kembali ke Riwayat
        </a>
    @else
        <a href="{{ route('kurikulum.mata-kuliah.index', $mataKuliah->kurikulum_id) }}"
           class="btn btn-secondary">
            Kembali
        </a>
    @endif

</div>

@endsection
