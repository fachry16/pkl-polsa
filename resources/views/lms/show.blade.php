@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $pengampu->dosen?->user?->name ?? '-' }} &middot; {{ $pengampu->semester_akademik ?? '-' }} {{ $pengampu->tahunAkademik?->tahun ?? '' }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-primary btn-sm">Beranda</a>
    <a href="{{ route('lms.materi.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Materi ({{ $materiCount }})</a>
    <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Tugas ({{ $tugasCount }})</a>
    <a href="{{ route('lms.forum.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Forum</a>
    <a href="{{ route('lms.pengumuman.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Pengumuman</a>
    <a href="{{ route('lms.absensi.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Presensi</a>
    <a href="{{ route('lms.tugas.rekap', $pengampu->id) }}" class="btn btn-secondary btn-sm">Rekap Nilai</a>
    <a href="{{ route('lms.index') }}" class="btn btn-secondary btn-sm" style="margin-left: auto;">Kembali</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Materi Terbaru</h3>
        @forelse($pengampu->lmsMateris as $materi)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                <div style="font-size: 0.85rem; font-weight: 500; color: #1e293b; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $materi->judul }}</div>
                <span style="font-size: 0.7rem; color: #94a3b8;">{{ $materi->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada materi.</p>
        @endforelse
    </div>

    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem;">
        <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Tugas Terbaru</h3>
        @forelse($pengampu->lmsTugas as $tugas)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 0.85rem; font-weight: 500; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $tugas->judul }}</div>
                    <div style="font-size: 0.7rem; color: #94a3b8;">
                        Deadline: {{ $tugas->deadline->format('d M Y H:i') }} &middot; {{ $tugas->submissions_count }} pengumpulan
                    </div>
                </div>
            </div>
        @empty
            <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada tugas.</p>
        @endforelse
    </div>
</div>

<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-top: 1rem;">
    <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Diskusi Terbaru</h3>
    @forelse($pengampu->lmsForumDiskusis as $forum)
        <div style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
            <div style="width: 2rem; height: 2rem; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; flex-shrink: 0;">{{ substr($forum->user->name ?? '?', 0, 2) }}</div>
            <div style="flex: 1;">
                <div style="font-size: 0.8rem; font-weight: 500; color: #1e293b;">{{ $forum->user->name ?? '-' }}</div>
                <div style="font-size: 0.85rem; color: #475569; margin-top: 0.15rem; line-height: 1.5;">{{ Str::limit($forum->pesan, 200) }}</div>
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">
                    {{ $forum->created_at->diffForHumans() }} &middot; {{ $forum->replies->count() }} balasan
                </div>
            </div>
        </div>
    @empty
        <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada diskusi.</p>
    @endforelse
</div>

<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-top: 1rem;">
    <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 0.75rem; color: #1e293b;">Pengumuman Terbaru</h3>
    @forelse($pengampu->lmsPengumumans as $pengumuman)
        <div style="padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9;">
            <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">{{ $pengumuman->judul }}</div>
            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.15rem; line-height: 1.5;">{{ Str::limit($pengumuman->isi, 160) }}</div>
            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">{{ $pengumuman->published_at?->format('d M Y H:i') }}</div>
        </div>
    @empty
        <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1rem 0;">Belum ada pengumuman.</p>
    @endforelse
</div>

@endsection
