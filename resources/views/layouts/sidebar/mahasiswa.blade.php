<aside class="sidebar">
    @include('layouts.partials.sidebar-toggle')
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img class="sidebar-logo-img" src="{{ asset('images/eduva/eduva.png') }}" alt="Eduva">
            @include('layouts.partials.notification-bell')
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard" data-title="Dashboard"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><span class="nav-text">Dashboard</span></a>
        <a href="{{ route('mahasiswa.identitas') }}" class="{{ request()->routeIs('mahasiswa.identitas') ? 'active' : '' }}" title="Identitas" data-title="Identitas">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M5 16c0-1.66 1.79-3 4-3s4 1.34 4 3"/><line x1="16" y1="8" x2="20" y2="8"/><line x1="16" y1="12" x2="20" y2="12"/><line x1="16" y1="16" x2="18" y2="16"/></svg>
            <span class="nav-text">Identitas</span>
        </a>
        @php $unreadLms = auth()->user()->unread_lms_count; @endphp
        <a href="{{ route('mahasiswa.lms.index') }}" class="{{ request()->routeIs('mahasiswa.lms.*') ? 'active' : '' }}" title="Kelas LMS Saya" data-title="Kelas LMS Saya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            <span class="nav-text">Kelas LMS Saya</span>
            @if($unreadLms > 0)
                <span class="menu-badge">{{ $unreadLms > 99 ? '99+' : $unreadLms }}</span>
            @endif
        </a>
    </nav>
</aside>
