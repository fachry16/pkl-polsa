@extends('layouts.app')

@section('content')

@php
    $editable = auth()->user()->isAdmin() || auth()->user()->isKaprodi();
    $cpmksJson = Js::from($cpmks->map(fn ($c) => [
        'id' => $c->id,
        'kode_cpmk' => $c->kode_cpmk,
        'deskripsi' => $c->deskripsi,
    ])->values()->all());
    $selectedJson = Js::from($mataKuliahs->mapWithKeys(fn ($mk) => [
        $mk->id => $mk->cpmks->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
    ])->all());
    $mkLabelsJson = Js::from($mataKuliahs->mapWithKeys(fn ($mk) => [$mk->id => $mk->kode])->all());
@endphp

<div x-data="mkCpmkPicker({{ $cpmksJson }}, {{ $selectedJson }}, {{ $mkLabelsJson }})">

    <h1 class="page-header">
        Matriks MK - CPMK
    </h1>

    <p class="mb-5">
        {{ $kurikulum->nama_kurikulum }}
        -
        {{ $kurikulum->programStudi->nama_prodi }}
    </p>

    <x-alert type="success" :message="session('success')" />

    <form method="POST"
          action="{{ route('kurikulum.mk-cpmk.update', $kurikulum->id) }}">
        @csrf
        @method('PUT')

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th>CPMK</th>
                        @if($editable)
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($mataKuliahs as $mataKuliah)
                    <tr>
                        <td class="font-medium">{{ $mataKuliah->kode }}</td>
                        <td>{{ $mataKuliah->nama }}</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="id in sel({{ $mataKuliah->id }})" :key="'chip'+id">
                                    <span class="cpmk-chip">
                                        <span x-text="kodeOf(id).kode_cpmk"></span>
                                        <button type="button"
                                                class="cpmk-info-btn"
                                                title="Lihat deskripsi CPMK"
                                                @click.prevent.stop="open(kodeOf(id), $event.currentTarget)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                        </button>
                                    </span>
                                </template>

                                @if($editable)
                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        x-show="! hasSel({{ $mataKuliah->id }})"
                                        @click="openModal({{ $mataKuliah->id }}, $event)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Tambah
                                </button>
                                @else
                                <span x-show="! hasSel({{ $mataKuliah->id }})"
                                      style="font-size: 0.75rem; color: #94a3b8; line-height: 1.8;">
                                    Belum ada CPMK
                                </span>
                                @endif
                            </div>

                            <template x-for="id in sel({{ $mataKuliah->id }})" :key="'hid'+id">
                                <input type="checkbox"
                                       class="sr-only"
                                       :name="'mataKuliah[{{ $mataKuliah->id }}][]'"
                                       :value="id"
                                       checked>
                            </template>
                        </td>
                        @if($editable)
                        <td>
                            <button type="button"
                                    class="btn btn-secondary btn-sm"
                                    @click="openModal({{ $mataKuliah->id }}, $event)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                Edit
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($editable)
        <button type="submit" class="btn btn-primary mt-5">Simpan Matriks</button>
        @endif
    </form>

    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">Kembali</a>
    </div>

    <template x-teleport="body">
        <div x-show="modalMkId !== null"
             class="modal-overlay"
             style="display: none;"
             @keydown.escape.window="closeModal()">
            <div class="modal-backdrop" @click="closeModal()">
                <div class="modal-backdrop-bg"></div>
            </div>

            <div x-show="modalMkId !== null"
                 x-ref="modalPanel"
                 class="modal-content"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="mk-cpmk-modal-title"
                 style="padding: 1.25rem;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.9rem;">
                    <div>
                        <div id="mk-cpmk-modal-title" style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Pilih CPMK Mata Kuliah</div>
                        <div x-show="mkLabel(modalMkId)"
                             style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;"
                             x-text="'Kode MK: ' + mkLabel(modalMkId)"></div>
                    </div>
                    <button type="button"
                            class="cpmk-info-btn"
                            style="flex-shrink: 0;"
                            title="Tutup"
                            @click="closeModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div x-show="cpmks.length === 0" style="font-size: 0.8rem; color: #94a3b8; padding: 1rem 0;">
                    Belum ada CPMK di kurikulum ini.
                </div>

                <div x-show="cpmks.length > 0" class="flex flex-wrap gap-2" style="max-height: 50vh; overflow-y: auto;">
                    <template x-for="c in cpmks" :key="'mod'+c.id">
                        <span class="cpmk-chip" style="cursor: pointer;">
                            <label class="cpmk-chip-label">
                                <input class="sr-only"
                                       type="checkbox"
                                       :checked="draft.includes(c.id)"
                                       @change="toggle(c.id)">
                                <svg x-show="draft.includes(c.id)"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                     style="width: 14px; height: 14px; margin-right: 0.3rem;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span x-text="c.kode_cpmk"></span>
                            </label>
                            <button type="button"
                                    class="cpmk-info-btn"
                                    title="Lihat deskripsi CPMK"
                                    @click.prevent.stop="open(c, $event.currentTarget)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            </button>
                        </span>
                    </template>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.1rem;">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Batal</button>
                    <button type="button" class="btn btn-primary" @click="simpan()">Simpan</button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="info"
             x-ref="popover"
             x-cloak
             class="cpmk-info-popover"
             style="position: fixed; display: none;"
             @click.outside="close()"
             @keydown.escape.window="close()">
            <div class="cpmk-info-kode" x-text="info?.cpmk.kode_cpmk"></div>
            <div class="cpmk-info-deskripsi" x-text="info?.cpmk.deskripsi"></div>
        </div>
    </template>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mkCpmkPicker', (cpmks, selected, mkLabels) => ({
        cpmks,
        selected,
        mkLabels,
        info: null,
        modalMkId: null,
        draft: [],
        byId: {},

        init() {
            this.byId = Object.fromEntries(this.cpmks.map(c => [c.id, c]));
        },

        sel(mkId) {
            return this.selected[mkId] || [];
        },

        hasSel(mkId) {
            return this.sel(mkId).length > 0;
        },

        kodeOf(id) {
            return this.byId[id] || { kode_cpmk: '', deskripsi: '' };
        },

        mkLabel(mkId) {
            return this.mkLabels[mkId] || '';
        },

        openModal(mkId, evt) {
            this.lastFocus = evt.currentTarget;
            this.draft = [...this.sel(mkId)];
            this.modalMkId = mkId;
            document.body.classList.add('overflow-y-hidden');
            this.$nextTick(() => {
                const first = this.$refs.modalPanel?.querySelector('input[type="checkbox"]');
                first && first.focus();
            });
        },

        closeModal() {
            if (this.modalMkId === null) {
                return;
            }
            this.modalMkId = null;
            this.draft = [];
            document.body.classList.remove('overflow-y-hidden');
            this.lastFocus && this.lastFocus.focus();
        },

        toggle(id) {
            const i = this.draft.indexOf(id);
            if (i > -1) {
                this.draft.splice(i, 1);
            } else {
                this.draft.push(id);
            }
        },

        simpan() {
            this.selected[this.modalMkId] = [...this.draft];
            this.closeModal();
        },

        open(cpmk, el) {
            if (this.info && this.info.cpmk.id === cpmk.id) {
                return this.close();
            }
            this.info = { cpmk, rect: el.getBoundingClientRect() };
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
    }));
});
</script>
@endpush