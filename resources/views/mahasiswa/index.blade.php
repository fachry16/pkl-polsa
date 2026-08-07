@extends('layouts.app')

@section('content')

    <h1 class="page-header">
        Data Mahasiswa
    </h1>

@unless(auth()->user()->isDirektur())
<div class="mb-4">
    <a href="{{ route('mahasiswa.create') }}"
       class="btn btn-primary">

        Tambah Mahasiswa

    </a>

</div>
@endunless

<x-alert type="success" :message="session('success')" />

<form method="GET" class="filter-card">
    <div class="filter-group">
        <label class="filter-label">Program Studi</label>
        <select name="program_studi_id" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($programStudis as $prodi)
                <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                    {{ $prodi->nama_prodi }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Angkatan</label>
        <select name="angkatan" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($angkatans as $thn)
                <option value="{{ $thn }}" {{ request('angkatan') == $thn ? 'selected' : '' }}>
                    {{ $thn }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Tahun Akademik</label>
        <select name="tahun_akademik_id" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($tahunAkademiks as $ta)
                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} {{ $ta->semester }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Terapkan Filter</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
    </div>
</form>

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Angkatan</th>
                <th>Semester</th>
                <th>Tahun Akademik</th>
                <th>Akun</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($mahasiswas as $index => $mahasiswa)

            @php
                $semesterAktif = $mahasiswa->semesterMahasiswas->sortByDesc('id')->first();
            @endphp

            <tr>

                <td>
                    {{ $mahasiswas->firstItem() + $index }}
                </td>

                <td>
                    {{ $mahasiswa->nim }}
                </td>

                <td>
                    {{ $mahasiswa->nama }}
                </td>

                <td>
                    {{ $mahasiswa->programStudi->nama_prodi }}
                </td>

                <td>
                    {{ $mahasiswa->angkatan }}
                </td>

                <td>
                    {{ $semesterAktif?->semester ?? '-' }}
                </td>

                <td>
                    {{ $semesterAktif?->tahunAkademik?->tahun ?? '-' }}
                    {{ $semesterAktif?->tahunAkademik?->semester ?? '' }}
                </td>

                <td>
                    @if($mahasiswa->user)
                        <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.15rem;">{{ $mahasiswa->user->email }}</div>
                    @else
                        <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Belum</span>
                    @endif
                </td>

                <td>

                    <div class="btn-group">
                        @unless(auth()->user()->isDirektur())
                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('mahasiswa.destroy', $mahasiswa->id) }}"
                            method="DELETE"
                            title="Hapus Mahasiswa"
                            message="Hapus Mahasiswa?"
                            sub-message="Data ini akan hilang selamanya."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endunless
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="9"
                    class="text-center">

                    Data mahasiswa belum tersedia.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-5">

    {{ $mahasiswas->withQueryString()->links() }}

</div>

@endsection


