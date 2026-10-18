@php
    $linkBase = 'display:flex; align-items:center; gap:0.55rem; padding:0.55rem 0.75rem; border-radius:8px; font-size:0.8rem; font-weight:600; text-decoration:none; border-left:3px solid transparent;';
    $activeStyle = 'background:#FFF8E0; color:#B8860B; border-left-color:#A16207;';
    $idleStyle = 'color:#475569;';
@endphp

<aside style="width: 235px; flex-shrink: 0; position: sticky; top: 1rem;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #1e293b; line-height: 1.35;">{{ $mahasiswa->nama }}</div>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.15rem;">
                {{ $mahasiswa->nim }}@if($mahasiswa->programStudi) · {{ $mahasiswa->programStudi->nama_prodi }}@endif
            </div>
        </div>

        <nav style="display: flex; flex-direction: column; padding: 0.5rem; gap: 0.15rem;">
            <a href="{{ route('mahasiswa.nilai', $mahasiswa->id) }}" style="{{ $linkBase }} {{ $current === 'nilai' ? $activeStyle : $idleStyle }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                KHS
            </a>

            <a href="{{ route('mahasiswa.status', $mahasiswa->id) }}" style="{{ $linkBase }} {{ $current === 'status' ? $activeStyle : $idleStyle }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                Status
            </a>

            <a href="{{ route('mahasiswa.transkrip', $mahasiswa->id) }}" style="{{ $linkBase }} {{ $current === 'transkrip' ? $activeStyle : $idleStyle }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Transkrip
            </a>
        </nav>

        <div style="padding: 0.75rem 1rem; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('mahasiswa.index') }}" style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; font-weight:600; color:#64748b; text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Data Mahasiswa
            </a>
        </div>
    </div>
</aside>