@extends('layouts.app')

@section('content')

<div style="max-width: 1100px; margin: 0 auto;">
    {{-- Tombol Kembali --}}
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('mahasiswa.lms.show', $pengampu->id) }}?tab=tugas_kelas" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Kembali ke Tugas Kelas
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        
        {{-- KOLOM KIRI: DETAIL TUGAS & KOMENTAR KELAS --}}
        <div>
            {{-- Card Detail Tugas --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 1.25rem; align-items: flex-start;">
                    <div style="width: 3rem; height: 3rem; border-radius: 50%; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                    </div>
                    <div style="flex: 1;">
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $tugas->judul }}</h1>
                        
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span>{{ $pengampu->dosen?->user?->name ?? 'Dosen' }}</span>
                            &middot;
                            <span>{{ $tugas->created_at->format('d M Y, H:i') }}</span>
                            @if($tugas->rpsPertemuan)
                                &middot;
                                <span style="background: #f1f5f9; color: #475569; padding: 0.1rem 0.5rem; border-radius: 4px; font-weight: 600;">Pertemuan {{ $tugas->rpsPertemuan->minggu_ke }}</span>
                            @endif
                        </div>

                        {{-- Poin & Tenggat --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding: 0.6rem 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem;">
                            <span style="font-weight: 600; color: #1e293b;">{{ $tugas->bobot_nilai ?? 100 }} poin</span>
                            <span style="color: #ef4444; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Tenggat: {{ $tugas->deadline->format('d M Y, H:i') }}
                            </span>
                        </div>

                        {{-- Instruksi --}}
                        @if($tugas->instruksi)
                            <div style="font-size: 0.9rem; color: #334155; margin-top: 1.25rem; line-height: 1.7; white-space: pre-wrap;">{!! linkify($tugas->instruksi) !!}</div>
                        @endif

                        {{-- Lampiran File Dosen --}}
                        @if($tugas->file_lampiran)
                            <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                                <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Lampiran Tugas:</div>
                                <x-file-link :file="$tugas->file_lampiran" :href="route('mahasiswa.lms.file', ['tugas', $tugas->id])" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Komentar Kelas (Class Comments) --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Komentar Kelas ({{ $komentarsKelas->count() }})
                </h3>

                {{-- Form Tambah Komentar --}}
                <form action="{{ route('mahasiswa.lms.topik.komentar.store', $pengampu->id) }}" method="POST" style="margin-bottom: 1.5rem;">
                    @csrf
                    <input type="hidden" name="tipe_topik" value="tugas">
                    <input type="hidden" name="topik_id" value="{{ $tugas->id }}">
                    <input type="hidden" name="is_private" value="0">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="pesan" class="form-input" placeholder="Tambahkan komentar kelas..." required style="flex: 1; font-size: 0.85rem;">
                        <button type="submit" class="btn btn-primary btn-sm">Kirim</button>
                    </div>
                </form>

                {{-- List Komentar --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @forelse($komentarsKelas as $komentar)
                        <div style="display: flex; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #f8fafc;">
                            <div style="width: 2rem; height: 2rem; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                {{ substr($komentar->user->name ?? '?', 0, 2) }}
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $komentar->user->name ?? '-' }}</span>
                                    <span style="font-size: 0.7rem; color: #94a3b8;">{{ $komentar->created_at->diffForHumans() }}</span>
                                </div>
                                <div style="font-size: 0.85rem; color: #334155; margin-top: 0.2rem;">{{ $komentar->pesan }}</div>
                                @if($komentar->user_id === auth()->id() && $komentar->isWithinTimeLimit(15))
                                    <div style="margin-top: 0.25rem;">
                                        <form action="{{ route('mahasiswa.lms.topik.komentar.destroy', [$pengampu->id, $komentar->id]) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?');" style="margin: 0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-xs" style="color: #ef4444; padding: 0; font-size: 0.7rem; text-decoration: none;">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; text-align: center; padding: 1rem 0;">Belum ada komentar kelas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: CARD PENUGASAN & KOMENTAR PRIBADI --}}
        <div>
            
            {{-- CARD TUGAS ANDA --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Tugas Anda</h3>
                    @if($submission)
                        <span style="background: {{ $submission->isTerlambat() ? '#fef2f2' : '#ecfdf5' }}; color: {{ $submission->isTerlambat() ? '#dc2626' : '#059669' }}; border: 1px solid {{ $submission->isTerlambat() ? '#fecaca' : '#a7f3d0' }}; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                            {{ $submission->isTerlambat() ? 'Terlambat' : 'Diserahkan' }}
                        </span>
                    @elseif($tugas->deadline->isPast())
                        <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                            Terlewat
                        </span>
                    @else
                        <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                            Ditugaskan
                        </span>
                    @endif
                </div>

                {{-- Status Nilai --}}
                @if($submission && $submission->nilai !== null)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; margin-bottom: 1rem; text-align: center;">
                        <span style="font-size: 0.75rem; color: #64748b; display: block;">Nilai Anda</span>
                        <span style="font-size: 1.5rem; font-weight: 700; color: {{ $submission->nilai >= 60 ? '#059669' : '#dc2626' }};">
                            {{ $submission->nilai }}
                        </span>
                        @if($submission->catatan_dosen)
                            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.25rem;">Catatan: {{ $submission->catatan_dosen }}</div>
                        @endif
                    </div>
                @endif

                {{-- Jika Sudah Dikumpulkan --}}
                @if($submission)
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
                        @if($submission->file_jawaban)
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                    <a href="{{ route('mahasiswa.lms.file', ['submission', $submission->id]) }}" target="_blank" style="font-size: 0.8rem; font-weight: 500; color: #2563eb; text-decoration: none;">
                                        {{ basename($submission->file_jawaban) }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($submission->link_jawaban)
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <a href="{{ $submission->link_jawaban }}" target="_blank" style="font-size: 0.8rem; font-weight: 500; color: #2563eb; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $submission->link_jawaban }}
                                </a>
                            </div>
                        @endif

                        @if($submission->catatan_mahasiswa)
                            <div style="font-size: 0.8rem; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.75rem;">
                                <strong>Pesan:</strong> {{ $submission->catatan_mahasiswa }}
                            </div>
                        @endif
                    </div>

                    {{-- Form Perbarui Kiriman jika belum dinilai & belum deadline --}}
                    @if(!$tugas->deadline->isPast() && $submission->nilai === null)
                        <div x-data="{ openEdit: false }">
                            <button type="button" @click="openEdit = !openEdit" class="btn btn-secondary btn-sm" style="width: 100%;">
                                <span x-text="openEdit ? 'Batal Perbarui' : 'Batalkan / Perbarui Pengiriman'">Batalkan / Perbarui Pengiriman</span>
                            </button>

                            <form x-show="openEdit" action="{{ route('mahasiswa.lms.tugas.update', $submission->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 1rem; display: none;">
                                @csrf @method('PATCH')
                                <div class="form-group" style="margin-bottom: 0.75rem;">
                                    <label class="form-label" style="font-size: 0.75rem;">File Jawaban Baru</label>
                                    <input type="file" name="file_jawaban" class="form-input">
                                </div>
                                <div class="form-group" style="margin-bottom: 0.75rem;">
                                    <label class="form-label" style="font-size: 0.75rem;">Link Jawaban (Google Drive, GitHub, dll)</label>
                                    <input type="url" name="link_jawaban" class="form-input" value="{{ old('link_jawaban', $submission->link_jawaban) }}" placeholder="https://...">
                                </div>
                                <div class="form-group" style="margin-bottom: 0.75rem;">
                                    <label class="form-label" style="font-size: 0.75rem;">Catatan Tambahan</label>
                                    <input type="text" name="catatan_mahasiswa" class="form-input" value="{{ old('catatan_mahasiswa', $submission->catatan_mahasiswa) }}" placeholder="Pesan untuk dosen...">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Simpan Perubahan</button>
                            </form>
                        </div>
                    @endif

                {{-- Jika Belum Dikumpulkan & Belum Deadline --}}
                @elseif(!$tugas->deadline->isPast())
                    <form action="{{ route('mahasiswa.lms.tugas.kumpul', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div x-data="{ addType: '' }">
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.75rem;">
                                {{-- Input File --}}
                                <div class="form-group" style="margin-bottom: 0.5rem;">
                                    <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Lampirkan File</label>
                                    <input type="file" name="file_jawaban" class="form-input" style="font-size: 0.8rem;">
                                </div>

                                {{-- Input Link --}}
                                <div class="form-group" style="margin-bottom: 0.5rem;">
                                    <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Tautan / Link URL</label>
                                    <input type="url" name="link_jawaban" class="form-input" placeholder="https://drive.google.com/..." style="font-size: 0.8rem;">
                                </div>

                                {{-- Input Pesan / Catatan --}}
                                <div class="form-group" style="margin-bottom: 0.5rem;">
                                    <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Pesan / Catatan</label>
                                    <input type="text" name="catatan_mahasiswa" class="form-input" placeholder="Catatan tugas..." style="font-size: 0.8rem;">
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-weight: 600;">
                                    Serahkan Tugas
                                </button>
                                
                                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; font-size: 0.8rem;">
                                    Tandai sebagai selesai
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div style="font-size: 0.8rem; color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem; text-align: center;">
                        Tenggat pengumpulan tugas telah berakhir.
                    </div>
                @endif
            </div>

            {{-- CARD KOMENTAR PRIBADI --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Komentar Pribadi</h3>
                </div>
                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0 0 1rem;">Hanya terlihat oleh Anda dan pengajar.</p>

                {{-- List Komentar Pribadi --}}
                <div style="display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1rem;">
                    @forelse($komentarsPribadi as $komentar)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.2rem;">
                                <span style="font-weight: 600; font-size: 0.75rem; color: #1e293b;">
                                    {{ $komentar->user->name ?? '-' }}
                                    @if($komentar->user->isDosen())
                                        <span style="color: #2563eb; font-weight: 600;">(Dosen)</span>
                                    @endif
                                </span>
                                <span style="font-size: 0.65rem; color: #94a3b8;">{{ $komentar->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 0.8rem; color: #334155; line-height: 1.4;">{{ $komentar->pesan }}</div>
                            @if($komentar->user_id === auth()->id() && $komentar->isWithinTimeLimit(15))
                                <div style="margin-top: 0.2rem; text-align: right;">
                                    <form action="{{ route('mahasiswa.lms.topik.komentar.destroy', [$pengampu->id, $komentar->id]) }}" method="POST" onsubmit="return confirm('Hapus komentar pribadi ini?');" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-xs" style="color: #ef4444; padding: 0; font-size: 0.65rem; text-decoration: none;">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="font-size: 0.75rem; color: #94a3b8; text-align: center; margin: 0.5rem 0;">Belum ada komentar pribadi.</p>
                    @endforelse
                </div>

                {{-- Form Kirim Komentar Pribadi --}}
                <form action="{{ route('mahasiswa.lms.topik.komentar.store', $pengampu->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipe_topik" value="tugas">
                    <input type="hidden" name="topik_id" value="{{ $tugas->id }}">
                    <input type="hidden" name="is_private" value="1">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="pesan" class="form-input" placeholder="Tambahkan komentar pribadi..." required style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.4rem 0.75rem;">Kirim</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection