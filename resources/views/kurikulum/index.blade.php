@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Kurikulum
</h1>

@if(auth()->user()->role !== 'dosen')
<div class="mb-5">
    <a href="{{ route('kurikulum.create') }}"
       class="btn btn-primary">

        Tambah Kurikulum

    </a>
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<x-alert type="error" :message="session('error')" />

<div class="table-container">

    <table class="data-table">

        <thead>
            <tr>
                <th>No</th>
                <th>Program Studi</th>
                <th>Nama Kurikulum</th>
                <th>Tahun Berlaku</th>
                <th>Beban Studi</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($kurikulums as $index => $kurikulum)

            <tr>

                <td>
                    {{ $kurikulums->firstItem() + $index }}
                </td>

                <td>
                    {{ $kurikulum->programStudi->nama_prodi }}
                </td>

                <td>
                    {{ $kurikulum->nama_kurikulum }}
                </td>

                <td>
                    {{ $kurikulum->tahun_berlaku }}
                </td>
                <td>{{ $kurikulum->beban_studi }}</td>

                <td>{{ $kurikulum->deskripsi }}</td>

                <td>

                    @if($kurikulum->status == 'Aktif')

                        <span class="badge">
                            Aktif
                        </span>

                    @elseif($kurikulum->status == 'Draft')
                        <span class="badge">
                            Draft
                        </span>
                    @else
                        <span class="badge">
                            Arsip
                        </span>

                    @endif

                </td>

                <td>

                    <div class="flex flex-wrap gap-2">

                        @if(auth()->user()->role !== 'dosen')
                        @if($kurikulum->status != 'Aktif')

                        <x-confirm
                            action="{{ route('kurikulum.aktifkan', $kurikulum->id) }}"
                            method="PATCH"
                            title="Aktifkan Kurikulum"
                            message="Aktifkan kurikulum ini?"
                            sub-message="Kurikulum yang aktif akan digunakan sebagai acuan utama."
                            buttonText="Aktifkan"
                            buttonClass="btn btn-success btn-sm"
                            confirmText="Ya, Aktifkan"
                            confirmClass="btn-success"
                        />

                        @endif
                        @endif
                        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
                        class="btn btn-primary btn-sm">
                            Kelola Struktur
                        </a>

                        @if(auth()->user()->role !== 'dosen')
                        <a href="{{ route('kurikulum.edit', $kurikulum->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.destroy', $kurikulum->id) }}"
                            method="DELETE"
                            title="Hapus Kurikulum"
                            message="Hapus kurikulum ini?"
                            sub-message="Data terkait seperti CPL, CPMK, dan MK akan ikut terhapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif

                    </div>

                </td>

            </tr>

            @empty

            <tr>
                <td colspan="6"
                    class="text-center">

                    Data kurikulum belum tersedia.

                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

</div>
<div class="mt-5">
    <a href="{{ route('program-studi.index') }}" class="btn btn-secondary">
        Kembali
    </a>
</div>

<div class="mt-5">
    {{ $kurikulums->links() }}
</div>

@endsection
