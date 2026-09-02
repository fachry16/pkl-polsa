@extends('layouts.app')

@section('content')

<div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 16px; padding: 2rem; color: #ffffff; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15); position: relative; overflow: hidden;">
    <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.15; pointer-events: none;">
        <svg width="220" height="220" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
    </div>
    <div style="position: relative; z-index: 1;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">
                {{ $pengampu->mataKuliah->kode ?? 'MK' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
            </span>
            <span style="font-size: 0.8rem; opacity: 0.9;">{{ $pengampu->label_semester }} {{ $pengampu->tahunAkademik?->tahun ? '· TA ' . $pengampu->tahunAkademik->tahun : '' }}</span>
        </div>
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem; line-height: 1.2;">
            {{ $pengampu->mataKuliah->nama ?? 'Nama Mata Kuliah' }}
        </h1>
        <p style="margin: 0; font-size: 0.9rem; opacity: 0.9; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            {{ $pengampu->dosen?->user?->name ?? 'Dosen Pengampu' }}
        </p>
    </div>
</div>

{{-- Navigation Tabs (Google Classroom Style) --}}
<div x-data="{ 
    tab: '{{ request('tab', 'forum') }}',
    copyLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <button type="button" @click="tab = 'forum'; history.replaceState(null, null, '?tab=forum')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'forum' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Forum
        </button>

        <button type="button" @click="tab = 'tugas_kelas'; history.replaceState(null, null, '?tab=tugas_kelas')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'tugas_kelas' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Tugas Kelas ({{ $materiCount + $tugasCount }})
        </button>

        <button type="button" @click="tab = 'orang'; history.replaceState(null, null, '?tab=orang')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'orang' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Orang ({{ $mahasiswaCount }})
        </button>

        <button type="button" @click="tab = 'presensi'; history.replaceState(null, null, '?tab=presensi')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'presensi' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Presensi
        </button>

        <button type="button" @click="tab = 'rekap_nilai'; history.replaceState(null, null, '?tab=rekap_nilai')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'rekap_nilai' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Rekap Nilai
        </button>

        <a href="{{ route('lms.index') }}" class="btn btn-secondary btn-sm" style="margin-left: auto;">
            Kembali ke Daftar Kelas
        </a>
    </div>

    {{-- TAB 1: FORUM (STREAM) --}}
    <div x-show="tab === 'forum'">
        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
            
            {{-- Kolom Kiri: Tugas Mendatang & Pengumuman --}}
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                {{-- Card Mendatang --}}
                <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 700; color: #1e293b; margin: 0;">Mendatang</h3>
                    </div>
                    @php
                        $tugasMendatang = $pengampu->lmsTugas->filter(fn($t) => $t->deadline && !$t->deadline->isPast())->sortBy('deadline')->take(5);
                    @endphp
                    @forelse($tugasMendatang as $tugas)
                        <div style="padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9;">
                            <a href="{{ route('lms.tugas.show', [$pengampu->id, $tugas->id]) }}" style="font-size: 0.85rem; font-weight: 600; color: #1e293b; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $tugas->judul }}
                            </a>
                            <div style="font-size: 0.75rem; color: #ef4444; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.3rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Tenggat: {{ $tugas->deadline->format('d M, H:i') }}
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.5rem 0 0;">Tidak ada tugas yang mendekati tenggat waktu.</p>
                    @endforelse
                    <div style="margin-top: 0.75rem; text-align: right;">
                        <button type="button" @click="tab = 'tugas_kelas'; history.replaceState(null, null, '?tab=tugas_kelas')" style="background: none; border: none; font-size: 0.8rem; font-weight: 600; color: #2563eb; cursor: pointer; padding: 0;">
                            Lihat semua &rarr;
                        </button>
                    </div>
                </div>

                {{-- Pengumuman Kelas (Di Bawah Mendatang) --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 0.9rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                            Pengumuman
                        </h3>
                    </div>

                    {{-- Form Buat Pengumuman Baru --}}
                    <div x-data="{ openPengumuman: false }" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div @click="openPengumuman = !openPengumuman" style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                            <div style="width: 2rem; height: 2rem; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>
                            </div>
                            <div style="flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.4rem 0.8rem; font-size: 0.8rem; color: #64748b;">
                                Buat pengumuman...
                            </div>
                        </div>

                        <form x-show="openPengumuman" action="{{ route('lms.pengumuman.store', $pengampu->id) }}" method="POST" style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; display: none;">
                            @csrf
                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <input type="text" name="judul" class="form-input" placeholder="Judul Pengumuman" required style="font-size: 0.85rem; padding: 0.4rem 0.6rem;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <textarea name="isi" class="form-textarea" rows="3" placeholder="Tulis isi pengumuman..." required style="font-size: 0.85rem; padding: 0.4rem 0.6rem;"></textarea>
                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button type="button" @click="openPengumuman = false" class="btn btn-secondary btn-xs">Batal</button>
                                <button type="submit" class="btn btn-primary btn-xs">Posting</button>
                            </div>
                        </form>
                    </div>

                    {{-- List Pengumuman Aktif --}}
                    @forelse($pengampu->lmsPengumumans as $pengumuman)
                        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem;">
                                <div>
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $pengumuman->judul }}</div>
                                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.1rem;">
                                        {{ $pengumuman->created_at->format('d M, H:i') }}
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.25rem;">
                                    <a href="{{ route('lms.pengumuman.edit', [$pengampu->id, $pengumuman->id]) }}" class="btn btn-secondary btn-xs" style="padding: 0.15rem 0.35rem; font-size: 0.65rem;">Edit</a>
                                    <form action="{{ route('lms.pengumuman.destroy', [$pengampu->id, $pengumuman->id]) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?');" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" style="padding: 0.15rem 0.35rem; font-size: 0.65rem;">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div style="font-size: 0.8rem; color: #334155; margin-top: 0.5rem; line-height: 1.5; white-space: pre-wrap;">{!! linkify($pengumuman->isi) !!}</div>
                        </div>
                    @empty
                        <p style="font-size: 0.8rem; color: #94a3b8; text-align: center; margin: 0.5rem 0;">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Kanan: Diskusi Forum Kelas --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Diskusi Forum Kelas
                    </h3>

                    {{-- Form Kirim Pesan Forum Baru --}}
                    <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                        @csrf
                        <textarea name="pesan" class="form-textarea" rows="2" placeholder="Tulis pesan atau pertanyaan di forum..." required style="background: #fff;"></textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <input type="file" name="file" class="form-input" style="max-width: 250px; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <button type="submit" class="btn btn-primary btn-sm">Kirim Diskusi</button>
                        </div>
                    </form>

                    {{-- List Thread Diskusi --}}
                    @forelse($pengampu->lmsForumDiskusis as $post)
                        <div style="border-bottom: 1px solid #f1f5f9; padding: 1rem 0;">
                            <div style="display: flex; gap: 0.75rem;">
                                <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                    {{ substr($post->user->name ?? '?', 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $post->user->name ?? '-' }}</span>
                                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $post->created_at->diffForHumans() }}</span>
                                        @if($post->user->isDosen())
                                            <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.4rem; font-size: 0.65rem; font-weight: 600;">Dosen</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 0.85rem; color: #334155; margin-top: 0.35rem; line-height: 1.6; white-space: pre-wrap;">{!! linkify($post->pesan) !!}</div>

                                    @if($post->file_path)
                                        <div style="margin-top: 0.5rem;">
                                            <a href="{{ route('lms.file', ['forum', $post->id]) }}" target="_blank" class="btn btn-secondary btn-xs" style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                                {{ basename($post->file_path) }}
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Actions (30-min limit / Dosen) --}}
                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                        @if(Auth::id() === $post->user_id && $post->isWithinTimeLimit(30))
                                            <a href="{{ route('lms.forum.edit', [$pengampu->id, $post->id]) }}" class="btn btn-secondary btn-xs">Edit</a>
                                        @endif
                                        @if(! $post->user?->isMahasiswa() && $post->isWithinTimeLimit(30))
                                            <form action="{{ route('lms.forum.destroy', [$pengampu->id, $post->id]) }}" method="POST" onsubmit="return confirm('Hapus pesan ini? Semua balasan terkait juga akan terhapus.');" style="margin: 0;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Balasan Thread --}}
                                    @if($post->replies->count())
                                        <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 2px solid #e2e8f0; display: flex; flex-direction: column; gap: 0.75rem;">
                                            @foreach($post->replies as $reply)
                                                <div style="display: flex; gap: 0.6rem;">
                                                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 50%; background: #64748b; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.65rem; flex-shrink: 0;">
                                                        {{ substr($reply->user->name ?? '?', 0, 2) }}
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div style="font-size: 0.8rem; font-weight: 600; color: #1e293b;">
                                                            {{ $reply->user->name ?? '-' }}
                                                            <span style="font-weight: 400; color: #94a3b8; font-size: 0.7rem; margin-left: 0.3rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; white-space: pre-wrap;">{!! linkify($reply->pesan) !!}</div>
                                                        <div style="display: flex; gap: 0.4rem; margin-top: 0.3rem;">
                                                            @if(Auth::id() === $reply->user_id && $reply->isWithinTimeLimit(30))
                                                                <a href="{{ route('lms.forum.edit', [$pengampu->id, $reply->id]) }}" class="btn btn-secondary btn-xs">Edit</a>
                                                            @endif
                                                            @if(! $reply->user?->isMahasiswa() && $reply->isWithinTimeLimit(30))
                                                                <form action="{{ route('lms.forum.destroy', [$pengampu->id, $reply->id]) }}" method="POST" onsubmit="return confirm('Hapus balasan ini?');" style="margin: 0;">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Form Balas --}}
                                    <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" style="margin-top: 0.75rem;">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                        <div style="display: flex; gap: 0.5rem;">
                                            <input type="text" name="pesan" class="form-input" placeholder="Tulis balasan..." required style="padding: 0.35rem 0.6rem; font-size: 0.8rem;">
                                            <button type="submit" class="btn btn-secondary btn-sm">Balas</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 2rem 0;">Belum ada percakapan forum. Mulai diskusi pertama!</p>
                    @endforelse
                </div>
            </div>
        </div>

    {{-- TAB 2: TUGAS KELAS (CLASSWORK) --}}
    <div x-show="tab === 'tugas_kelas'">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem;">Tugas & Materi Perkuliahan</h2>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Kelola modul belajar, materi, dan penugasan kelas.</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('lms.materi.index', $pengampu->id) }}" class="btn btn-primary btn-sm">+ Tambah Materi</a>
                <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">+ Buat Tugas</a>
            </div>
        </div>

        {{-- Daftar Topik / Modul --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            
            {{-- Unified Stream of Classwork --}}
            @php
                $allClasswork = collect();
                foreach($pengampu->lmsMateris as $m) {
                    $allClasswork->push((object)[
                        'type' => 'materi',
                        'id' => $m->id,
                        'title' => $m->judul,
                        'pertemuan' => $m->rpsPertemuan?->minggu_ke ?? null,
                        'deadline' => null,
                        'created_at' => $m->created_at,
                        'url' => route('lms.materi.show', [$pengampu->id, $m->id]),
                        'edit_url' => route('lms.materi.edit', [$pengampu->id, $m->id]),
                        'obj' => $m
                    ]);
                }
                foreach($pengampu->lmsTugas as $t) {
                    $allClasswork->push((object)[
                        'type' => 'tugas',
                        'id' => $t->id,
                        'title' => $t->judul,
                        'pertemuan' => $t->rpsPertemuan?->minggu_ke ?? null,
                        'deadline' => $t->deadline,
                        'submissions_count' => $t->submissions_count,
                        'created_at' => $t->created_at,
                        'url' => route('lms.tugas.show', [$pengampu->id, $t->id]),
                        'edit_url' => route('lms.tugas.edit', [$pengampu->id, $t->id]),
                        'obj' => $t
                    ]);
                }
                $sortedClasswork = $allClasswork->sortByDesc('created_at');
            @endphp

            @forelse($sortedClasswork as $item)
                <div x-data="{ openMenu: false }" 
                    @click="window.location.href = '{{ $item->url }}'"
                    style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.03); cursor: pointer;"
                    onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.03)'; this.style.borderColor='#e2e8f0'">
                    
                    <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 0;">
                        {{-- Icon Pembeda: Biru untuk Materi, Abu-abu untuk Tugas --}}
                        <div style="width: 2.75rem; height: 2.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $item->type === 'materi' ? '#dbeafe' : '#f1f5f9' }}; color: {{ $item->type === 'materi' ? '#2563eb' : '#475569' }};">
                            @if($item->type === 'materi')
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            @else
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                            @endif
                        </div>

                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <a href="{{ $item->url }}" @click.stop style="font-weight: 600; font-size: 0.95rem; color: #1e293b; text-decoration: none;">
                                    {{ $item->title }}
                                </a>
                                @if($item->pertemuan)
                                    <span style="background: #f1f5f9; color: #64748b; font-size: 0.7rem; font-weight: 600; padding: 0.1rem 0.4rem; border-radius: 4px;">Pertemuan {{ $item->pertemuan }}</span>
                                @endif
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem;">
                                Diposting: {{ $item->created_at->format('d M, H:i') }}
                                @if($item->type === 'tugas' && isset($item->submissions_count))
                                    &middot; <span style="color: #2563eb; font-weight: 500;">{{ $item->submissions_count }} pengumpulan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tenggat & Kebab Menu --}}
                    <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                        @if($item->deadline)
                            <div style="font-size: 0.8rem; color: #64748b; text-align: right;">
                                <span style="display: block; font-size: 0.7rem; color: #94a3b8;">Tenggat</span>
                                {{ $item->deadline->format('d M, H:i') }}
                            </div>
                        @endif

                        {{-- 3-Dots Kebab Menu --}}
                        <div style="position: relative;" @click.stop>
                            <button type="button" @click="openMenu = !openMenu" @click.outside="openMenu = false" style="background: none; border: none; padding: 0.4rem; cursor: pointer; color: #64748b; border-radius: 50%;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>

                            <div x-show="openMenu" style="position: absolute; right: 0; top: 100%; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; min-width: 140px; z-index: 20; padding: 0.35rem 0; display: none;">
                                <button type="button" @click="copyLink('{{ $item->url }}'); openMenu = false;" style="width: 100%; text-align: left; padding: 0.5rem 0.85rem; font-size: 0.8rem; background: none; border: none; cursor: pointer; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    Salin Link
                                </button>
                                @if($item->obj->canBeModified())
                                    <a href="{{ $item->edit_url }}" style="width: 100%; text-align: left; padding: 0.5rem 0.85rem; font-size: 0.8rem; background: none; border: none; cursor: pointer; color: #1e293b; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 3rem; text-align: center;">
                    <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Belum ada topik materi atau tugas yang dibuat.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- TAB 3: ORANG (PEOPLE) --}}
    <div x-show="tab === 'orang'">
        <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">
            
            {{-- Pengajar / Dosen --}}
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e3a8a; border-bottom: 2px solid #2563eb; padding-bottom: 0.5rem; margin: 0 0 1rem;">
                    Pengajar
                </h2>
                <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                        {{ substr($pengampu->dosen?->user?->name ?? 'D', 0, 2) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b;">{{ $pengampu->dosen?->user?->name ?? '-' }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">NIDN: {{ $pengampu->dosen?->nidn ?? '-' }} &middot; {{ $pengampu->dosen?->user?->email ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Mahasiswa --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2563eb; padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e3a8a; margin: 0;">
                        Mahasiswa
                    </h2>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">{{ $mahasiswaCount }} mahasiswa</span>
                </div>

                <div style="display: flex; flex-direction: column;">
                    @forelse($pengampu->mahasiswas->sortBy('nim') as $mhs)
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;">
                                {{ substr($mhs->nama ?? $mhs->user?->name ?? 'M', 0, 2) }}
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">{{ $mhs->nama ?? $mhs->user?->name }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">NIM: {{ $mhs->nim }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.85rem; padding: 1.5rem 0; text-align: center;">Belum ada mahasiswa yang terdaftar di kelas ini.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 4: PRESENSI --}}
    <div x-show="tab === 'presensi'">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem;">Presensi Kehadiran</h2>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Buka sesi pertemuan dan pantau kehadiran mahasiswa.</p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($pertemuans as $pertemuan)
                @php
                    $sesi = $sesis->get($pertemuan->id);
                    $counts = $sesi ? $sesi->absensis->groupBy('status')->map->count() : collect();
                @endphp
                <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                    <div style="flex: 1; min-width: 220px;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: #0f172a;">
                            Pertemuan {{ $pertemuan->minggu }}
                        </div>
                        <div style="font-size: 0.85rem; color: #475569; margin-top: 0.2rem;">
                            {{ $pertemuan->materi }}
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">
                            @if($sesi)
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                                    Dilaksanakan {{ $sesi->tanggal_aktual->format('d M Y') }}
                                </span> &middot;
                                Hadir <strong style="color: #059669;">{{ $counts->get('hadir', 0) }}</strong> / 
                                Sakit <strong style="color: #d97706;">{{ $counts->get('sakit', 0) }}</strong> /
                                Izin <strong style="color: #2563eb;">{{ $counts->get('izin', 0) }}</strong> / 
                                Alpa <strong style="color: #dc2626;">{{ $counts->get('alpa', 0) }}</strong>
                            @else
                                <span style="color: #94a3b8;">Belum dibuka</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($sesi)
                            <a href="{{ route('lms.absensi.show', [$pengampu->id, $sesi->id]) }}" class="btn btn-secondary btn-sm">
                                Isi &amp; Ubah Presensi
                            </a>
                        @else
                            <form action="{{ route('lms.absensi.buka', $pengampu->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="rps_pertemuan_id" value="{{ $pertemuan->id }}">
                                <button type="submit" class="btn btn-primary btn-sm">Buka Sesi</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                    <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">RPS belum memiliki daftar pertemuan. Tambahkan pertemuan di menu RPS terlebih dahulu.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- TAB 5: REKAP NILAI --}}
    <div x-show="tab === 'rekap_nilai'">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem;">Rekap Nilai Perkuliahan</h2>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Rekap nilai tugas, input komponen nilai (Quiz/UTS/UAS), dan kalkulasi nilai akhir.</p>
            </div>
            <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    Hitung Ulang Nilai
                </button>
            </form>
        </div>

        {{-- CARD SIMULASI & BEDAH FORMULA REAL-TIME --}}
        @php
            $simulasiData = $pengampu->mahasiswas->sortBy('nim')->map(function ($mhs) use ($nilaiByMhs, $bobot) {
                $mhsNilai = $nilaiByMhs->get($mhs->id, collect());
                $komponens = [];
                $totalPoin = 0;
                $totalBobotAktif = 0;
                $perkalianList = [];
                $bobotList = [];

                foreach ($bobot as $k => $persen) {
                    if ($persen <= 0) continue;
                    $val = $mhsNilai->firstWhere('komponen', $k)?->nilai;
                    $valNum = $val !== null ? (float) $val : null;
                    $terisi = $valNum !== null;
                    $kontribusi = $terisi ? round($valNum * $persen / 100, 2) : 0;
                    if ($terisi) {
                        $poin = $valNum * (float) $persen;
                        $totalPoin += $poin;
                        $totalBobotAktif += (float) $persen;
                        $perkalianList[] = "({$valNum} × {$persen})";
                        $bobotList[] = "{$persen}%";
                    }
                    $komponens[] = [
                        'nama' => ucfirst($k),
                        'bobot' => (float) $persen,
                        'nilai' => $valNum,
                        'kontribusi' => $kontribusi,
                        'terisi' => $terisi,
                    ];
                }

                $nilaiAkhir = $totalBobotAktif > 0 ? round($totalPoin / $totalBobotAktif, 2) : null;
                $huruf = $nilaiAkhir !== null ? konversiNilaiHuruf($nilaiAkhir) : '-';
                $bobotMutu = $nilaiAkhir !== null ? konversiBobotMutu($nilaiAkhir) : null;
                $predikat = $nilaiAkhir !== null ? predikatNilai($nilaiAkhir) : 'Belum Ada Nilai';

                return [
                    'id' => $mhs->id,
                    'nim' => $mhs->nim,
                    'nama' => $mhs->nama,
                    'jenis_kelas' => $mhs->jenis_kelas ?? 'Reguler',
                    'komponens' => $komponens,
                    'totalPoin' => round($totalPoin, 2),
                    'totalBobotAktif' => round($totalBobotAktif, 2),
                    'perkalianStr' => !empty($perkalianList) ? implode(' + ', $perkalianList) : '-',
                    'bobotStr' => !empty($bobotList) ? implode(' + ', $bobotList) : '-',
                    'nilaiAkhir' => $nilaiAkhir,
                    'huruf' => $huruf,
                    'bobotMutu' => $bobotMutu !== null ? number_format($bobotMutu, 2) : '-',
                    'predikat' => $predikat,
                    'isDemo' => false,
                ];
            })->values();

            if ($simulasiData->isEmpty()) {
                $simulasiData = collect([[
                    'id' => 1,
                    'nim' => '32240001',
                    'nama' => 'Contoh Mahasiswa (Simulasi Demo)',
                    'jenis_kelas' => 'Reguler',
                    'komponens' => [
                        ['nama' => 'Tugas', 'bobot' => 20, 'nilai' => 85.0, 'kontribusi' => 17.0, 'terisi' => true],
                        ['nama' => 'Uts', 'bobot' => 30, 'nilai' => 80.0, 'kontribusi' => 24.0, 'terisi' => true],
                        ['nama' => 'Quiz', 'bobot' => 10, 'nilai' => 90.0, 'kontribusi' => 9.0, 'terisi' => true],
                        ['nama' => 'Uas', 'bobot' => 40, 'nilai' => null, 'kontribusi' => 0, 'terisi' => false],
                    ],
                    'totalPoin' => 4900.0,
                    'totalBobotAktif' => 60.0,
                    'perkalianStr' => '(85 × 20) + (80 × 30) + (90 × 10)',
                    'bobotStr' => '20% + 30% + 10%',
                    'nilaiAkhir' => 81.67,
                    'huruf' => 'A',
                    'bobotMutu' => '4.00',
                    'predikat' => 'Sangat Baik (Istimewa)',
                    'isDemo' => true,
                ]]);
            }
        @endphp

        <div x-data="{
            selectedId: {{ $simulasiData->first()['id'] ?? 0 }},
            students: {{ Js::from($simulasiData) }},
            get student() {
                return this.students.find(s => s.id == this.selectedId) || this.students[0];
            }
        }" style="background: #ffffff; border: 1.5px solid #c7d2fe; border-radius: 14px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 4px 16px rgba(79, 70, 229, 0.06);">
            {{-- Header Simulasi --}}
            <div style="padding: 1rem 1.25rem; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-bottom: 1px solid #e0e7ff; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="16" y1="14" x2="16" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="16" y1="18" x2="16" y2="18.01"/><line x1="12" y1="18" x2="12" y2="18.01"/><line x1="8" y1="18" x2="8" y2="18.01"/><line x1="8" y1="10" x2="16" y2="10"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Simulasi &amp; Bedah Rumus Nilai Real-Time</h3>
                            <span style="background: #e0e7ff; color: #4338ca; font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.5rem; border-radius: 999px;">Live OBE Engine</span>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0;">Pilih salah satu mahasiswa di samping untuk melihat pembuktian kalkulasi formula OBE langkah demi langkah secara nyata.</p>
                    </div>
                </div>

                {{-- Selector Mahasiswa --}}
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #4338ca; white-space: nowrap;">Pilih Mahasiswa:</label>
                    <select x-model="selectedId" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; font-weight: 600; border-radius: 8px; border: 1.5px solid #6366f1; background: #ffffff; color: #1e293b; min-width: 280px; max-width: 360px; cursor: pointer; box-shadow: 0 1px 3px rgba(99, 102, 241, 0.2);">
                        <template x-for="s in students" :key="s.id">
                            <option :value="s.id" x-text="`${s.nim} - ${s.nama} (${s.nilaiAkhir !== null ? s.nilaiAkhir + ' [' + s.huruf + ']' : 'Belum Dinilai'})`"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Body Simulasi --}}
            <div style="padding: 1.25rem;">
                {{-- 3 Kartu Ringkasan Atas --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                    {{-- Kartu Mahasiswa --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.9rem 1rem; display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; flex-shrink: 0;" x-text="student.nama ? student.nama.charAt(0).toUpperCase() : 'M'"></div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Mahasiswa Terpilih</div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="student.nama"></div>
                            <div style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem;">
                                <span>NIM: <strong x-text="student.nim"></strong></span>
                                <span>&bull;</span>
                                <span style="background: #ecfdf5; color: #047857; font-weight: 600; padding: 0.05rem 0.4rem; border-radius: 4px; font-size: 0.68rem;" x-text="student.jenis_kelas"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Kartu Status Bobot Aktif --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.9rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Progres Evaluasi RPS</span>
                            <span style="font-size: 0.82rem; font-weight: 700; color: #2563eb;" x-text="student.totalBobotAktif + '% / 100%'"></span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; margin-bottom: 0.4rem;">
                            <div :style="`width: ${student.totalBobotAktif}%; background: linear-gradient(90deg, #3b82f6, #6366f1); height: 100%; border-radius: 999px;`"></div>
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b;" x-text="student.totalBobotAktif < 100 ? `${student.totalBobotAktif}% bobot RPS sudah terisi, ${100 - student.totalBobotAktif}% belum dinilai.` : 'Semua komponen penilaian RPS telah lengkap.'"></div>
                    </div>

                    {{-- Kartu Hasil Nilai & Huruf --}}
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 0.9rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div>
                            <div style="font-size: 0.72rem; color: #166534; font-weight: 600; text-transform: uppercase;">Hasil Kalkulasi Real-Time</div>
                            <div style="display: flex; align-items: baseline; gap: 0.4rem; margin-top: 0.15rem;">
                                <span style="font-size: 1.65rem; font-weight: 800; color: #15803d; line-height: 1;" x-text="student.nilaiAkhir !== null ? student.nilaiAkhir : '-'"></span>
                                <span style="font-size: 0.8rem; color: #166534; font-weight: 600;">/ 100</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #15803d; margin-top: 0.2rem;" x-text="student.predikat"></div>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: #15803d; color: #fff; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 800; font-size: 1.15rem; display: inline-block; box-shadow: 0 2px 4px rgba(21, 128, 61, 0.2);" x-text="student.huruf"></span>
                            <div style="font-size: 0.7rem; color: #166534; font-weight: 600; margin-top: 0.25rem;" x-text="`Bobot ${student.bobotMutu}`"></div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Komponen Mahasiswa Ini --}}
                <div style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 1.25rem;">
                    <div style="background: #f8fafc; padding: 0.6rem 1rem; border-bottom: 1px solid #e2e8f0; font-size: 0.78rem; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center;">
                        <span>Rincian Nilai Komponen Mahasiswa: <strong x-text="student.nama"></strong></span>
                        <span style="font-size: 0.72rem; color: #64748b; font-weight: 400;">Poin = (Nilai &times; Bobot RPS)</span>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.78rem;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #475569; text-align: left;">
                                <th style="padding: 0.5rem 0.85rem; border-bottom: 1px solid #e2e8f0;">Komponen Penilaian</th>
                                <th style="padding: 0.5rem 0.85rem; border-bottom: 1px solid #e2e8f0; text-align: center;">Bobot RPS</th>
                                <th style="padding: 0.5rem 0.85rem; border-bottom: 1px solid #e2e8f0; text-align: center;">Nilai Mahasiswa</th>
                                <th style="padding: 0.5rem 0.85rem; border-bottom: 1px solid #e2e8f0; text-align: center;">Status Komponen</th>
                                <th style="padding: 0.5rem 0.85rem; border-bottom: 1px solid #e2e8f0; text-align: right;">Poin Tertimbang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="c in student.komponens" :key="c.nama">
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 0.55rem 0.85rem; font-weight: 600; color: #1e293b;" x-text="c.nama"></td>
                                    <td style="padding: 0.55rem 0.85rem; text-align: center; color: #475569;" x-text="`${c.bobot}%`"></td>
                                    <td style="padding: 0.55rem 0.85rem; text-align: center;">
                                        <template x-if="c.terisi">
                                            <span style="font-weight: 700; color: #059669;" x-text="c.nilai"></span>
                                        </template>
                                        <template x-if="!c.terisi">
                                            <span style="color: #94a3b8; font-style: italic;">Belum diinput</span>
                                        </template>
                                    </td>
                                    <td style="padding: 0.55rem 0.85rem; text-align: center;">
                                        <template x-if="c.terisi">
                                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.1rem 0.45rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Aktif (Dihitung)</span>
                                        </template>
                                        <template x-if="!c.terisi">
                                            <span style="background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; padding: 0.1rem 0.45rem; border-radius: 4px; font-size: 0.7rem;">Dikecualikan (Belum Ada)</span>
                                        </template>
                                    </td>
                                    <td style="padding: 0.55rem 0.85rem; text-align: right; font-weight: 600;">
                                        <template x-if="c.terisi">
                                            <span style="color: #1e40af;" x-text="`${c.nilai} × ${c.bobot} = ${(c.nilai * c.bobot).toFixed(0)}`"></span>
                                        </template>
                                        <template x-if="!c.terisi">
                                            <span style="color: #cbd5e1;">-</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Kotak Bedah Rumus Langkah demi Langkah --}}
                <div style="background: #f8fafc; border: 1.5px dashed #818cf8; border-radius: 10px; padding: 1.15rem; font-size: 0.82rem; color: #334155;">
                    <div style="font-weight: 700; color: #4338ca; margin-bottom: 0.75rem; font-size: 0.88rem; display: flex; align-items: center; gap: 0.4rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Bedah Langkah Perhitungan Nilai (Bahan Penjelasan Presentasi):
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; line-height: 1.55;">
                        {{-- Langkah 1 --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem;">
                            <strong style="color: #1e293b;">Langkah 1: Hitung Total Poin dari Komponen yang Sudah Diisi</strong>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem;">Kalikan setiap nilai komponen yang ada dengan bobot persentasenya masing-masing:</div>
                            <div style="margin-top: 0.35rem; font-family: monospace; font-size: 0.82rem; color: #1e40af; background: #eff6ff; padding: 0.35rem 0.6rem; border-radius: 4px;">
                                Total Poin = <span x-text="student.perkalianStr"></span> = <strong style="font-size: 0.9rem;" x-text="student.totalPoin"></strong>
                            </div>
                        </div>

                        {{-- Langkah 2 --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem;">
                            <strong style="color: #1e293b;">Langkah 2: Tentukan Total Bobot Komponen Aktif (Sebagai Pembagi)</strong>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem;">Hanya jumlahkan bobot dari komponen yang sudah dinilai. Komponen yang belum berlangsung dikecualikan agar nilai sementara mahasiswa tidak anjlok ke nilai E:</div>
                            <div style="margin-top: 0.35rem; font-family: monospace; font-size: 0.82rem; color: #047857; background: #ecfdf5; padding: 0.35rem 0.6rem; border-radius: 4px;">
                                Total Bobot Aktif = <span x-text="student.bobotStr"></span> = <strong style="font-size: 0.9rem;" x-text="`${student.totalBobotAktif}%`"></strong>
                            </div>
                        </div>

                        {{-- Langkah 3 --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem;">
                            <strong style="color: #1e293b;">Langkah 3: Hitung Nilai Akhir (Skala 0 - 100)</strong>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem;">Bagi Total Poin dengan Total Bobot Aktif:</div>
                            <div style="margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-family: monospace; font-size: 0.82rem; color: #4338ca; background: #e0e7ff; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 700;">
                                    Nilai Akhir = <span x-text="student.totalPoin"></span> &divide; <span x-text="student.totalBobotAktif"></span> = <span style="font-size: 1rem; color: #1e1b4b;" x-text="student.nilaiAkhir !== null ? student.nilaiAkhir : '-'"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Langkah 4 --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem;">
                            <strong style="color: #1e293b;">Langkah 4: Konversi ke Abjad Huruf Mutu &amp; Bobot KHS</strong>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem;">Cocokkan nilai akhir ke tabel konversi standar akademik:</div>
                            <div style="margin-top: 0.35rem; font-size: 0.8rem; color: #1e293b;">
                                Nilai <strong x-text="student.nilaiAkhir"></strong> dikonversi menjadi <strong style="color: #15803d; font-size: 0.95rem;" x-text="`Grade ${student.huruf}`"></strong> dengan Bobot Mutu <strong x-text="student.bobotMutu"></strong> (<span style="color: #475569;" x-text="student.predikat"></span>).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Informasi Formula Penilaian & Konversi Abjad --}}
        <div x-data="{ openGuide: true }" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div @click="openGuide = !openGuide" style="padding: 0.9rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: #e0e7ff; color: #4338ca; font-size: 0.85rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </span>
                    <div>
                        <strong style="font-size: 0.88rem; color: #1e293b;">Panduan Formula Penilaian &amp; Konversi Abjad Nilai Mutu (KRS/KHS)</strong>
                        <div style="font-size: 0.72rem; color: #64748b;">Klik untuk melihat/sembunyikan penjelasan rumus OBE, bobot RPS, dan standar abjad</div>
                    </div>
                </div>
                <svg :style="openGuide ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s; color: #64748b;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>

            <div x-show="openGuide" style="padding: 1.25rem; font-size: 0.82rem; color: #334155;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                    {{-- Kolom 1: Formula Perhitungan --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            1. Formula Nilai Akhir (OBE)
                        </div>
                        <div style="font-size: 0.76rem; color: #475569; line-height: 1.55;">
                            <p style="margin: 0 0 0.4rem;">
                                <strong>Nilai Tugas:</strong> Rata-rata nilai submisi tugas dikali bobot tugas masing-masing:
                                <br><code>Nilai Tugas = &Sigma;(Nilai &times; Bobot Tugas) &divide; &Sigma;Bobot Tugas</code>
                            </p>
                            <p style="margin: 0 0 0.4rem;">
                                <strong>Nilai Akhir:</strong> Rata-rata tertimbang komponen aktif dari RPS:
                                <br><code>Nilai Akhir = &Sigma;(Nilai Komponen &times; Bobot RPS) &divide; &Sigma;Bobot Terisi</code>
                            </p>
                            <div style="background: #eff6ff; border-left: 3px solid #3b82f6; padding: 0.4rem 0.6rem; border-radius: 4px; font-size: 0.72rem; color: #1e40af; margin-top: 0.4rem;">
                                💡 <em>Normalisasi OBE:</em> Jika UAS belum berlangsung, pembagi disesuaikan hanya dengan bobot yang sudah terisi sehingga nilai sementara mahasiswa tetap adil dan proporsional.
                            </div>
                        </div>
                    </div>

                    {{-- Kolom 2: Bobot RPS & Presensi --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            2. Bobot RPS &amp; Aspek Keaktifan
                        </div>
                        <div style="font-size: 0.76rem; color: #475569; line-height: 1.55;">
                            <div style="margin-bottom: 0.5rem;">
                                <strong>Bobot RPS Mata Kuliah Ini:</strong>
                                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.3rem;">
                                    @foreach($bobot as $komp => $persen)
                                        @if($persen > 0)
                                            <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">
                                                {{ ucfirst($komp) }}: {{ $persen }}%
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <p style="margin: 0 0 0.35rem;">
                                <strong>Presensi:</strong> Berfungsi memantau kehadiran 16 sesi (rekam jejak kedisiplinan dan monitoring kelas).
                            </p>
                            <p style="margin: 0;">
                                <strong>Keaktifan &amp; Sikap (Sopan-Santun):</strong> Dosen dapat mengalokasikan penilaian keaktifan diskusi/partisipasi kelas ke dalam slot <code>Quiz</code> atau <code>Project/Tugas</code>.
                            </p>
                        </div>
                    </div>

                    {{-- Kolom 3: Tabel Konversi Abjad Mutu --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            3. Standar Abjad &amp; Bobot Mutu (KRS/KHS)
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.73rem; text-align: center;">
                                <thead>
                                    <tr style="background: #e2e8f0; color: #334155;">
                                        <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Rentang</th>
                                        <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Huruf</th>
                                        <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Bobot</th>
                                        <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">&ge; 80.00</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #059669;">A</td><td style="border: 1px solid #e2e8f0;">4.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Sangat Baik</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">75.00 - 79.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">B+</td><td style="border: 1px solid #e2e8f0;">3.50</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Antara A &amp; B</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">70.00 - 74.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">B</td><td style="border: 1px solid #e2e8f0;">3.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Baik</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">65.00 - 69.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ca8a04;">C+</td><td style="border: 1px solid #e2e8f0;">2.50</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Antara B &amp; C</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">60.00 - 64.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ca8a04;">C</td><td style="border: 1px solid #e2e8f0;">2.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Cukup (Lulus)</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">50.00 - 59.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ea580c;">D</td><td style="border: 1px solid #e2e8f0;">1.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Kurang (Remedi)</td></tr>
                                    <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">&lt; 50.00</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #dc2626;">E</td><td style="border: 1px solid #e2e8f0;">0.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Tidak Lulus</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="table-container" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($tugasList as $tugas)
                            <th style="text-align: center; font-size: 0.7rem;">{{ Str::limit($tugas->judul, 15) }}</th>
                        @endforeach
                        <th style="text-align: center; font-weight: 700;">Nilai Tugas</th>
                        @foreach($bobot as $komponen => $persen)
                            @if($komponen !== 'tugas' && $persen > 0)
                                <th style="text-align: center; font-size: 0.7rem;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small></th>
                            @endif
                        @endforeach
                        <th style="text-align: center; font-weight: 700; background: #f8fafc;">Nilai Angka</th>
                        <th style="text-align: center; font-weight: 700; background: #f8fafc;">Huruf Mutu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengampu->mahasiswas->sortBy('nim') as $mahasiswa)
                        @php
                            $nilaiTugas = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'tugas')?->nilai;
                            $nilaiAkhir = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $mahasiswa->nim }}</td>
                            <td>{{ $mahasiswa->nama }}</td>
                            @foreach($tugasList as $tugas)
                                @php
                                    $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                                    $nilaiSub = $submission?->nilai;
                                @endphp
                                <td style="text-align: center;">
                                    @if($nilaiSub !== null)
                                        <span style="font-weight: 600; color: {{ $nilaiSub >= 60 ? '#059669' : '#dc2626' }};">{{ $nilaiSub }}</span>
                                    @elseif($submission)
                                        <span style="color: #d97706; font-size: 0.75rem;">Blm Dinilai</span>
                                    @else
                                        <span style="color: #cbd5e1;">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td style="text-align: center; font-weight: 700;">
                                {{ $nilaiTugas !== null ? number_format($nilaiTugas, 2) : '-' }}
                            </td>
                            @foreach($bobot as $komponen => $persen)
                                @if($komponen !== 'tugas' && $persen > 0)
                                    @php
                                        $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                                    @endphp
                                    <td style="text-align: center;">
                                        {{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2) : '-' }}
                                    </td>
                                @endif
                            @endforeach
                            <td style="text-align: center; font-weight: 700; background: #f8fafc;">
                                {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}
                            </td>
                            <td style="text-align: center; background: #f8fafc;">
                                @if($nilaiAkhir !== null)
                                    @php
                                        $huruf = konversiNilaiHuruf($nilaiAkhir);
                                        $bobotM = konversiBobotMutu($nilaiAkhir);
                                        $badgeStyle = match($huruf) {
                                            'A' => 'background: #ecfdf5; color: #059669; border-color: #a7f3d0;',
                                            'B+', 'B' => 'background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;',
                                            'C+', 'C' => 'background: #fefce8; color: #a16207; border-color: #fde047;',
                                            'D' => 'background: #fff7ed; color: #c2410c; border-color: #fdba74;',
                                            default => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                                        };
                                    @endphp
                                    <span style="{{ $badgeStyle }} border-width: 1px; border-style: solid; padding: 0.15rem 0.55rem; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span>{{ $huruf }}</span>
                                        <span style="font-size: 0.7rem; opacity: 0.85;">({{ number_format($bobotM, 2) }})</span>
                                    </span>
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $tugasList->count() + collect($bobot)->except('tugas')->filter()->count() }}" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Form Input Nilai Komponen --}}
        @if($pengampu->mahasiswas->isNotEmpty())
            <div class="card" style="margin-top: 1.5rem; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #1e293b;">
                    Input Nilai Komponen (Quiz / UTS / UAS / Praktikum / Project)
                </div>
                <form action="{{ route('lms.tugas.komponen', $pengampu->id) }}" method="POST">
                    @csrf
                    <div class="table-container" style="border: none; margin: 0;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Quiz</th>
                                    <th>UTS</th>
                                    <th>UAS</th>
                                    <th>Praktikum</th>
                                    <th>Project</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengampu->mahasiswas->sortBy('nim') as $mahasiswa)
                                    @php
                                        $records = $nilaiByMhs->get($mahasiswa->id, collect());
                                    @endphp
                                    <tr>
                                        <td>{{ $mahasiswa->nim }}</td>
                                        <td>{{ $mahasiswa->nama }}</td>
                                        @foreach(['quiz', 'uts', 'uas', 'praktikum', 'project'] as $komp)
                                            <td>
                                                <input type="number"
                                                       name="nilai[{{ $mahasiswa->id }}][{{ $komp }}]"
                                                       class="form-input"
                                                       style="width: 80px; padding: 0.3rem 0.5rem; font-size: 0.85rem;"
                                                       min="0" max="100" step="0.01"
                                                       value="{{ old("nilai.{$mahasiswa->id}.{$komp}", $records->firstWhere('komponen', $komp)?->nilai) }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 1rem 1.25rem; border-top: 1px solid #e2e8f0; text-align: right; background: #f8fafc;">
                        <button type="submit" class="btn btn-success">Simpan Nilai Komponen</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection
