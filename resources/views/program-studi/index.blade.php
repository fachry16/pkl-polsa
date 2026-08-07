@extends('layouts.app')

@section('content')

    <div class="page-header">
        Data Program Studi
    </div>

    @unless(auth()->user()->isDirektur())
    <div class="mb-4">
        <a href="{{ route('program-studi.create') }}" class="btn btn-primary">
            Tambah Program Studi
        </a>
    </div>
    @endunless

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<x-alert type="error" :message="session('error')" />

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Prodi</th>
                    <th>Nama Prodi</th>
                    <th>Jenjang</th>
                    <th>Akreditasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programStudis as $index => $prodi)
                <tr>
                    <td>{{ $programStudis->firstItem() + $index }}</td>
                    <td>{{ $prodi->kode_prodi }}</td>
                    <td>{{ $prodi->nama_prodi }}</td>
                    <td>{{ $prodi->jenjang }}</td>
                    <td>
                        @if($prodi->akreditasi == 'Baik')
                            <span class="badge badge-draft">Baik</span>
                        @elseif($prodi->akreditasi == 'Baik Sekali')
                            <span class="badge badge-revisi">Baik Sekali</span>
                        @else
                            <span class="badge badge-disetujui">Unggul</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('program-studi.kurikulum', $prodi->id) }}" class="btn btn-sm btn-primary">
                                Cek Kurikulum
                            </a>
                            @unless(auth()->user()->isDirektur())
                            <a href="{{ route('program-studi.edit', $prodi->id) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            <x-confirm
                                action="{{ route('program-studi.destroy', $prodi->id) }}"
                                method="DELETE"
                                title="Hapus Program Studi"
                                message="Hapus program studi ini?"
                                sub-message="Semua data kurikulum dan mahasiswa terkait akan ikut terhapus."
                                buttonText="Hapus"
                                confirmText="Ya, Hapus"
                            />
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">Data program studi belum tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $programStudis->links() }}
    </div>

@endsection
