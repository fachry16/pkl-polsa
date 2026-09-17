@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Data Mata Kuliah
</h1>

@if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
<div class="mb-5 flex gap-2" style="align-items: center; flex-wrap: wrap;">

    <a href="{{ route('kurikulum.mata-kuliah.create', $kurikulum->id) }}"
       class="btn btn-primary">

        Tambah Mata Kuliah

    </a>

    <x-import-modal
        title="Import Data Mata Kuliah"
        :template-url="route('kurikulum.mata-kuliah.template-import', $kurikulum->id)"
        :action-url="route('kurikulum.mata-kuliah.import', $kurikulum->id)"
        :columns="[
            ['name' => 'kode', 'desc' => 'Kode Mata Kuliah unik.'],
            ['name' => 'nama', 'desc' => 'Nama Mata Kuliah.'],
            ['name' => 'sks_teori', 'desc' => 'Jumlah SKS teori (0-6).'],
            ['name' => 'sks_praktikum', 'desc' => 'Jumlah SKS praktikum (0-6).'],
            ['name' => 'semester', 'desc' => 'Semester penempatan (1-14).'],
            ['name' => 'jenis', 'desc' => 'Pilih salah satu: Wajib / Pilihan.'],
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
                <th>Kode MK</th>
                <th>Nama MK</th>
                <th>Semester</th>
                <th>Teori</th>
                <th>Praktik</th>
                <th>Total</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($mataKuliahs as $index => $mataKuliah)

            <tr>

                <td>
                    {{ $mataKuliahs->firstItem() + $index }}
                </td>

                <td>
                    {{ $mataKuliah->kode }}
                </td>

                <td>
                    {{ $mataKuliah->nama }}
                </td>

                <td>
                    {{ $mataKuliah->semester }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_teori }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_praktikum }}
                </td>

                <td class="text-center">
                    {{ $mataKuliah->sks_teori + $mataKuliah->sks_praktikum }}
                </td>

                <td>
                    {{ $mataKuliah->jenis }}
                </td>

                <td>

                    <div class="flex gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                        <a href="{{ route('kurikulum.mata-kuliah.edit', [$kurikulum->id, $mataKuliah->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('kurikulum.mata-kuliah.destroy', [$kurikulum->id, $mataKuliah->id]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus mata kuliah ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endif

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="10"
                    class="text-center">

                    Data mata kuliah belum tersedia.

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

    {{ $mataKuliahs->links() }}

</div>

@endsection
