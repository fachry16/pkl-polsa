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
