@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data CPMK
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<div class="mb-5" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">

    <a href="{{ route('kurikulum.cpmk.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah CPMK

    </a>

    <x-import-modal
        title="Import Data CPMK"
        :template-url="route('kurikulum.cpmk.template-import', $kurikulum->id)"
        :action-url="route('kurikulum.cpmk.import', $kurikulum->id)"
        :columns="[
            ['name' => 'kode_cpmk', 'desc' => 'Kode CPMK unik.'],
            ['name' => 'deskripsi', 'desc' => 'Deskripsi sub-capaian pembelajaran.'],
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

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Kode CPMK</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($cpmks as $index => $cpmk)

            <tr>

                <td>
                    {{ $cpmks->firstItem() + $index }}
                </td>

                <td>
                    {{ $cpmk->kode_cpmk }}
                </td>

                <td>
                    {{ $cpmk->deskripsi }}
                </td>
                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <a href="{{ route('kurikulum.cpmk.edit', [$kurikulum->id, $cpmk->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.cpmk.destroy', [$kurikulum->id, $cpmk->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus CPMK ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center">

                    Data CPMK belum tersedia.

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

    {{ $cpmks->links() }}

</div>

@endsection
