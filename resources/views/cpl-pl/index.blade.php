@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Matriks CPL - PL
</h1>

<div x-data="{
    q: '',
    info: null,
    open(item, el) {
        if (this.info && this.info.item.key === item.key) {
            return this.close();
        }
        this.info = { item, rect: el.getBoundingClientRect() };
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

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode CPL" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari CPL">

<form method="POST"
      action="{{ route('kurikulum.cpl-pl.update', $kurikulum->id) }}">

    @csrf
    @method('PUT')

    <div class="table-container">

        <table class="data-table data-table-stack">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    @foreach($profilLulusans as $pl)

                    @php
                        $plDeskripsi = trim($pl->nama_pl.($pl->profesi ? ' ('.$pl->profesi.')' : ''));
                    @endphp

                    <th class="text-center">

                        <div>
                            {{ $pl->kode_pl }}

                            @if($plDeskripsi)
                                <button type="button"
                                        class="cpmk-info-btn"
                                        @click="open({ key: 'pl-{{ $pl->id }}', kode: {{ Js::from($pl->kode_pl) }}, deskripsi: {{ Js::from($plDeskripsi) }} }, $event.currentTarget)"
                                        :aria-expanded="info?.item.key === 'pl-{{ $pl->id }}'"
                                        title="Lihat profil lulusan"
                                        aria-label="Lihat profil lulusan {{ $pl->kode_pl }}"
                                        style="font-size: 0.7rem; font-weight: 700;">!</button>
                            @endif
                        </div>

                    </th>

                    @endforeach

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                <tr x-show="q === '' || $el.innerText.toLowerCase().includes(q.toLowerCase())">

                    <td>

                        <div class="font-medium">
                            {{ $cpl->kode_cpl }}

                            @if($cpl->deskripsi)
                                <button type="button"
                                        class="cpmk-info-btn"
                                        @click="open({ key: 'cpl-{{ $cpl->id }}', kode: {{ Js::from($cpl->kode_cpl) }}, deskripsi: {{ Js::from($cpl->deskripsi) }} }, $event.currentTarget)"
                                        :aria-expanded="info?.item.key === 'cpl-{{ $cpl->id }}'"
                                        title="Lihat deskripsi CPL"
                                        aria-label="Lihat deskripsi {{ $cpl->kode_cpl }}"
                                        style="font-size: 0.7rem; font-weight: 700;">!</button>
                            @endif
                        </div>

                    </td>

                    @foreach($profilLulusans as $pl)

                    <td class="text-center">

                        <input type="checkbox"
                               name="cpl[{{ $cpl->id }}][]"
                               value="{{ $pl->id }}"
                               {{ $cpl->profilLulusans->contains($pl->id) ? 'checked' : '' }}
                               {{ (auth()->user()->isAdmin() || auth()->user()->isKaprodi()) ? '' : 'disabled' }}>

                    </td>

                    @endforeach

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
    <button type="submit"
            class="btn btn-primary mt-5">

        Simpan Matriks

    </button>
    @endif

</form>

<template x-teleport="body">
    <div x-show="info"
         x-ref="popover"
         x-cloak
         class="cpmk-info-popover"
         style="position: fixed; display: none;"
         @click.outside="close()"
         @keydown.escape.window="close()">
        <div class="cpmk-info-kode" x-text="info?.item.kode"></div>
        <div class="cpmk-info-deskripsi" x-text="info?.item.deskripsi"></div>
    </div>
</template>

<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

        Kembali

    </a>
</div>

</div>

@endsection
