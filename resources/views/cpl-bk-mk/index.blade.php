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

<div x-data="{
    q: '',
    info: null,
    open(cpl, el) {
        if (this.info && this.info.cpl.id === cpl.id) {
            return this.close();
        }
        this.info = { cpl, rect: el.getBoundingClientRect() };
        this.$nextTick(() => this.position());
    },
    close() {
        this.info = null;
    },
    position() {
        const pop = this.$refs.popover;
        if (!pop || !this.info) {
            return;
        }
        const rect = this.info.rect;
        let left = rect.left;
        let top = rect.bottom + 8;
        if (left + pop.offsetWidth > window.innerWidth - 8) {
            left = Math.max(8, window.innerWidth - pop.offsetWidth - 8);
        }
        if (top + pop.offsetHeight > window.innerHeight - 8) {
            top = Math.max(8, rect.top - pop.offsetHeight - 8);
        }
        pop.style.left = left + 'px';
        pop.style.top = top + 'px';
    }
}">

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode / bahan kajian" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari bahan kajian">

<div class="table-container">

    <table class="data-table data-table-stack">

        <thead>

            <tr>

                <th>
                    Kode BK
                </th>

                <th>
                    Bahan Kajian
                </th>

                @foreach($cpls as $cpl)

                    <th class="text-center">

                        <div>
                            {{ $cpl->kode_cpl }}

                            @if($cpl->deskripsi)
                                <button type="button"
                                        class="cpmk-info-btn"
                                        @click="open({ id: {{ $cpl->id }}, kode_cpl: {{ Js::from($cpl->kode_cpl) }}, deskripsi: {{ Js::from($cpl->deskripsi) }} }, $event.currentTarget)"
                                        :aria-expanded="info?.cpl.id === {{ $cpl->id }}"
                                        title="Lihat deskripsi CPL"
                                        aria-label="Lihat deskripsi {{ $cpl->kode_cpl }}"
                                        style="font-size: 0.7rem; font-weight: 700;">!</button>
                            @endif
                        </div>

                    </th>

                @endforeach

                <th class="text-center">
                    Jumlah MK
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($bahanKajians as $bk)

                <tr x-show="q === '' || $el.innerText.toLowerCase().includes(q.toLowerCase())">

                    <td class="font-medium">
                        {{ $bk->kode_bk }}
                    </td>

                    <td>
                        {{ $bk->nama_bk }}
                    </td>

                    @foreach($cpls as $cpl)

                        <td class="text-center">

                            @if($bk->cpls->contains('id', $cpl->id))

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
                        {{ $bk->mataKuliahs->count() }}
                    </td>

                </tr>

            @empty

                <tr class="empty-row">
                    <td colspan="{{ 3 + $cpls->count() }}">
                        Belum ada bahan kajian.
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

<template x-teleport="body">
    <div x-show="info"
         x-ref="popover"
         x-cloak
         class="cpmk-info-popover"
         style="position: fixed; display: none;"
         @click.outside="close()"
         @keydown.escape.window="close()">
        <div class="cpmk-info-kode" x-text="info?.cpl.kode_cpl"></div>
        <div class="cpmk-info-deskripsi" x-text="info?.cpl.deskripsi"></div>
    </div>
</template>

</div>

<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
</div>

@endsection
