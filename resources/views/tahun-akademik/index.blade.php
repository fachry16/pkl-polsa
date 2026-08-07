@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Tahun Akademik
</h1>

@unless(auth()->user()->isDirektur())
<div class="mb-4">
    <a href="{{ route('tahun-akademik.create') }}"
       class="btn btn-primary">

        Tambah Tahun Akademik

    </a>
</div>
@endunless

@if(session('success'))
<div class="alert alert-success mb-3">
    {{ session('success') }}
</div>
@endif

<x-alert type="error" :message="session('error')" />

@if($errors->any())
<div class="alert alert-danger mb-3">
    {{ $errors->first() }}
</div>
@endif

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($tahunAkademiks as $index => $ta)

        <tr>

            <td>
                {{ $tahunAkademiks->firstItem() + $index }}
            </td>

            <td>
                {{ $ta->tahun }}
            </td>

            <td>
                {{ $ta->semester }}
            </td>

            <td>

                @if($ta->is_active)

                    <span class="badge badge-disetujui">
                        Aktif
                    </span>

                @else

                    <span class="badge badge-draft">
                        Tidak Aktif
                    </span>

                @endif

            </td>

            <td>

                <div class="btn-group">

                    <a href="{{ route('tahun-akademik.mahasiswa.index', $ta->id) }}"
                        class="btn btn-sm btn-secondary">

                           Cek Mahasiswa

                    </a>
                    @unless(auth()->user()->isDirektur())
                    <a href="{{ route('tahun-akademik.edit', $ta->id) }}"
                       class="btn btn-warning btn-sm">

                        Edit

                    </a>
                    @if(!$ta->is_active)

                    <form action="{{ route('tahun-akademik.aktifkan', $ta->id) }}"
                          method="POST">

                        @csrf
                        @method('PATCH')

                        <button class="btn btn-success btn-sm">

                            Aktifkan

                        </button>

                    </form>

                    <x-confirm
                        action="{{ route('tahun-akademik.destroy', $ta->id) }}"
                        method="DELETE"
                        title="Hapus Tahun Akademik"
                        message="Hapus tahun akademik ini?"
                        sub-message="Data yang terkait dengan tahun akademik ini akan terpengaruh."
                        buttonText="Hapus"
                        confirmText="Ya, Hapus"
                    />

                    @endif
                    @endunless

                </div>

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="5"
                class="text-center text-sm" style="padding: 1.5rem; color: #6b7280;">

                Data tahun akademik belum tersedia.

            </td>

        </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $tahunAkademiks->links() }}

</div>

@endsection
