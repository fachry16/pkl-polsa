@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $pengampu->dosen->user->name ?? '-' }}
    </span>
</div>

<div x-data="{ tab: 'overview' }">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
        <button @click="tab = 'overview'" :class="tab === 'overview' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Beranda</button>
        <button @click="tab = 'materi'" :class="tab === 'materi' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Materi ({{ $materiCount }})</button>
        <button @click="tab = 'tugas'" :class="tab === 'tugas' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Tugas ({{ $tugasCount }})</button>
        <button @click="tab = 'forum'" :class="tab === 'forum' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Forum</button>
    </div>

    {{-- Tab: Beranda --}}
    <div x-show="tab === 'overview'">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Materi Terbaru</h3>
                @forelse($pengampu->lmsMateris->take(5) as $materi)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 0.85rem; font-weight: 500; color: #1e293b; flex: 1;">{{ $materi->judul }}</div>
                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $materi->created_at->format('d M') }}</span>
                    </div>
                @empty
                    <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada materi.</p>
                @endforelse
                @if($materiCount > 5)
                    <button @click="tab = 'materi'" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Materi</button>
                @endif
            </div>

            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Tugas Terbaru</h3>
                @forelse($pengampu->lmsTugas->take(5) as $tugas)
                    @php $sub = $submissions->get($tugas->id); @endphp
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                        <div style="flex: 1;">
                            <div style="font-size: 0.85rem; font-weight: 500; color: #1e293b;">{{ $tugas->judul }}</div>
                            <div style="font-size: 0.7rem; color: #94a3b8;">
                                Deadline: {{ $tugas->deadline->format('d M Y H:i') }}
                                @if($sub)
                                    &middot; Nilai: {{ $sub->nilai ?? '-' }}
                                @endif
                            </div>
                        </div>
                        @if($sub)
                            <span style="background: {{ $sub->isTerlambat() ? '#fef2f2' : '#ecfdf5' }}; color: {{ $sub->isTerlambat() ? '#dc2626' : '#059669' }}; border: 1px solid {{ $sub->isTerlambat() ? '#fecaca' : '#a7f3d0' }}; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.65rem; font-weight: 600; white-space: nowrap;">
                                {{ $sub->isTerlambat() ? 'Terlambat' : 'Terkumpul' }}
                            </span>
                        @else
                            <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.65rem; font-weight: 600;">Belum</span>
                        @endif
                    </div>
                @empty
                    <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada tugas.</p>
                @endforelse
                @if($tugasCount > 5)
                    <button @click="tab = 'tugas'" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Tugas</button>
                @endif
            </div>
        </div>

        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-top: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Diskusi Terbaru</h3>
            @forelse($pengampu->lmsForumDiskusis->take(5) as $forum)
                <div style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 2rem; height: 2rem; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; flex-shrink: 0;">{{ substr($forum->user->name ?? '?', 0, 2) }}</div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.8rem; font-weight: 500; color: #1e293b;">{{ $forum->user->name ?? '-' }}</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-top: 0.15rem; line-height: 1.5;">{{ Str::limit($forum->pesan, 150) }}</div>
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">{{ $forum->created_at->diffForHumans() }} &middot; {{ $forum->replies->count() }} balasan</div>
                    </div>
                </div>
            @empty
                <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada diskusi.</p>
            @endforelse
            @if($pengampu->lmsForumDiskusis->count() > 5)
                <button @click="tab = 'forum'" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Forum</button>
            @endif
        </div>
    </div>

    {{-- Tab: Materi --}}
    <div x-show="tab === 'materi'">
        @forelse($pengampu->lmsMateris as $materi)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $materi->judul }}</div>
                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $materi->created_at->format('d M Y') }}</span>
                        @if($materi->deskripsi)
                            <p style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">{{ $materi->deskripsi }}</p>
                        @endif
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            @if($materi->file_path)
                                <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">Download File</a>
                            @endif
                            @if($materi->link_external)
                                <a href="{{ $materi->link_external }}" target="_blank" class="btn btn-secondary btn-sm">Buka Link</a>
                            @endif
                        </div>
                    </div>
                </div>
                @if($materi->link_external && (str_contains($materi->link_external, 'drive.google.com') || str_contains($materi->link_external, 'youtube.com') || str_contains($materi->link_external, 'youtu.be')))
                    <div style="margin-top: 0.75rem; border-radius: 8px; overflow: hidden;">
                        <iframe src="{{ $materi->link_external }}" style="width: 100%; height: 300px; border: none;" allowfullscreen></iframe>
                    </div>
                @elseif($materi->file_path && preg_match('/\.(pdf|doc|docx|xls|xlsx|ppt|pptx)$/i', $materi->file_path))
                    <div style="margin-top: 0.75rem; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <iframe src="https://drive.google.com/viewerng/viewer?embedded=true&url={{ urlencode(Storage::url($materi->file_path)) }}" style="width: 100%; height: 300px; border: none;"></iframe>
                    </div>
                @endif
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada materi.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Tugas --}}
    <div x-show="tab === 'tugas'">
        @forelse($pengampu->lmsTugas as $tugas)
            @php $sub = $submissions->get($tugas->id); @endphp
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $tugas->judul }}</span>
                            @if($tugas->deadline->isPast())
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Tutup</span>
                            @else
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Aktif</span>
                            @endif
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;">
                            Deadline: {{ $tugas->deadline->format('d M Y H:i') }} &middot; Bobot: {{ $tugas->bobot_nilai }}
                        </div>
                        @if($tugas->instruksi)
                            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem; line-height: 1.6;">{{ $tugas->instruksi }}</div>
                        @endif

                        @if($sub)
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                <span style="background: {{ $sub->isTerlambat() ? '#fef2f2' : '#ecfdf5' }}; color: {{ $sub->isTerlambat() ? '#dc2626' : '#059669' }}; border: 1px solid {{ $sub->isTerlambat() ? '#fecaca' : '#a7f3d0' }}; border-radius: 999px; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 600;">
                                    {{ $sub->isTerlambat() ? 'Terlambat' : 'Terkumpul' }}
                                </span>
                                @if($sub->nilai !== null)
                                    <span style="font-weight: 600; font-size: 0.9rem; color: {{ $sub->nilai >= 60 ? '#059669' : '#dc2626' }};">Nilai: {{ $sub->nilai }}</span>
                                @else
                                    <span style="font-size: 0.8rem; color: #d97706;">Belum Dinilai</span>
                                @endif
                                @if($sub->catatan_dosen)
                                    <span style="font-size: 0.75rem; color: #64748b;">Catatan: {{ $sub->catatan_dosen }}</span>
                                @endif
                            </div>
                            @if($sub->file_jawaban || $sub->link_external)
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                    @if($sub->file_jawaban)
                                        <a href="{{ Storage::url($sub->file_jawaban) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Jawaban</a>
                                    @endif
                                    @if($sub->link_external)
                                        <a href="{{ $sub->link_external }}" target="_blank" class="btn btn-secondary btn-sm">Buka Link Jawaban</a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <form action="{{ route('mahasiswa.lms.tugas.kumpul', $tugas->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                @csrf
                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <input type="file" name="file_jawaban" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.png,.mp4">
                                    </div>
                                    <div style="flex: 1; min-width: 200px;">
                                        <input type="url" name="link_external" class="form-input" placeholder="Atau link Google Drive">
                                    </div>
                                </div>
                                <div style="margin-top: 0.5rem;">
                                    <input type="text" name="catatan_mahasiswa" class="form-input" placeholder="Catatan (opsional)">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">Kumpulkan Tugas</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada tugas.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Forum --}}
    <div x-show="tab === 'forum'">
        @forelse($pengampu->lmsForumDiskusis as $post)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; gap: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">{{ substr($post->user->name ?? '?', 0, 2) }}</div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $post->user->name ?? '-' }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: 0.85rem; color: #334155; margin-top: 0.35rem; line-height: 1.7; white-space: pre-wrap;">{{ $post->pesan }}</div>
                        @if($post->file_path || $post->link_external)
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                @if($post->file_path) <a href="{{ Storage::url($post->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">Download</a> @endif
                                @if($post->link_external) <a href="{{ $post->link_external }}" target="_blank" class="btn btn-secondary btn-sm">Buka Link</a> @endif
                            </div>
                        @endif
                        @if($post->replies->count())
                            <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 2px solid #e2e8f0;">
                                @foreach($post->replies as $reply)
                                    <div style="display: flex; gap: 0.6rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                                        <div style="width: 1.5rem; height: 1.5rem; border-radius: 6px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.55rem; flex-shrink: 0;">{{ substr($reply->user->name ?? '?', 0, 2) }}</div>
                                        <div style="flex: 1;">
                                            <div style="font-size: 0.75rem; font-weight: 500; color: #1e293b;">{{ $reply->user->name ?? '-' }} <span style="font-weight: 400; color: #94a3b8; font-size: 0.65rem;">{{ $reply->created_at->diffForHumans() }}</span></div>
                                            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; white-space: pre-wrap;">{{ $reply->pesan }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.5rem;">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $post->id }}">
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" name="pesan" class="form-input" style="flex: 1; padding: 0.35rem 0.6rem; font-size: 0.8rem;" placeholder="Tulis balasan..." required>
                                <button type="submit" class="btn btn-primary btn-sm">Balas</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada diskusi.</p>
            </div>
        @endforelse

        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-top: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem;">Diskusi Baru</h3>
            <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea name="pesan" class="form-textarea" style="min-height: 100px;" required placeholder="Tulis pesan diskusi..."></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <input type="file" name="file" class="form-input" style="flex: 1;">
                    <input type="url" name="link_external" class="form-input" style="flex: 1;" placeholder="Atau link eksternal">
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">Kirim Diskusi</button>
            </form>
        </div>
    </div>
</div>

@endsection
