@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Metode dan Bobot Penilaian
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('kurikulum.metode-bobot-penilaian.store', $kurikulum->id) }}"
      method="POST"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label" for="mbp-cpl">
            CPL
        </label>

        <x-searchable-select
            id="mbp-cpl"
            name="cpl_id"
            :options="$cpls->map(fn ($cpl) => ['value' => $cpl->id, 'label' => $cpl->kode_cpl.($cpl->deskripsi ? ' - '.\Illuminate\Support\Str::limit($cpl->deskripsi, 70) : '')])"
            :value="old('cpl_id')"
            placeholder="Ketik kode atau deskripsi CPL" />

        @error('cpl_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="mbp-mk">
            Mata Kuliah
        </label>

        <x-searchable-select
            id="mbp-mk"
            name="mata_kuliah_id"
            :options="$mataKuliahs->map(fn ($mk) => ['value' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama])"
            :value="old('mata_kuliah_id')"
            placeholder="Ketik kode atau nama mata kuliah" />

        @error('mata_kuliah_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="mbp-cpmk">
            CPMK
        </label>

        <x-searchable-select
            id="mbp-cpmk"
            name="cpmk_id"
            :options="$cpmks->map(fn ($cpmk) => ['value' => $cpmk->id, 'label' => $cpmk->kode_cpmk.($cpmk->deskripsi ? ' - '.\Illuminate\Support\Str::limit($cpmk->deskripsi, 70) : '')])"
            :value="old('cpmk_id')"
            placeholder="Ketik kode atau deskripsi CPMK" />

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
               value="{{ old($field, 0) }}"
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
        <p id="total-penilaian" class="text-lg font-bold">0%</p>
        <p class="text-sm">Total sebaiknya mencapai 100%.</p>
    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

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
