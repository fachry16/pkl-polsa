@extends('layouts.app')

@section('content')

<div class="page-header">
    Tugas - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'rekap_nilai']) }}" class="btn btn-secondary btn-sm">Rekap Nilai</a>
</div>


<div style="display: grid; grid-template-columns: 360px 1fr; gap: 1.5rem; align-items: start;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0 0 1rem;">Buat Tugas Baru</h3>
        <form action="{{ route('lms.tugas.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul Tugas <span style="color: #dc2626;">*</span></label>
                <input type="text" name="judul" class="form-input" required value="{{ old('judul') }}">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Instruksi <span style="color: #dc2626;">*</span></label>
                <textarea name="instruksi" class="form-textarea" style="min-height: 100px;" required>{{ old('instruksi') }}</textarea>
                @error('instruksi') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            @if($pertemuans->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">RPS Pertemuan</label>
                    <select name="rps_pertemuan_id" class="form-input">
                        <option value="">-- Pilih Pertemuan (opsional) --</option>
                        @foreach($pertemuans as $pertemuan)
                            <option value="{{ $pertemuan->id }}" @selected(old('rps_pertemuan_id') == $pertemuan->id)>
                                Minggu {{ $pertemuan->minggu }} - {{ Str::limit($pertemuan->sub_cpmk, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Deadline <span style="color: #dc2626;">*</span></label>
                <input type="datetime-local" name="deadline" class="form-input" required value="{{ old('deadline') }}">
                @error('deadline') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bobot (Relatif Antar Tugas) <span style="color: #dc2626;">*</span></label>
                <input type="number" name="bobot_nilai" class="form-input" min="0" max="100" required value="{{ old('bobot_nilai', 100) }}">
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">Pembobotan antar tugas di komponen Tugas. Bobot komponen diatur di RPS → Penilaian.</div>
                @error('bobot_nilai') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Batas Upload File Jawaban (MB)</label>
                <input type="number" name="batas_upload_mb" class="form-input" min="1" max="50" value="{{ old('batas_upload_mb') }}" placeholder="Kosongkan = 50 MB">
                @error('batas_upload_mb') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">File Lampiran</label>
                <input type="file" name="file" class="form-input">
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Buat Tugas</button>
        </form>
    </div>

    <div>
        @forelse($tugas as $item)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; transition: all 0.2s; margin-bottom: 0.75rem;"
                 onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)';"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                <a href="{{ route('lms.tugas.show', [$pengampu->id, $item->id]) }}" style="display: block; text-decoration: none;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $item->judul }}</span>
                                @if($item->rps_pertemuan_id)
                                    <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Minggu {{ $item->rpsPertemuan->minggu ?? '?' }}</span>
                                @endif
                                @if($item->deadline->isPast())
                                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Tutup</span>
                                @else
                                    <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Aktif</span>
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                                Deadline: {{ $item->deadline->format('d M Y H:i') }} &middot; Bobot: {{ $item->bobot_nilai }} &middot; {{ $item->submissions_count }} pengumpulan
                            </div>
                        </div>
                    </div>
                </a>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('lms.tugas.edit', [$pengampu->id, $item->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form action="{{ route('lms.tugas.destroy', ['pengampu' => $pengampu->id, 'tugas' => $item->id]) }}" method="POST" style="margin: 0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada tugas. Silakan buat tugas pertama.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $tugas->links() }}
        </div>
    </div>
</div>

@endsection
