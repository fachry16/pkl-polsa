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

<x-alert type="success" :message="session('success')" />

<div class="table-container mt-5">

    <table class="data-table">

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

                    <td>
                        {{ $krs->programStudi->kode_prodi ?? '-' }}
                    </td>

                    <td>
                        {{ $krs->mataKuliah->kode }}
                        -
                        {{ $krs->mataKuliah->nama }}
                    </td>

                    <td>
                        {{ $krs->dosen->user->name ?? '-' }}
                    </td>

                    <td>
                        {{ $krs->tahunAkademik->tahun }}
                        {{ $krs->tahunAkademik->semester }}
                    </td>

                    <td>
                        {{ $krs->kelas }}
                    </td>

                    <td>
                        {{ $krs->mahasiswas->count() }} Mahasiswa
                    </td>

                    <td class="aksi-cell">
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
