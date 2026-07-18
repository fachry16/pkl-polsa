@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Dosen Pengampu
</h1>

@unless(auth()->user()->isDirektur())
<a href="{{ route('pengampu.create') }}"
   class="btn btn-primary">

    Tambah Pengampu

</a>
@endunless

<x-alert type="success" :message="session('success')" />

<form method="GET" class="filter-card">
    <div class="filter-group">
        <label class="filter-label">Dosen</label>
        <select name="dosen_id" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($dosens as $d)
                <option value="{{ $d->id }}" {{ request('dosen_id') == $d->id ? 'selected' : '' }}>
                    {{ $d->user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Mata Kuliah</label>
        <select name="mata_kuliah_id" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($mataKuliahs as $mk)
                <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                    {{ $mk->kode }} - {{ $mk->nama }}
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

    <div class="filter-group">
        <label class="filter-label">Semester</label>
        <select name="semester_akademik" class="form-select filter-select">
            <option value="">Semua</option>
            <option value="Ganjil" {{ request('semester_akademik') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="Genap" {{ request('semester_akademik') == 'Genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Terapkan Filter</button>
        <a href="{{ route('pengampu.index') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
    </div>
</form>

<div class="table-container mt-5">

    <table class="data-table">

        <thead>

            <tr>

                <th>Dosen</th>
                <th>Mata Kuliah</th>
                <th>Tahun Akademik</th>
                <th>Semester</th>
                <th>Kelas</th>
                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($pengampus as $pengampu)

                <tr>

                    <td>
                        {{ $pengampu->dosen->user->name }}
                    </td>

                    <td>
                        {{ $pengampu->mataKuliah->kode }}
                        -
                        {{ $pengampu->mataKuliah->nama }}
                    </td>

                    <td>
                        {{ $pengampu->tahunAkademik->tahun }}
                    </td>

                    <td>
                        {{ $pengampu->semester_akademik }}
                    </td>

                    <td>
                        {{ $pengampu->kelas }}
                    </td>

                    <td>
                        <a href="{{ route('pengampu.lihat-kelas', $pengampu->id) }}"
                           class="btn btn-primary btn-sm"
                           style="margin-right: 0.25rem;">
                            Lihat Kelas
                        </a>
                        @unless(auth()->user()->isDirektur())
                        <x-confirm
                            action="{{ route('pengampu.destroy', $pengampu->id) }}"
                            method="DELETE"
                            title="Hapus Pengampu"
                            message="Hapus data pengampu ini?"
                            sub-message="Dosen akan dilepaskan dari mata kuliah ini."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endunless
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center">
                        Data pengampu belum tersedia.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>
<div class="mt-4">
    {{ $pengampus->withQueryString()->links() }}
</div>

@endsection
