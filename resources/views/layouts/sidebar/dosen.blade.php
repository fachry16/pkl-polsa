<aside class="sidebar">
    @include('layouts.partials.sidebar-toggle')
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 4L26 10V18L16 24L6 18V10L16 4Z" fill="#eef2ff" stroke="#4f46e5" stroke-width="1.5"/>
                <path d="M16 24L26 18V22L16 28L6 22V18L16 24Z" fill="#e0e7ff" stroke="#4f46e5" stroke-width="1.5"/>
                <path d="M16 14L21 11V15L16 18L11 15V11L16 14Z" fill="#4f46e5" opacity="0.3"/>
            </svg>
            <span class="sidebar-brand">PIKOBE</span>
            @include('layouts.partials.notification-bell')
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard" data-title="Dashboard"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><span class="nav-text">Dashboard</span></a>
        @if(auth()->user()->isDirektur())
            <a href="{{ route('dashboard-direktur') }}" class="{{ request()->routeIs('dashboard-direktur') ? 'active' : '' }}" title="Dashboard Direktur" data-title="Dashboard Direktur"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><span class="nav-text">Dashboard Direktur</span></a>
        @endif
        <a href="{{ route('dosen.self') }}" class="{{ request()->routeIs('dosen.self') ? 'active' : '' }}" title="Data Diri" data-title="Data Diri"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="nav-text">Data Diri</span></a>
        @php
            $unreadRps = auth()->user()->unread_rps_count;
            $unreadLms = auth()->user()->unread_lms_count;
        @endphp
        @if(auth()->user()->isKaprodi())
            @if(auth()->user()->dosen)
                <a href="{{ route('program-studi.kurikulum', auth()->user()->dosen->program_studi_id) }}" class="{{ request()->routeIs('program-studi.kurikulum') || request()->routeIs('kurikulum.*') ? 'active' : '' }}" title="Kurikulum" data-title="Kurikulum"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg><span class="nav-text">Kurikulum</span></a>
            @endif
            <a href="{{ route('rps.pengajuan') }}" class="{{ request()->routeIs('rps.pengajuan') ? 'active' : '' }}" title="Pengajuan RPS" data-title="Pengajuan RPS">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span class="nav-text">Pengajuan RPS</span>
                @if($unreadRps > 0)
                    <span class="menu-badge">{{ $unreadRps > 99 ? '99+' : $unreadRps }}</span>
                @endif
            </a>
            <a href="{{ route('krs.index') }}" class="{{ request()->routeIs('krs.*') ? 'active' : '' }}" title="KRS" data-title="KRS"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg><span class="nav-text">KRS</span></a>
        @endif
        @if(auth()->user()->dosen)
            <a href="{{ route('dosen.self.riwayat') }}" class="{{ request()->routeIs('dosen.self.riwayat') ? 'active' : '' }}" title="Riwayat Mengajar &amp; RPS" data-title="Riwayat Mengajar &amp; RPS">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="nav-text">Riwayat Mengajar &amp; RPS</span>
                @if(!auth()->user()->isKaprodi() && $unreadRps > 0)
                    <span class="menu-badge">{{ $unreadRps > 99 ? '99+' : $unreadRps }}</span>
                @endif
            </a>
        @endif
        <a href="{{ route('lms.index') }}" class="{{ request()->routeIs('lms.*') ? 'active' : '' }}" title="Kelas LMS Saya" data-title="Kelas LMS Saya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            <span class="nav-text">Kelas LMS Saya</span>
            @if($unreadLms > 0)
                <span class="menu-badge">{{ $unreadLms > 99 ? '99+' : $unreadLms }}</span>
            @endif
        </a>
    </nav>
</aside>
