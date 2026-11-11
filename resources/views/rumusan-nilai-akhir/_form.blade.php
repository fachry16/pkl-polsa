@php
    $submitLabel = $submitLabel ?? 'Simpan';
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
          get metode() {
              return this.data.metode[this.mk + '-' + this.cpmk + '-' + this.cpl] ?? null;
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

        <label class="form-label" for="rumusan-mk">
            Mata Kuliah
        </label>

        <select id="rumusan-mk"
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

        <label class="form-label" for="rumusan-cpmk">
            CPMK
        </label>

        <select id="rumusan-cpmk"
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

        <label class="form-label" for="rumusan-cpl">
            CPL
        </label>

        <select id="rumusan-cpl"
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

    <div class="form-group" x-show="metode" x-cloak>

        <label class="form-label">
            Metode &amp; Bobot Penilaian
        </label>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-center">Bobot (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="komponen in metode.komponen" :key="komponen.label">
                        <tr>
                            <td x-text="komponen.label"></td>
                            <td class="text-center" x-text="komponen.nilai"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row">Total</th>
                        <th class="text-center"><span x-text="metode.total"></span>%</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <p class="form-error" x-show="mk && cpmk && cpl && !metode" x-cloak>
        Metode &amp; bobot penilaian untuk kombinasi MK, CPMK, dan CPL ini belum tersedia.
    </p>

    <div class="btn-group">

        <button class="btn btn-primary" :disabled="!metode">
            {{ $submitLabel }}
        </button>

        <a href="{{ $backUrl }}" class="btn btn-secondary">
            Kembali
        </a>

    </div>

</form>
