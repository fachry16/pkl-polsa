@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Pertemuan RPS
</h1>

<form action="{{ route('rps.pertemuan.store', $rps->id) }}"
      method="POST"
      enctype="multipart/form-data"
      class="card">

    @csrf

    <div class="form-group">

        <label class="form-label">Minggu</label>

        <input type="number"
               name="minggu"
               class="form-input w-full"
               value="{{ old('minggu') }}">

    </div>

    <div class="form-group">

        <label class="form-label" for="rps-pertemuan-cpmk">CPMK Induk</label>

        <x-searchable-select
            id="rps-pertemuan-cpmk"
            name="cpmk_induk"
            :options="$cpmks->map(fn ($cpmk) => ['value' => $cpmk->kode_cpmk, 'label' => $cpmk->kode_cpmk.($cpmk->deskripsi ? ' - '.\Illuminate\Support\Str::limit($cpmk->deskripsi, 80) : '')])"
            :value="old('cpmk_induk')"
            placeholder="Ketik kode atau deskripsi CPMK"
            input-class="form-select w-full" />

        @if($cpmks->isEmpty())
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                Belum ada CPMK. <a href="{{ route('kurikulum.mk-cpmk.index', $rps->mataKuliah->kurikulum_id) }}" style="text-decoration: underline;">Kelola CPMK</a>.
            </div>
        @endif

    </div>

    <div class="form-group">

        <label class="form-label" for="rps-pertemuan-sub-cpmk">Sub CPMK</label>

        @php
            $selectedSubCpmk = old('sub_cpmk');
            $opsiSubCpmk = $subCpmks;
            if ($selectedSubCpmk && ! in_array($selectedSubCpmk, $opsiSubCpmk, true)) {
                array_unshift($opsiSubCpmk, $selectedSubCpmk);
            }
        @endphp

        <select id="rps-pertemuan-sub-cpmk" name="sub_cpmk" class="form-select w-full" required>
            <option value="">-- Pilih Sub-CPMK --</option>
            @foreach($opsiSubCpmk as $opsi)
                <option value="{{ $opsi }}" @selected($selectedSubCpmk === $opsi)>{{ $opsi }}</option>
            @endforeach
        </select>

        @if(empty($subCpmks))
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                Belum ada Sub-CPMK. <a href="{{ route('rps.sub-cpmk.index', $rps) }}" style="text-decoration: underline;">Tambah Sub-CPMK</a>.
            </div>
        @endif

        @error('sub_cpmk')
            <span class="form-error">{{ $message }}</span>
        @enderror

    </div>

    <div class="form-group">

        <label class="form-label">Materi Pembelajaran</label>

        <textarea name="materi"
                  class="form-textarea w-full"
                  rows="4">{{ old('materi') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran</label>

        <input type="text"
               name="metode"
               class="form-input w-full"
               value="{{ old('metode') }}">

    </div>

    <div class="form-group">

        <label class="form-label">Pengalaman Belajar</label>

        <textarea name="pengalaman_belajar"
                  class="form-textarea w-full"
                  rows="4">{{ old('pengalaman_belajar') }}</textarea>

    </div>
    <div class="form-group">

        <label class="form-label">Indikator</label>

        <textarea name="indikator"
                  class="form-textarea w-full"
                  rows="4">{{ old('indikator') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Penilaian - Teknik &amp; Kriteria</label>

        <textarea name="teknik_kriteria"
                  class="form-textarea w-full"
                  rows="3">{{ old('teknik_kriteria') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran Daring (Online)</label>

        <textarea name="metode_daring"
                  class="form-textarea w-full"
                  rows="3">{{ old('metode_daring') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Metode Pembelajaran Luring (Offline)</label>

        <textarea name="metode_luring"
                  class="form-textarea w-full"
                  rows="3">{{ old('metode_luring') }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Bobot (%)</label>

        <input type="number"
               name="bobot"
               class="form-input w-full"
               value="{{ old('bobot') }}">

    </div>

    <div class="form-group">

        <label class="form-label">File Materi (Lampiran)</label>

        <input type="file"
               name="file"
               class="form-input w-full">
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Format PDF, DOC, XLS, PPT, ZIP, gambar (maks 50 MB)</div>

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('rps.pertemuan.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection
