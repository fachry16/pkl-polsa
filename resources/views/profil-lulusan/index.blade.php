@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Profil Lulusan
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<div class="mb-5" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">

    <a href="{{ route(
        'kurikulum.profil-lulusan.create',
        $kurikulum->id
    ) }}"
    class="btn btn-primary">

        Tambah Profil Lulusan

    </a>

    <x-import-modal
        title="Import Data Profil Lulusan"
        :template-url="route('kurikulum.profil-lulusan.template-import', $kurikulum->id)"
        :action-url="route('kurikulum.profil-lulusan.import', $kurikulum->id)"
        :columns="[
            ['name' => 'kode_pl', 'desc' => 'Kode Profil Lulusan unik.'],
            ['name' => 'nama_pl', 'desc' => 'Nama Profil Lulusan.'],
            ['name' => 'profesi', 'desc' => 'Profesi terkait (opsional, pisahkan dengan koma).'],
        ]"
    />

</div>
@endif

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
    <input type="search" name="cari" value="{{ request('cari') }}" class="form-input filter-select" placeholder="Cari kode / nama / profesi" style="flex: 1;" aria-label="Cari profil lulusan">
    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    @if(request('cari'))
        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
    @endif
</form>

<table class="detail-table">

    <thead>

        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Profesi</th>
            <th>Aksi</th>
        </tr>

    </thead>

    <tbody>

        @forelse($profilLulusans as $pl)

        <tr>

            <td>
                {{ $pl->kode_pl }}
            </td>

            <td>
                {{ $pl->nama_pl }}
            </td>

            <td>
                {{ $pl->profesi }}
            </td>

            <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <a href="{{ route('kurikulum.profil-lulusan.edit', [$kurikulum->id, $pl->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.profil-lulusan.destroy', [$kurikulum->id, $pl->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus profil lulusan ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif
                    </div>

                </td>

            </tr>

        @empty

        <tr>

            <td colspan="4" class="text-center">
                {{ request('cari') ? 'Tidak ada profil lulusan yang cocok dengan pencarian.' : 'Profil lulusan belum tersedia.' }}
            </td>

        </tr>

        @endforelse

    </tbody>

</table>
    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>
    </div>

@endsection
