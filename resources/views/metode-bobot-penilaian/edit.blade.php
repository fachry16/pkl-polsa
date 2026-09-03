@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Metode dan Bobot Penilaian
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('kurikulum.metode-bobot-penilaian.update', [$kurikulum->id, $item->id]) }}"
      method="POST"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">
            CPL
        </label>

        <select name="cpl_id"
                class="form-select">

            <option value="">
                -- Pilih CPL --
            </option>

            @foreach($cpls as $cpl)

            <option value="{{ $cpl->id }}"
                {{ old('cpl_id', $item->cpl_id) == $cpl->id ? 'selected' : '' }}>

                {{ $cpl->kode_cpl }}
                @if($cpl->deskripsi)
                    — {{ \Illuminate\Support\Str::limit($cpl->deskripsi, 60) }}
                @endif

            </option>

            @endforeach

        </select>

        @error('cpl_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            Mata Kuliah
        </label>

        <select name="mata_kuliah_id"
                class="form-select">

            <option value="">
                -- Pilih Mata Kuliah --
            </option>

            @foreach($mataKuliahs as $mk)

            <option value="{{ $mk->id }}"
                {{ old('mata_kuliah_id', $item->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>

                {{ $mk->kode }}
                -
                {{ $mk->nama }}

            </option>

            @endforeach

        </select>

        @error('mata_kuliah_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">
            CPMK
        </label>

        <select name="cpmk_id"
                class="form-select">

            <option value="">
                -- Pilih CPMK --
            </option>

            @foreach($cpmks as $cpmk)

            <option value="{{ $cpmk->id }}"
                {{ old('cpmk_id', $item->cpmk_id) == $cpmk->id ? 'selected' : '' }}>

                {{ $cpmk->kode_cpmk }}
                @if($cpmk->deskripsi)
                    — {{ \Illuminate\Support\Str::limit($cpmk->deskripsi, 60) }}
                @endif

            </option>

            @endforeach

        </select>

        @error('cpmk_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    @php
        $komponen = [
            'partisipasi' => 'Partisipasi',
            'kuis' => 'Kuis',
            'tugas_teori_individu' => 'Tugas Teori (Individu)',
            'unjuk_kerja_presentasi' => 'Unjuk Kerja (Presentasi)',
            'tes_tulis_uts' => 'Tes Tulis (UTS)',
            'tes_tulis_uas' => 'Tes Tulis (UAS)',
            'tugas_teori_kelompok' => 'Tugas Teori (Kelompok)',
            'tugas_praktikum' => 'Tugas Praktikum',
            'responsi' => 'Responsi',
        ];
    @endphp

    @foreach($komponen as $field => $label)

    <div class="form-group">

        <label class="form-label">
            {{ $label }} (%)
        </label>

        <input type="number"
               name="{{ $field }}"
               value="{{ old($field, $item->{$field}) }}"
               min="0"
               max="100"
               step="0.01"
               class="form-input w-full bobot-input"
               oninput="hitungTotalPenilaian()">

        @error($field)
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    @endforeach

    <div class="form-group">
        <label class="form-label font-bold">Total</label>
        <p id="total-penilaian" class="text-lg font-bold">{{ $item->total }}%</p>
        <p class="text-sm">Total sebaiknya mencapai 100%.</p>
    </div>

    <div class="btn-group">

        <button class="btn btn-warning">

            Update

        </button>

        <a href="{{ route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@push('scripts')
<script>
function hitungTotalPenilaian() {
    var inputs = document.querySelectorAll('.bobot-input');
    var total = 0;
    inputs.forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('total-penilaian').textContent = total + '%';
}
hitungTotalPenilaian();
</script>
@endpush

@endsection
