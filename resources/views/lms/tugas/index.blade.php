@extends('layouts.app')

@section('content')

<div class="page-header">
    Tugas - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'rekap_nilai']) }}" class="btn btn-secondary btn-sm">Rekap Nilai</a>
</div>

<div style="display: grid; grid-template-columns: 400px 1fr; gap: 1.5rem; align-items: start;"
     x-data="{
         rpsTugasList: @js($rpsTugasList),
         pertemuans: @js($pertemuans->map(fn($p) => ['id' => $p->id, 'minggu' => (int) $p->minggu])),
         selectedRpsTugasId: '',
         selectedRpsTugas: null,
         parseMinggu(topik) {
             if (!topik) return null;
             let m = String(topik).match(/\d+/);
             return m ? parseInt(m[0], 10) : null;
         },
         autoPertemuan(topik) {
             let minggu = this.parseMinggu(topik);
             if (!minggu) return '';
             let found = this.pertemuans.find(p => p.minggu === minggu);
             return found ? found.id : '';
         },
         formatDatetimeLocal(dtString) {
             if (!dtString) return '';
             let d = new Date(dtString);
             if (isNaN(d.getTime())) return '';
             let year = d.getFullYear();
             let month = String(d.getMonth() + 1).padStart(2, '0');
             let day = String(d.getDate()).padStart(2, '0');
             let hours = String(d.getHours()).padStart(2, '0');
             let minutes = String(d.getMinutes()).padStart(2, '0');
             return `${year}-${month}-${day}T${hours}:${minutes}`;
         },
         selectRpsTugas(id) {
             this.selectedRpsTugasId = id;
             if (!id) {
                 this.selectedRpsTugas = null;
                 return;
             }
             this.selectedRpsTugas = this.rpsTugasList.find(t => t.id == id) || null;
             if (this.selectedRpsTugas) {
                 document.getElementById('input-judul').value = this.selectedRpsTugas.nama_tugas || '';
                 let ins = '';
                 if (this.selectedRpsTugas.penugasan) ins += 'Penugasan: ' + this.selectedRpsTugas.penugasan + '\n\n';
                 if (this.selectedRpsTugas.ruang_lingkup) ins += 'Ruang Lingkup: ' + this.selectedRpsTugas.ruang_lingkup + '\n';
                 if (this.selectedRpsTugas.cara_pengerjaan) ins += 'Cara Pengerjaan: ' + this.selectedRpsTugas.cara_pengerjaan + '\n';
                 if (this.selectedRpsTugas.luaran_tugas) ins += 'Luaran Tugas: ' + this.selectedRpsTugas.luaran_tugas;
                 document.getElementById('input-instruksi').value = ins.trim();

                 let autoId = this.autoPertemuan(this.selectedRpsTugas.minggu_topik);
                 let rpsSelect = document.getElementById('input-rps-pertemuan');
                 if (rpsSelect && autoId) rpsSelect.value = autoId;

                 if (this.selectedRpsTugas.deadline) {
                     let dlInput = document.querySelector('input[name="deadline"]');
                     if (dlInput) dlInput.value = this.formatDatetimeLocal(this.selectedRpsTugas.deadline);
                 }
                 if (this.selectedRpsTugas.bobot_nilai !== null && this.selectedRpsTugas.bobot_nilai !== undefined) {
                     let bobotInput = document.querySelector('input[name="bobot_nilai"]');
                     if (bobotInput) bobotInput.value = this.selectedRpsTugas.bobot_nilai;
                 }
             }
         }
     }">

    {{-- Form Buat / Unggah Tugas dari RPS --}}
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #1e293b;">Buat / Unggah Tugas ke LMS</h3>
            <span style="font-size: 0.7rem; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;">Berbasis RPS</span>
        </div>

        {{-- Dropdown Pilihan RPS Tugas --}}
        <div class="form-group" style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">
            <label class="form-label" style="font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 0.35rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                Ambil Penugasan dari RPS
            </label>
            <select x-model="selectedRpsTugasId" @change="selectRpsTugas($event.target.value)" class="form-input w-full" style="background: #fff; font-size: 0.82rem;">
                <option value="">-- Pilih Rancangan Tugas dari RPS --</option>
                <template x-for="t in rpsTugasList" :key="t.id">
                    <option :value="t.id" x-text="'Minggu ' + (t.minggu_topik || '-') + ' : ' + (t.nama_tugas ? t.nama_tugas.substring(0, 45) : 'Tugas')"></option>
                </template>
            </select>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.35rem;">
                Pilih rancangan tugas RPS untuk otomatis mengisi judul, instruksi, deadline, dan bobot nilai.
            </div>
        </div>

        {{-- Panel Preview Detail RPS Tugas (Tampil ketika item dipilih) --}}
        <template x-if="selectedRpsTugas">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.5px;">Preview Detail Rancangan RPS</span>
                    <span style="font-size: 0.7rem; background: #dcfce7; color: #16a34a; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;" x-text="'Minggu ' + selectedRpsTugas.minggu_topik"></span>
                </div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #14532d;" x-text="selectedRpsTugas.nama_tugas || '-'"></div>
                <div style="font-size: 0.75rem; color: #166534; margin-top: 0.35rem;" x-show="selectedRpsTugas.sub_cpmk">
                    <strong>Sub-CPMK:</strong> <span x-text="selectedRpsTugas.sub_cpmk"></span>
                </div>
                <div style="font-size: 0.75rem; color: #334155; margin-top: 0.35rem; line-height: 1.4;" x-show="selectedRpsTugas.penugasan">
                    <strong>Penugasan:</strong> <span x-text="selectedRpsTugas.penugasan"></span>
                </div>
                <div style="font-size: 0.72rem; color: #475569; margin-top: 0.35rem;" x-show="selectedRpsTugas.ruang_lingkup">
                    <strong>Ruang Lingkup:</strong> <span x-text="selectedRpsTugas.ruang_lingkup"></span>
                </div>
                <div style="font-size: 0.72rem; color: #475569; margin-top: 0.2rem;" x-show="selectedRpsTugas.luaran_tugas">
                    <strong>Luaran Tugas:</strong> <span x-text="selectedRpsTugas.luaran_tugas"></span>
                </div>
                <div style="font-size: 0.72rem; color: #16a34a; margin-top: 0.35rem; font-weight: 600;" x-show="selectedRpsTugas.batas_waktu || selectedRpsTugas.bobot_nilai">
                    <span x-show="selectedRpsTugas.batas_waktu" x-text="'Batas Waktu: ' + selectedRpsTugas.batas_waktu"></span>
                    <span x-show="selectedRpsTugas.bobot_nilai" x-text="' &middot; Bobot: ' + selectedRpsTugas.bobot_nilai"></span>
                </div>
                <div style="font-size: 0.72rem; color: #0284c7; margin-top: 0.25rem; font-weight: 600;" x-show="selectedRpsTugas.file_soal">
                    📎 File Lampiran Soal terlampir dari RPS
                </div>
            </div>
        </template>

        <form action="{{ route('lms.tugas.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul Tugas <span style="color: #dc2626;">*</span></label>
                <input type="text" id="input-judul" name="judul" class="form-input" required value="{{ old('judul') }}" placeholder="Judul penugasan">
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Instruksi Penugasan <span style="color: #dc2626;">*</span></label>
                <textarea id="input-instruksi" name="instruksi" class="form-textarea" style="min-height: 110px;" required placeholder="Petunjuk pengerjaan dan detail tugas...">{{ old('instruksi') }}</textarea>
                @error('instruksi') <div class="form-error">{{ $message }}</div> @enderror
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
                <label class="form-label">Tenggat Waktu (Deadline LMS) <span style="color: #dc2626;">*</span></label>
                <input type="datetime-local" name="deadline" class="form-input" required value="{{ old('deadline') }}">
                @error('deadline') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Bobot Relatif Antar Tugas <span style="color: #dc2626;">*</span></label>
                <input type="number" name="bobot_nilai" class="form-input" min="0" max="100" required value="{{ old('bobot_nilai', 100) }}">
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">Bobot relatif dalam komponen Tugas.</div>
                @error('bobot_nilai') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Batas Upload File Jawaban (MB)</label>
                <input type="number" name="batas_upload_mb" class="form-input" min="1" max="50" value="{{ old('batas_upload_mb') }}" placeholder="Kosongkan = 50 MB">
                @error('batas_upload_mb') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">File Soal / Lampiran</label>
                <input type="file" name="file" class="form-input">
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Konfirmasi &amp; Unggah Tugas ke LMS
            </button>
        </form>
    </div>

    {{-- Daftar Tugas LMS yang Sudah Dibuat --}}
    <div>
        @forelse($tugas as $item)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; transition: all 0.2s; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);"
                 onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)';"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                <a href="{{ route('lms.tugas.show', [$pengampu->id, $item->id]) }}" style="display: block; text-decoration: none;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
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
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                    @if($item->canBeModified())
                        <a href="{{ route('lms.tugas.edit', [$pengampu->id, $item->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form action="{{ route('lms.tugas.destroy', ['pengampu' => $pengampu->id, 'tugas' => $item->id]) }}" method="POST" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">Hapus</button>
                        </form>
                    @else
                        <span style="font-size: 0.72rem; color: #94a3b8;">Terkunci (lewat 1x24 jam)</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada tugas yang dibuat di kelas ini.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $tugas->links() }}
        </div>
    </div>
</div>

@endsection
