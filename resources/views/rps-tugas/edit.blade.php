@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Rancangan Tugas dan Latihan
</h1>

<form action="{{ route('rps.tugas.update', [$rps->id, $tugas->id]) }}"
      method="POST"
      enctype="multipart/form-data"
      class="card">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label class="form-label">Minggu Ke / Topik</label>

        <input type="text"
               name="minggu_topik"
               class="form-input w-full"
               value="{{ old('minggu_topik', $tugas->minggu_topik) }}"
               placeholder="Contoh: 2-3">

    </div>

    <div class="form-group">

        <label class="form-label">Nama Tugas</label>

        <input type="text"
               name="nama_tugas"
               class="form-input w-full"
               value="{{ old('nama_tugas', $tugas->nama_tugas) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Kategori Komponen Penilaian</label>

        <select name="kategori_komponen" class="form-select w-full" required style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid #cbd5e1;">
            <option value="tugas" {{ old('kategori_komponen', $tugas->kategori_komponen ?? 'tugas') === 'tugas' ? 'selected' : '' }}>Tugas (TTi / TTk)</option>
            <option value="quiz" {{ old('kategori_komponen', $tugas->kategori_komponen) === 'quiz' ? 'selected' : '' }}>Kuis / Quiz</option>
            <option value="uts" {{ old('kategori_komponen', $tugas->kategori_komponen) === 'uts' ? 'selected' : '' }}>UTS (Ujian Tengah Semester)</option>
            <option value="uas" {{ old('kategori_komponen', $tugas->kategori_komponen) === 'uas' ? 'selected' : '' }}>UAS (Ujian Akhir Semester)</option>
            <option value="praktikum" {{ old('kategori_komponen', $tugas->kategori_komponen) === 'praktikum' ? 'selected' : '' }}>Praktikum / Responsif</option>
            <option value="project" {{ old('kategori_komponen', $tugas->kategori_komponen) === 'project' ? 'selected' : '' }}>Project (Tugas Akhir / Project Base)</option>
        </select>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Kategori ini digunakan oleh LMS untuk menghitung bobot nilai akhir.</div>

    </div>

    <div class="form-group">

        <label class="form-label">Sub-CPMK</label>

        <input type="text"
               name="sub_cpmk"
               class="form-input w-full"
               value="{{ old('sub_cpmk', $tugas->sub_cpmk) }}"
               placeholder="Contoh: Sub-CPMK2">

    </div>

    <div class="form-group">

        <label class="form-label">Penugasan</label>

        <input type="text"
               name="penugasan"
               class="form-input w-full"
               value="{{ old('penugasan', $tugas->penugasan) }}"
               placeholder="Contoh: Kelompok (3-4 mahasiswa)">

    </div>

    <div class="form-group">

        <label class="form-label">Ruang Lingkup</label>

        <textarea name="ruang_lingkup"
                  class="form-textarea w-full"
                  rows="4">{{ old('ruang_lingkup', $tugas->ruang_lingkup) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Cara Pengerjaan</label>

        <textarea name="cara_pengerjaan"
                  class="form-textarea w-full"
                  rows="4">{{ old('cara_pengerjaan', $tugas->cara_pengerjaan) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Batas Waktu</label>

        <input type="text"
               name="batas_waktu"
               class="form-input w-full"
               value="{{ old('batas_waktu', $tugas->batas_waktu) }}"
               placeholder="Contoh: Minggu 4">

    </div>

    <div class="form-group">

        <label class="form-label">Luaran Tugas yang Dihasilkan</label>

        <textarea name="luaran_tugas"
                  class="form-textarea w-full"
                  rows="4">{{ old('luaran_tugas', $tugas->luaran_tugas) }}</textarea>

    </div>

    <div class="form-group">

        <label class="form-label">Tenggat Waktu / Deadline (LMS)</label>

        <input type="datetime-local"
               name="deadline"
               class="form-input w-full"
               value="{{ old('deadline', $tugas->deadline?->format('Y-m-d\TH:i')) }}">

    </div>

    <div class="form-group">

        <label class="form-label">Bobot Nilai (0 - 100)</label>

        <input type="number"
               name="bobot_nilai"
               class="form-input w-full"
               min="0"
               max="100"
               value="{{ old('bobot_nilai', $tugas->bobot_nilai ?? 100) }}">

    </div>

    <div class="form-group">

        <label class="form-label">File Soal / Lampiran</label>

        <input type="file"
               name="file"
               class="form-input w-full">
        @if($tugas->file_soal)
            <div style="font-size: 0.75rem; color: #16a34a; margin-top: 0.25rem;">
                File saat ini: <a href="{{ Storage::url($tugas->file_soal) }}" target="_blank" style="text-decoration: underline;">Unduh Lampiran</a>
            </div>
        @else
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Format PDF, DOC, XLS, PPT, ZIP, gambar (maks 50 MB)</div>
        @endif

    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Perbarui

        </button>

        <a href="{{ route('rps.tugas.index', $rps->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@endsection