@extends('layouts.app')

@section('content')

<div class="page-header">
    Edit Materi - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'materi']) }}" class="btn btn-secondary btn-sm">Kembali ke Materi</a>
</div>


<div style="max-width: 720px;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <form action="{{ route('lms.materi.update', [$pengampu->id, $materi->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Judul Materi <span style="color: #dc2626;">*</span></label>
                <input type="text" name="judul" class="form-input" required value="{{ old('judul', $materi->judul) }}">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea" style="min-height: 80px;">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
            </div>
            @if($pertemuans->isNotEmpty())
                <div class="form-group">
                    <label class="form-label" for="lms-materi-pertemuan">RPS Pertemuan</label>
                    <x-searchable-select
                        id="lms-materi-pertemuan"
                        name="rps_pertemuan_id"
                        :options="$pertemuans->map(fn ($pertemuan) => ['value' => $pertemuan->id, 'label' => 'Minggu '.$pertemuan->minggu.' - '.\Illuminate\Support\Str::limit($pertemuan->sub_cpmk, 80)])"
                        :value="old('rps_pertemuan_id', $materi->rps_pertemuan_id)"
                        placeholder="Pilih pertemuan (opsional)"
                        input-class="form-input" />
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.mp4,.jpg,.png">
                @if($materi->file_path)
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #64748b;">File saat ini: <x-file-link :file="$materi->file_path" :nama="$materi->file_name" :href="route('lms.file', ['materi', $materi->id])" /></div>
                @endif
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection
