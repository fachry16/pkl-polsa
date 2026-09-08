@php
    $query = request()->query();
    unset($query['pengampu_id']);
@endphp

<form method="GET" action="{{ request()->url() }}" class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-ta">Tahun Akademik</label>
            <select name="tahun_akademik_id" id="filter-ta" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($drop['tahunAkademiks'] as $ta)
                    <option value="{{ $ta->id }}" {{ ($filters['tahun_akademik_id'] ?? null) == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun }} — {{ ucfirst($ta->semester) }}
                    </option>
                @endforeach
            </select>
        </div>

        @unless(auth()->user()->isDosen() && ! auth()->user()->isKaprodi())
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-prodi">Program Studi</label>
            <select name="program_studi_id" id="filter-prodi" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($drop['programStudis'] as $prodi)
                    <option value="{{ $prodi->id }}" {{ ($filters['program_studi_id'] ?? null) == $prodi->id ? 'selected' : '' }}>
                        {{ $prodi->nama_prodi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-kurikulum">Kurikulum</label>
            <select name="kurikulum_id" id="filter-kurikulum" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($drop['kurikulums'] as $kurikulum)
                    <option value="{{ $kurikulum->id }}" {{ ($filters['kurikulum_id'] ?? null) == $kurikulum->id ? 'selected' : '' }}>
                        {{ $kurikulum->nama_kurikulum }}
                    </option>
                @endforeach
            </select>
        </div>
        @endunless

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-mk">Mata Kuliah</label>
            <select name="mata_kuliah_id" id="filter-mk" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($drop['mataKuliahs'] as $mk)
                    <option value="{{ $mk->id }}" {{ ($filters['mata_kuliah_id'] ?? null) == $mk->id ? 'selected' : '' }}>
                        {{ $mk->kode }} — {{ $mk->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
            Terapkan Filter
        </button>

        <a href="{{ request()->url() }}" class="btn btn-secondary" style="margin-bottom: 0;">
            Reset
        </a>
    </div>
</form>