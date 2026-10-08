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

<form method="GET" action="{{ url()->current() }}" style="display: flex; gap: 0.5rem; align-items: center; max-width: 420px; margin-bottom: 1rem;">
    <input type="search" name="cari" value="{{ request('cari') }}" class="form-input filter-select" placeholder="Cari kode / nama MK" style="flex: 1;" aria-label="Cari mata kuliah">
    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    @if(request('cari'))
        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
    @endif
</form>

@if($mataKuliahs->isEmpty())

<div class="table-container">

    <table class="data-table">

        <tbody>

            <tr>

                <td colspan="8"
                    class="text-center">

                    {{ request('cari') ? 'Tidak ada mata kuliah yang cocok dengan pencarian.' : 'Data mata kuliah belum tersedia.' }}

                </td>

            </tr>

        </tbody>

    </table>

</div>

@else

    @foreach($mataKuliahs->groupBy('semester')->sortKeys() as $semester => $items)

    <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 1.5rem 0 0.6rem;">
        Semester {{ $semester }}
    </h2>

    <div class="table-container">

        <table class="data-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Kode MK</th>
                    <th>Nama MK</th>
                    <th>Teori</th>
                    <th>Praktik</th>
                    <th>Total</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @foreach($items as $mataKuliah)

                <tr>

                    <td>
                        {{ $loop->index + 1 }}
                    </td>

                    <td>
                        {{ $mataKuliah->kode }}
                    </td>

                    <td>
                        {{ $mataKuliah->nama }}
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

                @endforeach

            </tbody>

        </table>

    </div>

    @endforeach

@endif

<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

        Kembali

    </a>
</div>

@endsection
