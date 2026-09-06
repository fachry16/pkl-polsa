@extends('layouts.app')

@section('content')

<div class="page-header">
    Materi - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
</div>

<div style="display: grid; grid-template-columns: 380px 1fr; gap: 1.5rem; align-items: start;"
     x-data="{
         rpsPertemuans: @js($rpsPertemuans),
         selectedRpsId: '',
         selectedRps: null,
         selectRps(id) {
             this.selectedRpsId = id;
             if (!id) {
                 this.selectedRps = null;
                 return;
             }
             this.selectedRps = this.rpsPertemuans.find(p => p.id == id) || null;
             if (this.selectedRps) {
                 document.getElementById('input-judul').value = this.selectedRps.materi || ('Materi Pertemuan ' + this.selectedRps.minggu);
                 let desc = '';
                 if (this.selectedRps.sub_cpmk) desc += 'Sub-CPMK: ' + this.selectedRps.sub_cpmk + '\n\n';
                 if (this.selectedRps.pengalaman_belajar) desc += 'Pengalaman Belajar: ' + this.selectedRps.pengalaman_belajar + '\n';
                 if (this.selectedRps.indikator) desc += 'Indikator: ' + this.selectedRps.indikator;
                 document.getElementById('input-deskripsi').value = desc.trim();
                 let rpsSelect = document.getElementById('input-rps-pertemuan');
                 if (rpsSelect) rpsSelect.value = id;
             }
         }
     }">

    {{-- Form Tambah / Unggah Materi dari RPS --}}
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #1e293b;">Posting Materi ke LMS</h3>
            <span style="font-size: 0.7rem; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;">Berbasis RPS</span>
        </div>

        {{-- Dropdown Pilihan RPS --}}
        <div class="form-group" style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">
            <label class="form-label" style="font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 0.35rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Ambil dari RPS Pertemuan
            </label>
            <select x-model="selectedRpsId" @change="selectRps($event.target.value)" class="form-input w-full" style="background: #fff; font-size: 0.82rem;">
                <option value="">-- Pilih Materi Pertemuan dari RPS --</option>
                <template x-for="p in rpsPertemuans" :key="p.id">
                    <option :value="p.id" x-text="'Minggu ' + p.minggu + ' - ' + (p.materi ? p.materi.substring(0, 45) : (p.sub_cpmk ? p.sub_cpmk.substring(0, 45) : 'Pertemuan ' + p.minggu))"></option>
                </template>
            </select>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.35rem;">
                Pilih modul/pertemuan RPS untuk otomatis mengisi judul dan rincian materi.
            </div>
        </div>

        {{-- Panel Preview Detail RPS (Tampil ketika item RPS dipilih) --}}
        <template x-if="selectedRps">
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #0369a1; text-transform: uppercase; letter-spacing: 0.5px;">Preview Detail RPS</span>
                    <span style="font-size: 0.7rem; background: #e0f2fe; color: #0284c7; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;" x-text="'Minggu ' + selectedRps.minggu"></span>
                </div>
                <div style="font-size: 0.82rem; font-weight: 600; color: #0c4a6e;" x-text="selectedRps.materi || '-'"></div>
                <div style="font-size: 0.75rem; color: #334155; margin-top: 0.35rem; line-height: 1.4;" x-show="selectedRps.sub_cpmk">
                    <strong>Sub-CPMK:</strong> <span x-text="selectedRps.sub_cpmk"></span>
                </div>
                <div style="font-size: 0.72rem; color: #475569; margin-top: 0.35rem; line-height: 1.4;" x-show="selectedRps.pengalaman_belajar">
                    <strong>Pengalaman Belajar:</strong> <span x-text="selectedRps.pengalaman_belajar"></span>
                </div>
            </div>
        </template>

        <form action="{{ route('lms.materi.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul Materi <span style="color: #dc2626;">*</span></label>
                <input type="text" id="input-judul" name="judul" class="form-input" required value="{{ old('judul') }}" placeholder="Judul materi pembelajaran">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi / Rincian Materi</label>
                <textarea id="input-deskripsi" name="deskripsi" class="form-textarea" style="min-height: 90px;" placeholder="Tuliskan deskripsi atau rincian instruksi materi...">{{ old('deskripsi') }}</textarea>
            </div>

            @if($pertemuans->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">RPS Pertemuan Terkait</label>
                    <select id="input-rps-pertemuan" name="rps_pertemuan_id" class="form-input">
                        <option value="">-- Pilih Pertemuan (opsional) --</option>
                        @foreach($pertemuans as $pertemuan)
                            <option value="{{ $pertemuan->id }}" @selected(old('rps_pertemuan_id') == $pertemuan->id)>
                                Minggu {{ $pertemuan->minggu }} - {{ Str::limit($pertemuan->sub_cpmk ?: $pertemuan->materi, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Upload File Lampiran</label>
                <input type="file" name="file" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.mp4,.jpg,.png">
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">PDF, PPT, DOC, XLS, Video, Gambar, atau ZIP (maks 50 MB).</div>
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Konfirmasi &amp; Unggah Materi ke LMS
            </button>
        </form>
    </div>

    {{-- Daftar Materi LMS yang Sudah Diunggah --}}
    <div>
        @forelse($materis as $materi)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $materi->judul }}</span>
                            <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">{{ $materi->created_at->format('d M Y, H:i') }}</span>
                            @if($materi->rps_pertemuan_id)
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Minggu {{ $materi->rpsPertemuan->minggu ?? '?' }}</span>
                            @endif
                        </div>
                        @if($materi->deskripsi)
                            <p style="font-size: 0.85rem; color: #475569; margin-top: 0.35rem; line-height: 1.6; white-space: pre-wrap;">{!! linkify($materi->deskripsi) !!}</p>
                        @endif
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; align-items: center;">
                            @if($materi->file_path)
                                <x-file-link :file="$materi->file_path" :href="route('lms.file', ['materi', $materi->id])" />
                            @endif
                            <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
                                @if($materi->canBeModified())
                                    <a href="{{ route('lms.materi.edit', [$pengampu->id, $materi->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route('lms.materi.destroy', ['pengampu' => $pengampu->id, 'materi' => $materi->id]) }}" method="POST" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus materi ini?')">Hapus</button>
                                    </form>
                                @else
                                    <span style="font-size: 0.72rem; color: #94a3b8;">Terkunci (lewat 1x24 jam)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada materi yang diunggah ke kelas ini.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $materis->links() }}
        </div>
    </div>
</div>

@endsection
