@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Bahan Kajian
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<div class="mb-5" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">

    <a href="{{ route('kurikulum.bahan-kajian.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah Bahan Kajian

    </a>

    <x-import-modal
        title="Import Data Bahan Kajian"
        :template-url="route('kurikulum.bahan-kajian.template-import', $kurikulum->id)"
        :action-url="route('kurikulum.bahan-kajian.import', $kurikulum->id)"
        :columns="[
            ['name' => 'kode_bk', 'desc' => 'Kode Bahan Kajian unik.'],
            ['name' => 'nama_bk', 'desc' => 'Nama Bahan Kajian.'],
            ['name' => 'deskripsi', 'desc' => 'Deskripsi bahan kajian.'],
            ['name' => 'referensi', 'desc' => 'Referensi terkait (opsional).'],
        ]"
    />

</div>
@endif

<x-alert type="success" :message="session('success')" />

@if(session('import_warnings'))
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.8rem; color: #92400e;">
        <div style="font-weight: 700; margin-bottom: 0.35rem;">Catatan Baris yang Dilewati:</div>
        <ul style="margin: 0; padding-left: 1.2rem; line-height: 1.5;">
            @foreach(session('import_warnings') as $warn)
                <li>{{ $warn }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="GET" action="{{ url()->current() }}" style="display: flex; gap: 0.5rem; align-items: center; max-width: 420px; margin-bottom: 1rem;">
    <input type="search" name="cari" value="{{ request('cari') }}" class="form-input filter-select" placeholder="Cari kode / nama / deskripsi" style="flex: 1;" aria-label="Cari bahan kajian">
    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    @if(request('cari'))
        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
    @endif
</form>

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode BK</th>
                <th>Nama BK</th>
                <th>Deskripsi</th>
                <th>Referensi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($bahanKajians as $index => $bahanKajian)

            <tr>

                <td>
                    {{ $bahanKajians->firstItem() + $index }}
                </td>

                <td>
                    {{ $bahanKajian->kode_bk }}
                </td>

                <td>
                    {{ $bahanKajian->nama_bk }}
                </td>

                <td>
                    {{ $bahanKajian->deskripsi ?? '-' }}
                </td>

                <td>
                    {{ $bahanKajian->referensi ?? '-' }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <a href="{{ route('kurikulum.bahan-kajian.edit', [$kurikulum->id, $bahanKajian->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.bahan-kajian.destroy', [$kurikulum->id, $bahanKajian->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus bahan kajian ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6"
                    class="text-center">

                    {{ request('cari') ? 'Tidak ada bahan kajian yang cocok dengan pencarian.' : 'Data bahan kajian belum tersedia.' }}

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>
    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>
    </div>

<div class="mt-5">

    {{ $bahanKajians->links() }}

</div>

@endsection
