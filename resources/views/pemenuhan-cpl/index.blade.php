@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Pemenuhan CPL
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

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

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode CPL" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari CPL">

<div class="table-container">

    <table class="data-table data-table-stack">

        <thead>

            <tr>

                <th>
                    Kode CPL
                </th>

                @for($semester = 1; $semester <= 8; $semester++)

                    <th class="text-center">
                        Smt {{ $semester }}
                    </th>

                @endfor

            </tr>

        </thead>

        <tbody>

            @forelse($cpls as $cpl)

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

                    @for($semester = 1; $semester <= 8; $semester++)

                        <td>

                            @php $kodes = $matrix[$cpl->id][$semester] ?? []; @endphp

                            @if($kodes)

                                <div class="flex flex-wrap gap-2">

                                    @foreach($kodes as $kode)
                                        <span class="cpmk-chip">{{ $kode }}</span>
                                    @endforeach

                                </div>

                            @else

                                <span style="color: #cbd5e1;">—</span>

                            @endif

                        </td>

                    @endfor

                </tr>

            @empty

                <tr class="empty-row">
                    <td colspan="9">
                        Belum ada CPL.
                    </td>
                </tr>

            @endforelse

        </tbody>

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

<div class="mt-5">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

            Kembali

    </a>

</div>

@endsection
