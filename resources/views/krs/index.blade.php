@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Kartu Rencana Studi (KRS)
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<a href="{{ route('krs.create') }}" class="btn btn-primary">
    Tambah KRS
</a>
@endif
<a href="{{ route('krs.cetak-pilih') }}" class="btn btn-secondary">
    Cetak KRS
</a>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table data-table-stack">

        <thead>

            <tr>

                <th>Prodi</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Tahun Akademik</th>
                <th>Kelas</th>
                <th>Jml Mahasiswa</th>
                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($krsList as $krs)

                <tr>

                    <td data-label="Prodi">
                        <span class="badge badge-draft" style="background: #f1f5f9; color: #475569;">{{ $krs->programStudi->kode_prodi ?? '-' }}</span>
                    </td>

                    <td data-label="Mata Kuliah">
                        <strong style="color: #0f172a;">{{ $krs->mataKuliah->nama }}</strong>
                        <div style="font-size: 0.72rem; color: #64748b;">{{ $krs->mataKuliah->kode }}</div>
                    </td>

                    <td data-label="Dosen">
                        {{ $krs->dosen->user->name ?? '-' }}
                    </td>

                    <td data-label="Tahun Akademik">
                        {{ $krs->tahunAkademik->tahun }} {{ $krs->tahunAkademik->semester }}
                    </td>

                    <td data-label="Kelas">
                        <strong>{{ $krs->kelas }}</strong>
                    </td>

                    <td data-label="Jml Mahasiswa">
                        {{ $krs->mahasiswas->count() }} Mahasiswa
                    </td>

                    <td data-label="Aksi" class="aksi-cell">
                        <a href="{{ route('krs.show', $krs->id) }}"
                           class="btn btn-primary btn-sm">
                            Kelola Mahasiswa
                        </a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <form action="{{ route('krs.destroy', $krs->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus data KRS ini? Data terkait di Pengampu juga akan dihapus.')">
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
                        Belum ada data KRS. Klik "Tambah KRS" untuk membuat kelas baru.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>
<div class="mt-4">
    {{ $krsList->withQueryString()->links() }}
</div>

<style>
.aksi-cell {
    white-space: nowrap;
}
.aksi-cell .btn {
    margin-right: 0.25rem;
}
.aksi-cell .btn:last-child {
    margin-right: 0;
}
</style>

@endsection
