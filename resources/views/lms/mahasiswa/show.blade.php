@extends('layouts.app')

@section('content')

{{-- Header Banner (Google Classroom Style) --}}
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
            Orang ({{ $pengampu->mahasiswas->count() }})
        </button>

        <button type="button" @click="tab = 'nilai'; history.replaceState(null, null, '?tab=nilai')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'nilai' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Nilai
        </button>

        <button type="button" @click="tab = 'kehadiran'; history.replaceState(null, null, '?tab=kehadiran')" 
            class="btn btn-secondary btn-sm"
            :style="tab === 'kehadiran' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600;' : ''">
            Kehadiran
        </button>

        <a href="{{ route('mahasiswa.lms.index') }}" class="btn btn-secondary btn-sm" style="margin-left: auto;">
            Kembali ke Kelas Saya
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
                        @php $sub = $submissions->get($tugas->id); @endphp
                        <div style="padding: 0.6rem 0.5rem; border-bottom: 1px solid #f1f5f9; border-radius: 6px; cursor: pointer; transition: background 0.15s;" 
                            onclick="window.location.href='{{ route('mahasiswa.lms.tugas.show', [$pengampu->id, $tugas->id]) }}'"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                <a href="{{ route('mahasiswa.lms.tugas.show', [$pengampu->id, $tugas->id]) }}" @click.stop style="font-size: 0.85rem; font-weight: 600; color: #1e293b; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                    {{ $tugas->judul }}
                                </a>
                                @if($sub)
                                    <span style="font-size: 0.65rem; color: #059669; font-weight: 600; background: #ecfdf5; padding: 0.1rem 0.4rem; border-radius: 4px; flex-shrink: 0;">Selesai</span>
                                @endif
                            </div>
                            <div style="font-size: 0.75rem; color: #ef4444; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.3rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Tenggat: {{ $tugas->deadline->format('d M, H:i') }}
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.5rem 0 0;">Hore, tidak ada tugas yang mendekati tenggat waktu!</p>
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
                            Pengumuman ({{ $pengumumans->count() }})
                        </h3>
                    </div>

                    @forelse($pengumumans as $pengumuman)
                        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $pengumuman->judul }}</div>
                            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.1rem;">
                                {{ $pengampu->dosen?->user?->name ?? 'Dosen' }} &middot; {{ $pengumuman->published_at?->format('d M, H:i') ?? $pengumuman->created_at->format('d M, H:i') }}
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
                    <form action="{{ route('mahasiswa.lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                        @csrf
                        <textarea name="pesan" class="form-textarea" rows="2" placeholder="Tulis pesan atau pertanyaan di forum..." required style="background: #fff;"></textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <input type="file" name="file" class="form-input" style="max-width: 250px; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <button type="submit" class="btn btn-primary btn-sm">Kirim Diskusi</button>
                        </div>
                    </form>

                    {{-- List Thread Diskusi --}}
                    @forelse($pengampu->lmsForumDiskusis as $post)
                        <div x-data="{ editPost: false }" style="border-bottom: 1px solid #f1f5f9; padding: 1rem 0;">
                            <div style="display: flex; gap: 0.75rem;">
                                <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                                    {{ substr($post->user->name ?? '?', 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $post->user->name ?? '-' }}</span>
                                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $post->created_at->diffForHumans() }}</span>
                                        @if($post->user?->isDosen())
                                            <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.4rem; font-size: 0.65rem; font-weight: 600;">Dosen</span>
                                        @endif
                                    </div>
                                    <div x-show="!editPost" style="font-size: 0.85rem; color: #334155; margin-top: 0.35rem; line-height: 1.6; white-space: pre-wrap;">{!! linkify($post->pesan) !!}</div>

                                    @if($post->file_path)
                                        <div x-show="!editPost" style="margin-top: 0.5rem;">
                                            <a href="{{ route('mahasiswa.lms.file', ['forum', $post->id]) }}" target="_blank" class="btn btn-secondary btn-xs" style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                                {{ basename($post->file_path) }}
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Actions (30-min limit) --}}
                                    @if(Auth::id() === $post->user_id && $post->isWithinTimeLimit(30))
                                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                            <button type="button" @click="editPost = !editPost" class="btn btn-secondary btn-xs">
                                                <span x-text="editPost ? 'Batal' : 'Ubah'">Ubah</span>
                                            </button>
                                            <form action="{{ route('mahasiswa.lms.forum.destroy', [$pengampu->id, $post->id]) }}" method="POST" onsubmit="return confirm('Hapus pesan ini? Semua balasan terkait juga akan terhapus.');" style="margin: 0;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                            </form>
                                        </div>

                                        <form x-show="editPost" action="{{ route('mahasiswa.lms.forum.update', [$pengampu->id, $post->id]) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                            @csrf @method('PATCH')
                                            <textarea name="pesan" class="form-textarea" style="min-height: 80px;" required>{{ old('pesan', $post->pesan) }}</textarea>
                                            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                                <input type="file" name="file" class="form-input" style="flex: 1; min-width: 200px;">
                                                @if($post->file_path)
                                                    <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: #64748b;">
                                                        <input type="checkbox" name="remove_file" value="1"> Hapus file
                                                    </label>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-xs" style="margin-top: 0.5rem;">Simpan</button>
                                        </form>
                                    @endif

                                    {{-- Balasan Thread --}}
                                    @if($post->replies->count())
                                        <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 2px solid #e2e8f0; display: flex; flex-direction: column; gap: 0.75rem;">
                                            @foreach($post->replies as $reply)
                                                <div x-data="{ editReply: false }" style="display: flex; gap: 0.6rem;">
                                                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 50%; background: #64748b; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.65rem; flex-shrink: 0;">
                                                        {{ substr($reply->user->name ?? '?', 0, 2) }}
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div style="font-size: 0.8rem; font-weight: 600; color: #1e293b;">
                                                            {{ $reply->user->name ?? '-' }}
                                                            <span style="font-weight: 400; color: #94a3b8; font-size: 0.7rem; margin-left: 0.3rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                            @if($reply->user?->isDosen())
                                                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.05rem 0.35rem; font-size: 0.6rem; font-weight: 600; margin-left: 0.3rem;">Dosen</span>
                                                            @endif
                                                        </div>
                                                        <div x-show="!editReply" style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; white-space: pre-wrap;">{!! linkify($reply->pesan) !!}</div>
                                                        @if($reply->file_path)
                                                            <div x-show="!editReply" style="margin-top: 0.3rem;">
                                                                <x-file-link :file="$reply->file_path" compact :href="route('mahasiswa.lms.file', ['forum', $reply->id])" />
                                                            </div>
                                                        @endif

                                                        @if(Auth::id() === $reply->user_id && $reply->isWithinTimeLimit(30))
                                                            <div style="display: flex; gap: 0.4rem; margin-top: 0.3rem;">
                                                                <button type="button" @click="editReply = !editReply" class="btn btn-secondary btn-xs">
                                                                    <span x-text="editReply ? 'Batal' : 'Ubah'">Ubah</span>
                                                                </button>
                                                                <form action="{{ route('mahasiswa.lms.forum.destroy', [$pengampu->id, $reply->id]) }}" method="POST" onsubmit="return confirm('Hapus balasan ini?');" style="margin: 0;">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                                                </form>
                                                            </div>

                                                            <form x-show="editReply" action="{{ route('mahasiswa.lms.forum.update', [$pengampu->id, $reply->id]) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.4rem; padding: 0.6rem; background: #f8fafc; border-radius: 8px;">
                                                                @csrf @method('PATCH')
                                                                <input type="text" name="pesan" class="form-input" value="{{ old('pesan', $reply->pesan) }}" required style="padding: 0.35rem 0.6rem; font-size: 0.8rem;">
                                                                <div style="display: flex; gap: 0.75rem; margin-top: 0.4rem; flex-wrap: wrap;">
                                                                    <input type="file" name="file" class="form-input" style="flex: 1; min-width: 160px;">
                                                                    @if($reply->file_path)
                                                                        <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: #64748b;">
                                                                            <input type="checkbox" name="remove_file" value="1"> Hapus file
                                                                        </label>
                                                                    @endif
                                                                </div>
                                                                <button type="submit" class="btn btn-primary btn-xs" style="margin-top: 0.4rem;">Simpan</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Form Balas --}}
                                    <form action="{{ route('mahasiswa.lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.75rem;">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <input type="text" name="pesan" class="form-input" placeholder="Tulis balasan..." required style="flex: 1; min-width: 160px; padding: 0.35rem 0.6rem; font-size: 0.8rem;">
                                            <input type="file" name="file" class="form-input" style="max-width: 160px; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                            <button type="submit" class="btn btn-secondary btn-sm">Balas</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 2rem 0;">Belum ada percakapan forum. Mulai diskusi kelas!</p>
                    @endforelse
                </div>
            </div>
        </div>

    {{-- TAB 2: TUGAS KELAS (CLASSWORK) --}}
    <div x-show="tab === 'tugas_kelas'">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem;">Tugas & Materi Perkuliahan</h2>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Akses materi perkuliahan dan kumpulkan tugas yang ditugaskan.</p>
            </div>
        </div>

        {{-- Unified Stream of Classwork --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @php
                $allClasswork = collect();
                foreach($pengampu->lmsMateris as $m) {
                    $isSelesai = $materiSelesai->has($m->id);
                    $allClasswork->push((object)[
                        'type' => 'materi',
                        'id' => $m->id,
                        'title' => $m->judul,
                        'pertemuan' => $m->rpsPertemuan?->minggu_ke ?? null,
                        'deadline' => null,
                        'is_selesai' => $isSelesai,
                        'created_at' => $m->created_at,
                        'url' => route('mahasiswa.lms.materi.show', [$pengampu->id, $m->id]),
                        'obj' => $m
                    ]);
                }
                foreach($pengampu->lmsTugas as $t) {
                    $sub = $submissions->get($t->id);
                    $allClasswork->push((object)[
                        'type' => 'tugas',
                        'id' => $t->id,
                        'title' => $t->judul,
                        'pertemuan' => $t->rpsPertemuan?->minggu_ke ?? null,
                        'deadline' => $t->deadline,
                        'submission' => $sub,
                        'created_at' => $t->created_at,
                        'url' => route('mahasiswa.lms.tugas.show', [$pengampu->id, $t->id]),
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
                            </div>
                        </div>
                    </div>

                    {{-- Status Tugas / Materi & Tenggat & Kebab Menu --}}
                    <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                        @if($item->type === 'materi')
                            @if($item->is_selesai)
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Selesai
                                </span>
                            @endif
                        @else
                            {{-- Status Tugas Mahasiswa --}}
                            @if($item->submission)
                                <span style="background: {{ $item->submission->isTerlambat() ? '#fef2f2' : '#ecfdf5' }}; color: {{ $item->submission->isTerlambat() ? '#dc2626' : '#059669' }}; border: 1px solid {{ $item->submission->isTerlambat() ? '#fecaca' : '#a7f3d0' }}; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                                    {{ $item->submission->isTerlambat() ? 'Terlambat' : 'Diserahkan' }}
                                </span>
                            @elseif($item->deadline && $item->deadline->isPast())
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                                    Terlewat
                                </span>
                            @else
                                <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600;">
                                    Ditugaskan
                                </span>
                            @endif

                            @if($item->deadline)
                                <div style="font-size: 0.8rem; color: #64748b; text-align: right;">
                                    <span style="display: block; font-size: 0.7rem; color: #94a3b8;">Tenggat</span>
                                    {{ $item->deadline->format('d M, H:i') }}
                                </div>
                            @endif
                        @endif

                        <div style="position: relative;" @click.stop>
                            <button type="button" @click="openMenu = !openMenu" @click.outside="openMenu = false" style="background: none; border: none; padding: 0.4rem; cursor: pointer; color: #64748b; border-radius: 50%;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>

                            <div x-show="openMenu" style="position: absolute; right: 0; top: 100%; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; min-width: 140px; z-index: 20; padding: 0.35rem 0; display: none;">
                                <button type="button" @click="copyLink('{{ $item->url }}'); openMenu = false;" style="width: 100%; text-align: left; padding: 0.5rem 0.85rem; font-size: 0.8rem; background: none; border: none; cursor: pointer; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    Salin Link
                                </button>
                                <a href="{{ $item->url }}" style="width: 100%; text-align: left; padding: 0.5rem 0.85rem; font-size: 0.8rem; background: none; border: none; cursor: pointer; color: #1e293b; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Buka Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 3rem; text-align: center;">
                    <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Belum ada materi atau tugas yang diberikan pada kelas ini.</p>
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

            {{-- Mahasiswa / Teman Sekelas --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2563eb; padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e3a8a; margin: 0;">
                        Teman Sekelas
                    </h2>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">{{ $pengampu->mahasiswas->count() }} mahasiswa</span>
                </div>

                <div style="display: flex; flex-direction: column;">
                    @forelse($pengampu->mahasiswas->sortBy('nim') as $mhs)
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;">
                                {{ substr($mhs->nama ?? $mhs->user?->name ?? 'M', 0, 2) }}
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">
                                    {{ $mhs->nama ?? $mhs->user?->name }}
                                    @if($mhs->id === Auth::user()->mahasiswa?->id)
                                        <span style="background: #eff6ff; color: #2563eb; font-size: 0.65rem; font-weight: 600; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem;">Saya</span>
                                    @endif
                                </div>
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

    {{-- Tab: Nilai --}}
    <div x-show="tab === 'nilai'">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Rekap Nilai</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Komponen</th>
                            <th style="text-align: center;">Bobot</th>
                            <th style="text-align: center;">Nilai</th>
                            <th style="text-align: center;">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bobot as $komponen => $persen)
                            @if($persen > 0)
                                @php
                                    $nilaiKomponen = $nilaiByKomponen->get($komponen)?->nilai;
                                    $kontribusi = $nilaiKomponen !== null ? round($nilaiKomponen * $persen / 100, 2) : null;
                                @endphp
                                <tr>
                                    <td style="text-transform: capitalize;">{{ $komponen }}</td>
                                    <td style="text-align: center;">{{ $persen }}%</td>
                                    <td style="text-align: center;">{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2) : '-' }}</td>
                                    <td style="text-align: center;">{{ $kontribusi !== null ? number_format($kontribusi, 2) : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $nilaiAkhir = $nilaiByKomponen->get('akhir')?->nilai;
                            $hurufAkhir = konversiNilaiHuruf($nilaiAkhir);
                            $bobotAkhir = konversiBobotMutu($nilaiAkhir);
                            $predikatAkhir = predikatNilai($nilaiAkhir);
                            $badgeStyle = match($hurufAkhir) {
                                'A' => 'background: #ecfdf5; color: #059669; border-color: #a7f3d0;',
                                'B+', 'B' => 'background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;',
                                'C+', 'C' => 'background: #fefce8; color: #a16207; border-color: #fde047;',
                                'D' => 'background: #fff7ed; color: #c2410c; border-color: #fdba74;',
                                default => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                            };
                        @endphp
                        <tr style="background: #f8fafc;">
                            <td colspan="2" style="text-align: right; font-weight: 700; color: #1e293b;">Nilai Akhir (Skala 100)</td>
                            <td style="text-align: center; font-weight: 800; font-size: 1rem; color: #1e293b;">
                                {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}
                            </td>
                            <td style="text-align: center;">
                                @if($nilaiAkhir !== null)
                                    <span style="{{ $badgeStyle }} border-width: 1px; border-style: solid; padding: 0.2rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <span>Nilai {{ $hurufAkhir }}</span>
                                        <span style="font-size: 0.72rem; opacity: 0.85;">({{ number_format($bobotAkhir, 2) }})</span>
                                    </span>
                                    <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">{{ $predikatAkhir }}</div>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.75rem;">Belum Terkalkulasi</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Penjelasan Transparansi Perhitungan Nilai Mahasiswa --}}
            <div x-data="{ showDetail: false }" style="margin-top: 1.25rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                <button type="button" @click="showDetail = !showDetail" style="background: none; border: none; padding: 0; font-size: 0.8rem; font-weight: 600; color: #2563eb; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span x-text="showDetail ? 'Sembunyikan Informasi Perhitungan Nilai & Skala Abjad' : 'Lihat Cara Perhitungan Nilai & Standar Abjad (KHS)'"></span>
                </button>

                <div x-show="showDetail" style="margin-top: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; font-size: 0.78rem; color: #475569; line-height: 1.6;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                        <div>
                            <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Cara Perhitungan Nilai:</strong>
                            <p style="margin: 0 0 0.35rem;">
                                Nilai akhir dihitung berdasarkan <strong>Rata-Rata Tertimbang</strong> bobot RPS mata kuliah ini.
                            </p>
                            <p style="margin: 0 0 0.35rem;">
                                <code>Nilai Akhir = &Sigma;(Nilai Komponen &times; Bobot RPS) &divide; &Sigma;Bobot Terisi</code>
                            </p>
                            <p style="margin: 0; font-size: 0.74rem; color: #64748b;">
                                <em>Catatan:</em> Presensi kehadiran 16 sesi digunakan sebagai rekam jejak kedisiplinan dan monitoring kelas. Nilai keaktifan dan sikap dinilai dosen pada komponen kuis/tugas partisipatif.
                            </p>
                        </div>
                        <div>
                            <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Standar Konversi Abjad &amp; Bobot Mutu:</strong>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem; font-size: 0.74rem;">
                                <div>&bull; &ge; 80.00 : <strong>A (4.00)</strong></div>
                                <div>&bull; 75.00 - 79.99 : <strong>B+ (3.50)</strong></div>
                                <div>&bull; 70.00 - 74.99 : <strong>B (3.00)</strong></div>
                                <div>&bull; 65.00 - 69.99 : <strong>C+ (2.50)</strong></div>
                                <div>&bull; 60.00 - 64.99 : <strong>C (2.00)</strong></div>
                                <div>&bull; 50.00 - 59.99 : <strong>D (1.00)</strong></div>
                                <div style="grid-column: span 2;">&bull; &lt; 50.00 : <strong style="color: #dc2626;">E (0.00)</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Kehadiran --}}
    <div x-show="tab === 'kehadiran'">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Kehadiran</h3>
            <div style="font-size: 0.85rem; color: #475569; margin-bottom: 0.75rem;">
                Hadir {{ $hadirCount }} dari {{ $totalSesi }} sesi
                @if($totalSesi > 0)
                    &middot; <strong>{{ round($hadirCount / $totalSesi * 100, 1) }}%</strong>
                @endif
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pertemuan</th>
                            <th>Materi</th>
                            <th>Tanggal</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiSesi as $sesi)
                            <tr>
                                <td>Pertemuan {{ $sesi->rpsPertemuan?->minggu ?? '-' }}</td>
                                <td>{{ $sesi->rpsPertemuan?->materi ?? '-' }}</td>
                                <td>{{ $sesi->tanggal_aktual?->format('d M Y') ?? '-' }}</td>
                                <td style="text-align: center;">
                                    @php
                                        $label = [
                                            'hadir' => ['Hadir', '#16a34a'],
                                            'sakit' => ['Sakit', '#f59e0b'],
                                            'izin' => ['Izin', '#3b82f6'],
                                            'alpa' => ['Alpa', '#dc2626'],
                                        ][$sesi->status_mahasiswa ?? 'alpa'];
                                    @endphp
                                    <span style="background: {{ $label[1] }}18; color: {{ $label[1] }}; border-radius: 999px; padding: 0.1rem 0.6rem; font-size: 0.7rem; font-weight: 600;">{{ $label[0] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: #94a3b8;">Belum ada sesi kehadiran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const maxMb = parseFloat(form.dataset.maxMb);
        if (!form.dataset.maxMb || !maxMb) return;

        const input = form.querySelector('input[name="file_jawaban"]');
        const file = input && input.files && input.files[0];

        if (file && file.size > maxMb * 1024 * 1024) {
            e.preventDefault();
            alert('Ukuran file melebihi batas maksimal ' + maxMb + ' MB.');
        }
    });
</script>

@endsection
