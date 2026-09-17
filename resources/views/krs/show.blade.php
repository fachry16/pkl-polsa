@extends('layouts.app')

@section('content')

<div class="flex-header">
    <h1 class="page-header" style="margin: 0;">Kelola Mahasiswa</h1>
    <a href="{{ route('krs.index') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="card-info">
    <div class="card-info-row">
        <div class="card-info-item">
            <span class="card-info-label">Program Studi</span>
            <span class="card-info-value">{{ $krs->programStudi->nama_prodi ?? '-' }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Mata Kuliah</span>
            <span class="card-info-value">{{ $krs->mataKuliah->kode }} - {{ $krs->mataKuliah->nama }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Dosen</span>
            <span class="card-info-value">{{ $krs->dosen->user->name ?? '-' }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Tahun Akademik</span>
            <span class="card-info-value">{{ $krs->tahunAkademik->tahun }} {{ $krs->tahunAkademik->semester }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Kelas</span>
            <span class="card-info-value">{{ $krs->kelas }}</span>
        </div>
    </div>
</div>

<div class="krs-layout">

    <div class="table-container" style="margin: 0;">

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                Mahasiswa di Kelas Ini
                <span style="font-weight: 400; color: #6b7280;">({{ $krs->mahasiswas->count() }})</span>
            </h3>
        </div>

        <table class="data-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Program</th>
                    <th>Angkatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($krs->mahasiswas as $mahasiswa)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $mahasiswa->nim }}</td>
                        <td>{{ $mahasiswa->nama }}</td>
                        <td>{{ $mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
                        <td>
                            @if(($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan')
                                <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 700;">Karyawan</span>
                            @else
                                <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 700;">Reguler</span>
                            @endif
                        </td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                        <td>
                            @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                            <form action="{{ route('krs.mahasiswa.destroy', [$krs->id, $mahasiswa->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Keluarkan {{ $mahasiswa->nama }} dari kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center">
                            Belum ada mahasiswa di kelas ini.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
    <div class="krs-sidebar" x-data="krsSidebar()">

        <h3 style="margin: 0 0 0.75rem; font-size: 1rem; font-weight: 600;">
            Tambah Mahasiswa
            <span style="font-weight: 400; color: #6b7280;">({{ $semuaMahasiswa->count() }})</span>
        </h3>

        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.75rem;">
            Data mahasiswa dari prodi <strong>{{ $krs->programStudi->kode_prodi ?? '' }}</strong>.
        </p>

        {{-- Filter --}}
        <div style="display: flex; gap: 0.4rem; margin-bottom: 0.5rem;">
            <select id="filterKelas" class="krs-search" style="width: 50%; margin-bottom: 0;" x-model="filterKelas" x-on:change="applyFilter()">
                <option value="">Semua Kelas</option>
                <option value="Reguler">Reguler</option>
                <option value="Karyawan">Karyawan</option>
            </select>
            <select id="filterAngkatan" class="krs-search" style="width: 50%; margin-bottom: 0;" x-model="filterAngkatan" x-on:change="applyFilter()">
                <option value="">Semua Angkatan</option>
                @foreach($semuaMahasiswa->pluck('angkatan')->unique()->sort()->values() as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <input type="text" id="cariMahasiswa" placeholder="Cari NIM atau Nama..."
               class="krs-search" x-model="search" x-on:input="applyFilter()">

        <form method="POST" action="{{ route('krs.mahasiswa.store', $krs->id) }}" id="bulkAddForm">
            @csrf

            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #e5e7eb; margin-bottom: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; color: #374151; cursor: pointer; user-select: none;">
                    <input type="checkbox" x-model="selectAll" x-on:change="toggleAll()" style="accent-color: #4f46e5;">
                    <span>Pilih Semua (<span x-text="visibleCount"></span>)</span>
                </label>
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.65rem; font-size: 0.72rem;" x-show="selectedIds.length > 0" x-text="'Tambah (' + selectedIds.length + ')'"></button>
            </div>

            <div class="krs-mahasiswa-list">
                @forelse($semuaMahasiswa as $mahasiswa)
                    <div class="krs-mahasiswa-item"
                         data-nim="{{ $mahasiswa->nim }}"
                         data-nama="{{ strtolower($mahasiswa->nama) }}"
                         data-kelas="{{ $mahasiswa->jenis_kelas ?? 'Reguler' }}"
                         data-angkatan="{{ $mahasiswa->angkatan }}">
                        <input type="checkbox"
                               name="mahasiswa_id[]"
                               value="{{ $mahasiswa->id }}"
                               x-model="selectedIds"
                               style="accent-color: #4f46e5; flex-shrink: 0; width: 15px; height: 15px;">
                        <div style="flex: 1; min-width: 0;">
                            <div class="krs-mahasiswa-nama">
                                {{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}
                            </div>
                            <div class="krs-mahasiswa-info" style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.15rem;">
                                <span>Angk. {{ $mahasiswa->angkatan }}</span>
                                <span>&bull;</span>
                                <span style="font-weight: 600; color: {{ ($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan' ? '#d97706' : '#2563eb' }};">
                                    {{ $mahasiswa->jenis_kelas ?? 'Reguler' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: #6b7280; font-size: 0.8rem; padding: 1rem 0;">
                        Semua mahasiswa sudah terdaftar.
                    </p>
                @endforelse
            </div>

        </form>

    </div>
    @endif

</div>

@push('scripts')
<script>
window.krsSidebar = function () {
    return {
        filterKelas: '',
        filterAngkatan: '',
        search: '',
        selectAll: false,
        selectedIds: [],
        visibleCount: 0,

        init() {
            this.applyFilter();
        },

        applyFilter() {
            const q = this.search.toLowerCase();
            const items = document.querySelectorAll('.krs-mahasiswa-item');
            let count = 0;
            items.forEach(el => {
                const matchSearch = !q || el.dataset.nim.toLowerCase().includes(q) || el.dataset.nama.includes(q);
                const matchKelas = !this.filterKelas || el.dataset.kelas === this.filterKelas;
                const matchAngkatan = !this.filterAngkatan || el.dataset.angkatan === this.filterAngkatan;
                const visible = matchSearch && matchKelas && matchAngkatan;
                el.style.display = visible ? '' : 'none';
                if (visible) count++;
            });
            this.visibleCount = count;
            const allVisible = this.getAllVisibleIds();
            this.selectAll = allVisible.length > 0 && allVisible.every(id => this.selectedIds.includes(id));
        },

        getAllVisibleIds() {
            const ids = [];
            document.querySelectorAll('.krs-mahasiswa-item').forEach(el => {
                if (el.style.display !== 'none') {
                    ids.push(el.querySelector('input[type="checkbox"]').value);
                }
            });
            return ids;
        },

        toggleAll() {
            const allVisible = this.getAllVisibleIds();
            if (this.selectAll) {
                allVisible.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !allVisible.includes(id));
            }
        }
    };
};
</script>
@endpush

<style>
.flex-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}
.card-info {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}
.card-info-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem 2.5rem;
}
.card-info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.1rem;
    min-width: 0;
}
.card-info-label {
    font-size: 0.65rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.card-info-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: #1e293b;
    word-break: break-word;
}
.krs-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 768px) {
    .krs-layout {
        grid-template-columns: 1fr;
    }
}
.krs-sidebar {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    position: sticky;
    top: 1rem;
}
.krs-search {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
    box-sizing: border-box;
}
.krs-mahasiswa-list {
    max-height: 400px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}
.krs-mahasiswa-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: border-color 0.15s;
}
.krs-mahasiswa-item:hover {
    border-color: #3b82f6;
}
.krs-mahasiswa-nama {
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.krs-mahasiswa-info {
    font-size: 0.7rem;
    color: #6b7280;
}
</style>

@endsection
