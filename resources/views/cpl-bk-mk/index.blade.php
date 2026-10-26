@extends('layouts.app')

@section('content')

<h1 class="page-header">
    CPL - BK - MK
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

<x-alert type="success" :message="session('success')" />

<div x-data="{ q: '' }">

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode / nama MK" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari mata kuliah">

<div class="table-container">

    <table class="data-table data-table-stack">

        <thead>

            <tr>

                <th>
                    Kode MK
                </th>

                <th>
                    Nama MK
                </th>

                @foreach($cpls as $cpl)

                    <th class="text-center">

                        <div>{{ $cpl->kode_cpl }}</div>

                        @if($cpl->deskripsi)
                        <div class="table-sub">{{ $cpl->deskripsi }}</div>
                        @endif

                    </th>

                @endforeach

                <th class="text-center">
                    Jumlah BK
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($mataKuliahs as $mk)

                <tr x-show="q === '' || $el.innerText.toLowerCase().includes(q.toLowerCase())">

                    <td class="font-medium">
                        {{ $mk->kode }}
                    </td>

                    <td>
                        {{ $mk->nama }}
                    </td>

                    @foreach($cpls as $cpl)

                        <td class="text-center">

                            @if($mk->cpls->contains('id', $cpl->id))

                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>

                                <span class="sr-only">Terpenuhi</span>

                            @else

                                <span class="sr-only">Belum terpenuhi</span>

                            @endif

                        </td>

                    @endforeach

                    <td class="text-center">
                        {{ $mk->bahanKajians->count() }}
                    </td>

                </tr>

            @empty

                <tr class="empty-row">
                    <td colspan="{{ 3 + $cpls->count() }}">
                        Belum ada mata kuliah.
                    </td>
                </tr>

            @endforelse

        </tbody>

        <tfoot>

            <tr>

                <th colspan="2" scope="row">
                    Jumlah BK per CPL
                </th>

                @foreach($cpls as $cpl)
                    <td class="text-center">{{ $cpl->jumlahBk }}</td>
                @endforeach

                <td class="text-center">—</td>

            </tr>

            <tr>

                <th colspan="2" scope="row">
                    Jumlah MK per CPL
                </th>

                @foreach($cpls as $cpl)
                    <td class="text-center">{{ $cpl->jumlahMk }}</td>
                @endforeach

                <td class="text-center">—</td>

            </tr>

        </tfoot>

    </table>

</div>

</div>

<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
</div>

@endsection
