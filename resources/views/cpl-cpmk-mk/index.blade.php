@extends('layouts.app')

@section('content')

<h1 class="page-header">
    CPL - CPMK
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

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode / deskripsi CPL atau CPMK" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari data">

<form method="POST"
      action="{{ route('kurikulum.cpl-cpmk-mk.store', $kurikulum->id) }}">

    @csrf

    <div class="table-container">

        <table class="data-table data-table-stack">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    <th>
                        Kode CPMK
                    </th>

                    <th>
                        Deskripsi CPMK
                    </th>

                    <th>
                        Semester
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                    @foreach($cpmks as $cpmk)

                        <tr x-show="q === '' || $el.innerText.toLowerCase().includes(q.toLowerCase())">

                            <td class="font-medium">

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

                            </td>

                            <td>
                                {{ $cpmk->kode_cpmk }}
                            </td>

                            <td>
                                {{ $cpmk->deskripsi }}
                            </td>

                            <td>

                                <div class="flex flex-wrap gap-2">

                                    @for($semester = 1; $semester <= 8; $semester++)

                                        @php
                                            $key = $cpl->id . '-' . $cpmk->id . '-' . $semester;
                                        @endphp

                                        <label class="flex items-center gap-2">

                                            <input type="checkbox"
                                                   name="mapping[{{ $key }}]"
                                                   value="1"
                                                   {{ isset($checked[$key]) ? 'checked' : '' }}
                                                   {{ (auth()->user()->isAdmin() || auth()->user()->isKaprodi()) ? '' : 'disabled' }}>

                                            <span>
                                                {{ $semester }}
                                            </span>

                                        </label>

                                    @endfor

                                </div>

                            </td>

                        </tr>

                    @endforeach

                @endforeach

            </tbody>

        </table>

    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
    <div class="flex gap-2 mt-5">

        <button class="btn btn-success">

            Simpan

        </button>

    </div>
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
        <div class="cpmk-info-kode" x-text="info?.cpl.kode_cpl"></div>
        <div class="cpmk-info-deskripsi" x-text="info?.cpl.deskripsi"></div>
    </div>
</template>

</div>
<div class="mt-5">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

            Kembali

    </a>

</div>

@endsection