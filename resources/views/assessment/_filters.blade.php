@php
    $query = request()->query();
    unset($query['pengampu_id']);
@endphp

<form method="GET" action="{{ request()->url() }}" class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-ta">Tahun Akademik</label>
            <x-searchable-select
                id="filter-ta"
                name="tahun_akademik_id"
                :options="$drop['tahunAkademiks']->map(fn ($ta) => ['value' => $ta->id, 'label' => $ta->tahun.' - '.ucfirst($ta->semester)])"
                :value="$filters['tahun_akademik_id'] ?? null"
                placeholder="Semua tahun akademik"
                allow-empty
                auto-submit />
        </div>

        @unless(auth()->user()->isDosen() && ! auth()->user()->isKaprodi())
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-prodi">Program Studi</label>
            <x-searchable-select
                id="filter-prodi"
                name="program_studi_id"
                :options="$drop['programStudis']->map(fn ($prodi) => ['value' => $prodi->id, 'label' => $prodi->nama_prodi])"
                :value="$filters['program_studi_id'] ?? null"
                placeholder="Semua program studi"
                allow-empty
                auto-submit />
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-kurikulum">Kurikulum</label>
            <x-searchable-select
                id="filter-kurikulum"
                name="kurikulum_id"
                :options="$drop['kurikulums']->map(fn ($kurikulum) => ['value' => $kurikulum->id, 'label' => $kurikulum->nama_kurikulum])"
                :value="$filters['kurikulum_id'] ?? null"
                placeholder="Semua kurikulum"
                allow-empty
                auto-submit />
        </div>
        @endunless

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-mk">Mata Kuliah</label>
            <x-searchable-select
                id="filter-mk"
                name="mata_kuliah_id"
                :options="$drop['mataKuliahs']->map(fn ($mk) => ['value' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama])"
                :value="$filters['mata_kuliah_id'] ?? null"
                placeholder="Semua mata kuliah"
                allow-empty
                auto-submit />
        </div>

        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
            Terapkan Filter
        </button>

        <a href="{{ request()->url() }}" class="btn btn-secondary" style="margin-bottom: 0;">
            Reset
        </a>
    </div>
</form>