@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data CPL
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<div class="mb-5" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">

    <a href="{{ route('kurikulum.cpl.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah CPL

    </a>

    <x-import-modal
        title="Import Data CPL"
        :template-url="route('kurikulum.cpl.template-import', $kurikulum->id)"
        :action-url="route('kurikulum.cpl.import', $kurikulum->id)"
        :columns="[
            ['name' => 'kode_cpl', 'desc' => 'Kode CPL unik.'],
            ['name' => 'deskripsi', 'desc' => 'Deskripsi capaian pembelajaran.'],
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
                <th>Kode CPL</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($cpls as $index => $cpl)

            <tr>

                <td>
                    {{ $cpls->firstItem() + $index }}
                </td>

                <td>
                    {{ $cpl->kode_cpl }}
                </td>

                <td>
                    {{ $cpl->deskripsi }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <a href="{{ route('kurikulum.cpl.edit', [$kurikulum->id, $cpl->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.cpl.destroy', [$kurikulum->id, $cpl->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus CPL ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="4"
                    class="text-center">

                    Data CPL belum tersedia.

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

    {{ $cpls->links() }}

</div>

@endsection
