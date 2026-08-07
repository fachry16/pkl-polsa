@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Mahasiswa Aktif
</h1>

<p class="mb-5">
    Tahun Akademik :
    {{ $tahunAkademik->tahun }}
</p>

<x-alert type="success" :message="session('success')" />

@unless(auth()->user()->isDirektur())
<div class="mb-4">

    <a href="{{ route('tahun-akademik.mahasiswa.create', $tahunAkademik->id) }}"
       class="btn btn-primary">

        Tambah Mahasiswa

    </a>

</div>
@endunless

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($mahasiswas as $index => $item)

                <tr>

                    <td>
                        {{ $mahasiswas->firstItem() + $index }}
                    </td>

                    <td>
                        {{ $item->mahasiswa->nim }}
                    </td>

                    <td>
                        {{ $item->mahasiswa->nama }}
                    </td>

                    <td>
                        Semester {{ $item->semester }}
                    </td>

                    <td>

                        @if($item->status == 'Aktif')

                            <span class="badge badge-disetujui">
                                Aktif
                            </span>

                        @elseif($item->status == 'Cuti')

                            <span class="badge badge-revisi">
                                Cuti
                            </span>

                        @elseif($item->status == 'Lulus')

                            <span class="badge">
                                Lulus
                            </span>

                        @else

                            <span class="badge badge-draft">
                                DO
                            </span>

                        @endif

                    </td>

                    <td>
                        @unless(auth()->user()->isDirektur())
                        <x-confirm
                            action="{{ route('tahun-akademik.mahasiswa.destroy', [$tahunAkademik->id, $item->id]) }}"
                            method="DELETE"
                            title="Hapus Data"
                            message="Hapus data mahasiswa tahun akademik ini?"
                            sub-message="Data tidak dapat dikembalikan setelah dihapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endunless
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6"
                        class="text-center">

                        Belum ada mahasiswa.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $mahasiswas->links() }}

</div>

<div class="mt-4">

    <a href="{{ route('tahun-akademik.index') }}"
       class="btn btn-secondary">

        Kembali

    </a>

</div>

@endsection
