@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $pengampu->dosen?->user?->name ?? '-' }}
    </span>
</div>

<div x-data="{ tab: '{{ request('tab', 'overview') }}' }">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
        <button @click="tab = 'overview'; history.replaceState(null, null, '?tab=overview')" :class="tab === 'overview' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Beranda</button>
        <button @click="tab = 'materi'; history.replaceState(null, null, '?tab=materi')" :class="tab === 'materi' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Materi ({{ $materiCount }})</button>
        <button @click="tab = 'tugas'; history.replaceState(null, null, '?tab=tugas')" :class="tab === 'tugas' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Tugas ({{ $tugasCount }})</button>
        <button @click="tab = 'forum'; history.replaceState(null, null, '?tab=forum')" :class="tab === 'forum' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Forum</button>
        <button @click="tab = 'pengumuman'; history.replaceState(null, null, '?tab=pengumuman')" :class="tab === 'pengumuman' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Pengumuman ({{ $pengumumans->count() }})</button>
        <button @click="tab = 'nilai'; history.replaceState(null, null, '?tab=nilai')" :class="tab === 'nilai' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Nilai</button>
        <button @click="tab = 'kehadiran'; history.replaceState(null, null, '?tab=kehadiran')" :class="tab === 'kehadiran' ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm'">Kehadiran</button>
    </div>

    {{-- Tab: Beranda --}}
    <div x-show="tab === 'overview'">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Materi Terbaru</h3>
                @forelse($pengampu->lmsMateris->take(5) as $materi)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 0.85rem; font-weight: 500; color: #1e293b; flex: 1;">{{ $materi->judul }}</div>
                        @if(isset($materiSelesai[$materi->id]))
                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Selesai</span>
                        @endif
                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $materi->created_at->format('d M') }}</span>
                    </div>
                @empty
                    <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada materi.</p>
                @endforelse
                @if($materiCount > 5)
                    <button @click="tab = 'materi'; history.replaceState(null, null, '?tab=materi')" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Materi</button>
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
                    <button @click="tab = 'tugas'; history.replaceState(null, null, '?tab=tugas')" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Tugas</button>
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
                <button @click="tab = 'forum'; history.replaceState(null, null, '?tab=forum')" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; width: 100%;">Lihat Semua Forum</button>
            @endif
        </div>
    </div>

    {{-- Tab: Materi --}}
    <div x-show="tab === 'materi'">
        @forelse($pengampu->lmsMateris as $materi)
            @php $isSelesai = isset($materiSelesai[$materi->id]); @endphp
            <div style="background: #fff; border-radius: 12px; border: 1px solid {{ $isSelesai ? '#a7f3d0' : '#e2e8f0' }}; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $materi->judul }}</span>
                            @if($isSelesai)
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Selesai</span>
                            @endif
                        </div>
                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $materi->created_at->format('d M Y') }}</span>
                        @if($materi->deskripsi)
                            <p style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">{!! linkify($materi->deskripsi) !!}</p>
                        @endif
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            @if($materi->file_path)
                                <x-file-link :file="$materi->file_path" :href="route('mahasiswa.lms.file', ['materi', $materi->id])" />
                            @endif
                            <form action="{{ route('mahasiswa.lms.materi.selesai', $materi->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="border: 1px solid {{ $isSelesai ? '#a7f3d0' : '#e2e8f0' }}; color: {{ $isSelesai ? '#059669' : '#64748b' }}; background: #fff; padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                                    {{ $isSelesai ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
                            Deadline: {{ $tugas->deadline->format('d M Y H:i') }} &middot; Bobot: {{ $tugas->bobot_nilai }} &middot; Maks. Upload: {{ $tugas->batas_upload_mb ?? 50 }} MB
                        </div>
                        @if($tugas->instruksi)
                            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem; line-height: 1.6;">{!! linkify($tugas->instruksi) !!}</div>
                        @endif
                        @if($tugas->file_lampiran)
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                <x-file-link :file="$tugas->file_lampiran" :href="route('mahasiswa.lms.file', ['tugas', $tugas->id])" />
                            </div>
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
                                    <span style="font-size: 0.75rem; color: #64748b;">Catatan: {!! linkify($sub->catatan_dosen) !!}</span>
                                @endif
                            </div>
                            @if($sub->file_jawaban)
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                    <x-file-link :file="$sub->file_jawaban" :href="route('mahasiswa.lms.file', ['submission', $sub->id])" />
                                </div>
                            @endif

                            <div x-data="{ edit: false }" style="margin-top: 0.75rem;">
                                @if(! $tugas->deadline->isPast() && $sub->nilai === null)
                                    <button type="button" @click="edit = !edit" class="btn btn-secondary btn-sm">
                                        <span x-text="edit ? 'Batal' : 'Perbarui Kiriman'">Perbarui Kiriman</span>
                                    </button>

                                    <form x-show="edit" action="{{ route('mahasiswa.lms.tugas.update', $sub->id) }}" method="POST" enctype="multipart/form-data" data-max-mb="{{ $tugas->batas_upload_mb ?? 50 }}" style="margin-top: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px; display: none;">
                                        @csrf @method('PATCH')
                                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                            <div style="flex: 1; min-width: 200px;">
                                                <label class="form-label" style="font-size: 0.75rem;">File Baru (opsional)</label>
                                                <input type="file" name="file_jawaban" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.png,.mp4">
                                                @error('file_jawaban') <div class="form-error">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div style="margin-top: 0.5rem;">
                                            <input type="text" name="catatan_mahasiswa" class="form-input" value="{{ old('catatan_mahasiswa', $sub->catatan_mahasiswa) }}" placeholder="Catatan kiriman...">
                                            @error('catatan_mahasiswa') <div class="form-error">{{ $message }}</div> @enderror
                                        </div>
                                        @if($sub->file_jawaban)
                                            <label style="display: flex; align-items: center; gap: 0.35rem; margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                                                <input type="checkbox" name="hapus_file_jawaban" value="1">
                                                Hapus file jawaban yang sudah dikumpulkan
                                            </label>
                                        @endif
                                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">Simpan Perubahan</button>
                                    </form>
                                @else
                                    <div style="font-size: 0.75rem; color: #94a3b8;">
                                        @if($sub->nilai !== null)
                                            Kiriman terkunci karena sudah dinilai.
                                        @else
                                            Kiriman terkunci karena melewati deadline.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @elseif(! $tugas->deadline->isPast())
                            <form action="{{ route('mahasiswa.lms.tugas.kumpul', $tugas->id) }}" method="POST" enctype="multipart/form-data" data-max-mb="{{ $tugas->batas_upload_mb ?? 50 }}" style="margin-top: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                @csrf
                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <input type="file" name="file_jawaban" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.png,.mp4">
                                        @error('file_jawaban') <div class="form-error">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div style="margin-top: 0.5rem;">
                                    <input type="text" name="catatan_mahasiswa" class="form-input" placeholder="Atau tulis jawaban sebagai catatan teks di sini">
                                    @error('catatan_mahasiswa') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">Kumpulkan Tugas</button>
                            </form>
                        @else
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.75rem;">Kiriman terkunci karena melewati deadline.</div>
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
            <div x-data="{ editPost: false }" style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; gap: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">{{ substr($post->user->name ?? '?', 0, 2) }}</div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $post->user->name ?? '-' }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <div x-show="!editPost" style="font-size: 0.85rem; color: #334155; margin-top: 0.35rem; line-height: 1.7; white-space: pre-wrap;">{!! linkify($post->pesan) !!}</div>
                        @if($post->file_path)
                            <div x-show="!editPost" style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                <x-file-link :file="$post->file_path" :href="route('mahasiswa.lms.file', ['forum', $post->id])" />
                            </div>
                        @endif

                        @if($post->user_id === auth()->id())
                            <form x-show="editPost" action="{{ route('mahasiswa.lms.forum.update', [$pengampu->id, $post->id]) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                @csrf @method('PATCH')
                                <textarea name="pesan" class="form-textarea" style="min-height: 80px;" required>{{ old('pesan', $post->pesan) }}</textarea>
                                @error('pesan') <div class="form-error">{{ $message }}</div> @enderror
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

                        @if($post->replies->count())
                            <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 2px solid #e2e8f0;">
                                @foreach($post->replies as $reply)
                                    <div x-data="{ editReply: false }" style="display: flex; gap: 0.6rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                                        <div style="width: 1.5rem; height: 1.5rem; border-radius: 6px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.55rem; flex-shrink: 0;">{{ substr($reply->user->name ?? '?', 0, 2) }}</div>
                                        <div style="flex: 1;">
                                            <div style="font-size: 0.75rem; font-weight: 500; color: #1e293b;">{{ $reply->user->name ?? '-' }} <span style="font-weight: 400; color: #94a3b8; font-size: 0.65rem;">{{ $reply->created_at->diffForHumans() }}</span></div>
                                            <div x-show="!editReply" style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; white-space: pre-wrap;">{!! linkify($reply->pesan) !!}</div>
                                            @if($reply->file_path)
                                                <div x-show="!editReply" style="margin-top: 0.3rem;">
                                                    <x-file-link :file="$reply->file_path" compact :href="route('mahasiswa.lms.file', ['forum', $reply->id])" />
                                                </div>
                                            @endif
                                            @if($reply->user_id === auth()->id())
                                                <div style="display: flex; gap: 0.5rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                                    <button type="button" @click="editReply = !editReply" class="btn btn-secondary btn-xs">
                                                        <span x-text="editReply ? 'Batal' : 'Ubah'">Ubah</span>
                                                    </button>
                                                    <form action="{{ route('mahasiswa.lms.forum.destroy', [$pengampu->id, $reply->id]) }}" method="POST" style="margin: 0;">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Hapus balasan ini?')">Hapus</button>
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

                        @if($post->user_id === auth()->id() && ! $post->parent_id)
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                <button type="button" @click="editPost = !editPost" class="btn btn-secondary btn-xs">
                                    <span x-text="editPost ? 'Batal' : 'Ubah'">Ubah</span>
                                </button>
                                <form action="{{ route('mahasiswa.lms.forum.destroy', [$pengampu->id, $post->id]) }}" method="POST" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Hapus pesan ini? Balasan pada pesan ini juga akan dihapus.')">Hapus Pesan</button>
                                </form>
                            </div>
                        @endif

                        <form action="{{ route('mahasiswa.lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.5rem;">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $post->id }}">
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <input type="text" name="pesan" class="form-input" style="flex: 1; min-width: 160px; padding: 0.35rem 0.6rem; font-size: 0.8rem;" placeholder="Tulis balasan..." required>
                                <input type="file" name="file" class="form-input" style="max-width: 160px; padding: 0.3rem;">
                                <button type="submit" class="btn btn-primary btn-sm">Balas</button>
                            </div>
                            @error('pesan') <div class="form-error">{{ $message }}</div> @enderror
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
            <form action="{{ route('mahasiswa.lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea name="pesan" class="form-textarea" style="min-height: 100px;" required placeholder="Tulis pesan diskusi..."></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <input type="file" name="file" class="form-input" style="flex: 1;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">Kirim Diskusi</button>
            </form>
        </div>
    </div>

    {{-- Tab: Pengumuman --}}
    <div x-show="tab === 'pengumuman'">
        @forelse($pengumumans as $pengumuman)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $pengumuman->judul }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $pengumuman->published_at?->format('d M Y H:i') }}</span>
                        </div>
                        <div style="font-size: 0.85rem; color: #475569; margin-top: 0.35rem; line-height: 1.7; white-space: pre-wrap;">{!! linkify($pengumuman->isi) !!}</div>
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada pengumuman.</p>
            </div>
        @endforelse
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
                        <tr>
                            @php $nilaiAkhir = $nilaiByKomponen->get('akhir')?->nilai; @endphp
                            <td colspan="3" style="text-align: right; font-weight: 700;">Nilai Akhir</td>
                            <td style="text-align: center; font-weight: 700;">{{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
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
