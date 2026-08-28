@extends('layouts.app')

@section('content')

<div class="page-header">
    Edit Tugas - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
</div>


<div style="max-width: 720px;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <form action="{{ route('lms.tugas.update', [$pengampu->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Judul Tugas <span style="color: #dc2626;">*</span></label>
                <input type="text" name="judul" class="form-input" required value="{{ old('judul', $tugas->judul) }}">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Instruksi <span style="color: #dc2626;">*</span></label>
                <textarea name="instruksi" class="form-textarea" style="min-height: 100px;" required>{{ old('instruksi', $tugas->instruksi) }}</textarea>
                @error('instruksi') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            @if($pertemuans->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">RPS Pertemuan</label>
                    <select name="rps_pertemuan_id" class="form-input">
                        <option value="">-- Pilih Pertemuan (opsional) --</option>
                        @foreach($pertemuans as $pertemuan)
                            <option value="{{ $pertemuan->id }}" @selected(old('rps_pertemuan_id', $tugas->rps_pertemuan_id) == $pertemuan->id)>
                                Minggu {{ $pertemuan->minggu }} - {{ Str::limit($pertemuan->sub_cpmk, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Deadline <span style="color: #dc2626;">*</span></label>
                <input type="datetime-local" name="deadline" class="form-input" required value="{{ old('deadline', $tugas->deadline->format('Y-m-d\TH:i')) }}">
                @error('deadline') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bobot (Relatif Antar Tugas) <span style="color: #dc2626;">*</span></label>
                <input type="number" name="bobot_nilai" class="form-input" min="0" max="100" required value="{{ old('bobot_nilai', $tugas->bobot_nilai) }}">
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">Pembobotan antar tugas di komponen Tugas. Bobot komponen diatur di RPS → Penilaian.</div>
                @error('bobot_nilai') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Batas Upload File Jawaban (MB)</label>
                <input type="number" name="batas_upload_mb" class="form-input" min="1" max="50" value="{{ old('batas_upload_mb', $tugas->batas_upload_mb) }}" placeholder="Kosongkan = 50 MB">
                @error('batas_upload_mb') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" class="form-input">
                @if($tugas->file_lampiran)
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #64748b;">File saat ini: <x-file-link :file="$tugas->file_lampiran" :href="route('lms.file', ['tugas', $tugas->id])" /></div>
                @endif
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection
