@extends('layouts.app')

@section('content')

<div x-data="cpmkInfo()">

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
                    </tr>
                </thead>
                <tbody>
                    @foreach($mataKuliahs as $mataKuliah)
                    <tr>
                        <td class="font-medium">{{ $mataKuliah->kode }}</td>
                        <td>{{ $mataKuliah->nama }}</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                @foreach($cpmks as $cpmk)
                                <span class="cpmk-chip">
                                    <label class="cpmk-chip-label">
                                        <input class="sr-only"
                                               type="checkbox"
                                               name="mataKuliah[{{ $mataKuliah->id }}][]"
                                               value="{{ $cpmk->id }}"
                                               {{ $mataKuliah->cpmks->contains($cpmk->id) ? 'checked' : '' }}
                                               {{ (auth()->user()->isAdmin() || auth()->user()->isKaprodi()) ? '' : 'disabled' }}>
                                        <span>{{ $cpmk->kode_cpmk }}</span>
                                    </label>
                                    <button type="button"
                                            class="cpmk-info-btn"
                                            title="Lihat deskripsi CPMK"
                                            data-cpmk="{{ json_encode($cpmk->only(['id', 'kode_cpmk', 'deskripsi'])) }}"
                                            @click.prevent.stop="open(JSON.parse($event.currentTarget.dataset.cpmk), $event.currentTarget)">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                    </button>
                                </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
        <button type="submit" class="btn btn-primary mt-5">Simpan Matriks</button>
        @endif
    </form>

    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">Kembali</a>
    </div>

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
    Alpine.data('cpmkInfo', () => ({
        info: null,

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