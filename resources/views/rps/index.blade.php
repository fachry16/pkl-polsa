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

    </table>

    <div class="btn-group mt-5">

        <a href="{{ route('rps.pertemuan.index', $rps) }}"
           class="btn btn-primary">
            Pertemuan
        </a>

        <a href="{{ route('rps.tugas.index', $rps) }}"
           class="btn btn-secondary">
            Tugas &amp; Latihan
        </a>

        <a href="{{ route('rps.bentuk-evaluasi.index', $rps) }}"
           class="btn btn-success">
            Rancangan Evaluasi
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

            @if($kelengkapan['siap'])
            <form action="{{ route('rps.ajukan', $rps) }}" method="POST">

                @csrf
                @method('PATCH')

                <button class="btn btn-primary">
                    {{ $rps->status == 'Revisi' ? 'Ajukan Ulang' : 'Ajukan ke Kaprodi' }}
                </button>

            </form>
            @else
            <button class="btn btn-primary" disabled
                    title="Lengkapi seluruh pertemuan (minggu 1-16), tugas & latihan, dan penilaian terlebih dahulu">
                {{ $rps->status == 'Revisi' ? 'Ajukan Ulang' : 'Ajukan ke Kaprodi' }}
            </button>
            @endif

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
            Kembali ke Riwayat Mengajar &amp; RPS
        </a>
    @else
        <a href="{{ url()->previous() }}"
           class="btn btn-secondary">
            Kembali
        </a>
    @endif

</div>

@endsection
