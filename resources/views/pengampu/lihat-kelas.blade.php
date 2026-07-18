@extends('layouts.app')

@section('content')

<div class="flex-header">
    <h1 class="page-header" style="margin: 0;">Daftar Mahasiswa</h1>
    @if(auth()->user()->isDosen())
        <a href="{{ route('dosen.self.riwayat') }}" class="btn btn-secondary">&larr; Kembali</a>
    @else
        <a href="{{ route('pengampu.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    @endif
</div>

<x-alert type="success" :message="session('success')" />

<div class="card-info">
    <div class="card-info-row">
        <div class="card-info-item">
            <span class="card-info-label">Dosen</span>
            <span class="card-info-value">{{ $pengampu->dosen->user->name }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Mata Kuliah</span>
            <span class="card-info-value">{{ $pengampu->mataKuliah->kode }} - {{ $pengampu->mataKuliah->nama }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Tahun Akademik</span>
            <span class="card-info-value">{{ $pengampu->tahunAkademik->tahun }} {{ $pengampu->tahunAkademik->semester }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Semester</span>
            <span class="card-info-value">{{ $pengampu->semester_akademik }}</span>
        </div>
        <div class="card-info-item">
            <span class="card-info-label">Kelas</span>
            <span class="card-info-value">{{ $pengampu->kelas ?? '-' }}</span>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: {{ auth()->user()->isAdmin() ? '1fr 320px' : '1fr' }}; gap: 1.5rem; align-items: start;">

    {{-- MAIN: Daftar Mahasiswa di Kelas Ini --}}
    <div class="table-container" style="margin: 0;">

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                Mahasiswa di Kelas Ini
                <span style="font-weight: 400; color: #6b7280;">({{ $pengampu->mahasiswas->count() }})</span>
            </h3>
        </div>

        <table class="data-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Angkatan</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>

            <tbody>

                @forelse($pengampu->mahasiswas as $mahasiswa)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $mahasiswa->nim }}</td>
                        <td>{{ $mahasiswa->nama }}</td>
                        <td>{{ $mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                        <td>{{ $mahasiswa->status }}</td>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <x-confirm
                                action="{{ route('pengampu.kelas.mahasiswa.destroy', [$pengampu->id, $mahasiswa->id]) }}"
                                method="DELETE"
                                title="Hapus Mahasiswa"
                                message="Keluarkan {{ $mahasiswa->nama }} dari kelas ini?"
                                sub-message="Mahasiswa akan dilepaskan dari kelas {{ $pengampu->kelas ?? '-' }}."
                                buttonText="Hapus"
                                confirmText="Ya, Hapus"
                            />
                        </td>
                        @endif
                    </tr>

                @empty

                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="text-center">
                            Belum ada mahasiswa di kelas ini.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- SIDEBAR: Semua Mahasiswa --}}
    @if(auth()->user()->isAdmin())
    <div class="filter-card" style="margin: 0; position: sticky; top: 1rem;">

        <h3 style="margin: 0 0 0.75rem; font-size: 1rem; font-weight: 600;">
            Tambah Mahasiswa
            <span style="font-weight: 400; color: #6b7280;">({{ $semuaMahasiswa->count() }})</span>
        </h3>

        <input type="text" id="cariMahasiswa" placeholder="Cari NIM atau Nama..."
               style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8rem; margin-bottom: 0.75rem; box-sizing: border-box;">

        <div style="max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.375rem;">

            @forelse($semuaMahasiswa as $mahasiswa)

                <div class="sidebar-item" data-nim="{{ $mahasiswa->nim }}" data-nama="{{ strtolower($mahasiswa->nama) }}">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.8rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}
                        </div>
                        <div style="font-size: 0.7rem; color: #6b7280;">
                            {{ $mahasiswa->programStudi->nama_prodi ?? '-' }} | Angkatan {{ $mahasiswa->angkatan }}
                        </div>
                    </div>
                    <form action="{{ route('pengampu.kelas.mahasiswa.store', $pengampu->id) }}" method="POST" style="margin: 0; flex-shrink: 0;">
                        @csrf
                        <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.7rem; white-space: nowrap;">Tambah</button>
                    </form>
                </div>

            @empty

                <p style="text-align: center; color: #6b7280; font-size: 0.8rem; padding: 1rem 0;">
                    Semua mahasiswa sudah terdaftar.
                </p>

            @endforelse

        </div>

    </div>
    @endif

</div>

@push('scripts')
<script>
    document.getElementById('cariMahasiswa')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.sidebar-item').forEach(el => {
            const match = el.dataset.nim.toLowerCase().includes(q) || el.dataset.nama.includes(q);
            el.style.display = match ? '' : 'none';
        });
    });
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
.sidebar-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: border-color 0.15s;
}
.sidebar-item:hover {
    border-color: #3b82f6;
}
</style>

@endsection
