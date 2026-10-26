@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Dosen Pengampu
</h1>

@if(auth()->user()->isAdmin())
<a href="{{ route('pengampu.create') }}"
   class="btn btn-primary">

    Tambah Pengampu

</a>
@endif

<x-alert type="success" :message="session('success')" />

<form method="GET" class="filter-card">
    <div class="filter-group">
        <label class="filter-label" for="pengampu-filter-dosen">Dosen</label>
        <x-searchable-select
            id="pengampu-filter-dosen"
            name="dosen_id"
            :options="$dosens->map(fn ($d) => ['value' => $d->id, 'label' => $d->user->name, 'search' => $d->nidn])"
            :value="request('dosen_id')"
            placeholder="Semua dosen"
            input-class="form-select filter-select search-select-input"
            allow-empty
            auto-submit />
    </div>

    <div class="filter-group">
        <label class="filter-label" for="pengampu-filter-mk">Mata Kuliah</label>
        <x-searchable-select
            id="pengampu-filter-mk"
            name="mata_kuliah_id"
            :options="$mataKuliahs->map(fn ($mk) => ['value' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama])"
            :value="request('mata_kuliah_id')"
            placeholder="Semua mata kuliah"
            input-class="form-select filter-select search-select-input"
            allow-empty
            auto-submit />
    </div>

    <div class="filter-group">
        <label class="filter-label" for="pengampu-filter-ta">Tahun Akademik</label>
        <x-searchable-select
            id="pengampu-filter-ta"
            name="tahun_akademik_id"
            :options="$tahunAkademiks->map(fn ($ta) => ['value' => $ta->id, 'label' => $ta->tahun.' '.$ta->semester])"
            :value="request('tahun_akademik_id')"
            placeholder="Semua tahun akademik"
            input-class="form-select filter-select search-select-input"
            allow-empty
            auto-submit />
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

        <table class="data-table data-table-stack">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Dosen</th>
                    <th>Mata Kuliah</th>
                    <th>Tahun Akademik</th>
                    <th>Semester</th>
                    <th>Kelas</th>
                    <th>Aksi</th>

                </tr>

            </thead>

        <tbody>

            @forelse($pengampus as $index => $pengampu)

                <tr>

                    <td data-label="No">{{ $pengampus->firstItem() + $index }}</td>

                    <td data-label="Dosen">
                    {{ $pengampu->dosen->user->name }}
                </td>

                <td data-label="Mata Kuliah">
                    {{ $pengampu->mataKuliah->kode }}
                    -
                    {{ $pengampu->mataKuliah->nama }}
                </td>

                <td data-label="Tahun Akademik">
                    {{ $pengampu->tahunAkademik->tahun }}
                </td>

                <td data-label="Semester">
                    {{ $pengampu->semester_akademik }}
                </td>

                <td data-label="Kelas">
                    {{ $pengampu->kelas }}
                </td>

                <td data-label="Aksi">
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
                <td colspan="7" data-label="Informasi" class="text-center">
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
