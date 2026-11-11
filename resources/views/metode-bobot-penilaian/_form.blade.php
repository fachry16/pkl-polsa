@php
    $item = $item ?? null;
    $submitLabel = $submitLabel ?? 'Simpan';
    $komponen = \App\Models\MetodeBobotPenilaian::KOMPONEN;
@endphp

<x-alert type="error" :message="session('error')" />

<form action="{{ $action }}"
      method="POST"
      class="card"
      x-data="{
          data: {{ Js::from($data) }},
          mk: {{ Js::from($initial['mk']) }},
          cpmk: {{ Js::from($initial['cpmk']) }},
          cpl: {{ Js::from($initial['cpl']) }},
          init() {
              this.$watch('mk', () => this.pilihCpmkPertama());
              this.$watch('cpmk', () => this.pilihCplPertama());
          },
          get cpmkOptions() {
              return this.data.cpmksByMk[this.mk] ?? [];
          },
          get cplOptions() {
              return this.data.cplsByCpmk[this.cpmk] ?? [];
          },
          pilihCpmkPertama() {
              this.cpmk = this.cpmkOptions.length ? String(this.cpmkOptions[0].id) : '';
              this.pilihCplPertama();
          },
          pilihCplPertama() {
              this.cpl = this.cplOptions.length ? String(this.cplOptions[0].id) : '';
          },
      }">

    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="form-group">

        <label class="form-label" for="mbp-mk">
            Mata Kuliah
        </label>

        <select id="mbp-mk"
                name="mata_kuliah_id"
                class="form-select"
                x-model="mk"
                required>
            <option value="">Pilih Mata Kuliah</option>
            <template x-for="option in data.mataKuliahs" :key="option.id">
                <option :value="String(option.id)" x-text="option.label"></option>
            </template>
        </select>

        @error('mata_kuliah_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="mbp-cpmk">
            CPMK
        </label>

        <select id="mbp-cpmk"
                name="cpmk_id"
                class="form-select"
                x-model="cpmk"
                :disabled="!mk"
                required>
            <option value="">Pilih CPMK</option>
            <template x-for="option in cpmkOptions" :key="option.id">
                <option :value="String(option.id)" x-text="option.label"></option>
            </template>
        </select>

        @error('cpmk_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label" for="mbp-cpl">
            CPL
        </label>

        <select id="mbp-cpl"
                name="cpl_id"
                class="form-select"
                x-model="cpl"
                :disabled="!cpmk"
                required>
            <option value="">Pilih CPL</option>
            <template x-for="option in cplOptions" :key="option.id">
                <option :value="String(option.id)" x-text="option.label"></option>
            </template>
        </select>

        @error('cpl_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    @foreach($komponen as $field => $label)

    <div class="form-group">

        <label class="form-label">
            {{ $label }} (%)
        </label>

        <input type="number"
               name="{{ $field }}"
               value="{{ old($field, $item?->{$field} ?? 0) }}"
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
        <p id="total-penilaian" class="text-lg font-bold">{{ $item?->total ?? 0 }}%</p>
        <p class="text-sm">Total sebaiknya mencapai 100%.</p>
    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            {{ $submitLabel }}

        </button>

        <a href="{{ $backUrl }}"
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
