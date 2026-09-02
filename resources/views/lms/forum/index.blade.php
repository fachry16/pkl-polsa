@extends('layouts.app')

@section('content')

<div class="page-header">
    Forum Diskusi - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Kelas</a>
</div>


<div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
    <div>
        @forelse($diskusi as $post)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0.75rem;">
                <div style="display: flex; gap: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">{{ substr($post->user->name ?? '?', 0, 2) }}</div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $post->user->name ?? '-' }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $post->created_at->diffForHumans() }}</span>
                            @if($post->user->isDosen())
                                <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.4rem; font-size: 0.65rem; font-weight: 600;">Dosen</span>
                            @endif
                        </div>
                        <div style="font-size: 0.85rem; color: #334155; margin-top: 0.35rem; line-height: 1.7; white-space: pre-wrap;">{!! linkify($post->pesan) !!}</div>

                        @if($post->file_path)
                            @php($fileUrl = route('lms.file', ['forum', $post->id]))
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                                @if($fileUrl)
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; vertical-align: -2px; margin-right: 0.25rem;"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                        {{ basename($post->file_path) }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                            @if(Auth::id() === $post->user_id && $post->isWithinTimeLimit(15))
                                <a href="{{ route('lms.forum.edit', [$pengampu->id, $post->id]) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">Edit</a>
                            @endif
                            @if(! $post->user?->isMahasiswa())
                                <form action="{{ route('lms.forum.destroy', [$pengampu->id, $post->id]) }}" method="POST" onsubmit="return confirm('Hapus pesan ini? Semua balasan terkait juga ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">Hapus</button>
                                </form>
                            @endif
                        </div>

                        @if($post->replies->count())
                            <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 2px solid #e2e8f0;">
                                @foreach($post->replies as $reply)
                                    <div style="display: flex; gap: 0.6rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                                        <div style="width: 1.5rem; height: 1.5rem; border-radius: 6px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.55rem; flex-shrink: 0;">{{ substr($reply->user->name ?? '?', 0, 2) }}</div>
                                        <div style="flex: 1;">
                                            <div style="font-size: 0.75rem; font-weight: 500; color: #1e293b;">
                                              {{ $reply->user->name ?? '-' }}
                                              <span style="font-weight: 400; color: #94a3b8; font-size: 0.65rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; white-space: pre-wrap;">{!! linkify($reply->pesan) !!}</div>
                                            <div style="display: flex; gap: 0.5rem; margin-top: 0.4rem;">
                                                @if(Auth::id() === $reply->user_id && $reply->isWithinTimeLimit(15))
                                                    <a href="{{ route('lms.forum.edit', [$pengampu->id, $reply->id]) }}" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.7rem;">Edit</a>
                                                @endif
                                                @if(! $reply->user?->isMahasiswa())
                                                    <form action="{{ route('lms.forum.destroy', [$pengampu->id, $reply->id]) }}" method="POST" onsubmit="return confirm('Hapus balasan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.7rem;">Hapus</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.75rem;">
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
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada diskusi. Mulai diskusi pertama!</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $diskusi->links() }}
        </div>
    </div>

    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; position: sticky; top: 1rem;">
        <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0 0 1rem;">Diskusi Baru</h3>
        <form action="{{ route('lms.forum.store', $pengampu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Pesan <span style="color: #dc2626;">*</span></label>
                <textarea name="pesan" class="form-textarea" style="min-height: 120px;" required>{{ old('pesan') }}</textarea>
                @error('pesan') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Lampiran File</label>
                <input type="file" name="file" class="form-input">
                @error('file') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Kirim Diskusi</button>
        </form>
    </div>
</div>

@endsection
