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
        <a href="{{ route('tahun-akademik.index') }}" class="{{ request()->routeIs('tahun-akademik.*') ? 'active' : '' }}" title="Tahun Akademik" data-title="Tahun Akademik"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span class="nav-text">Tahun Akademik</span></a>
        @php
            $unreadKrs = auth()->user()->unread_krs_count;
            $unreadKurikulum = auth()->user()->unread_kurikulum_count;
        @endphp
        <a href="{{ route('program-studi.index') }}" class="{{ request()->routeIs('program-studi.*') && !request()->routeIs('program-studi.kurikulum') ? 'active' : '' }}" title="Program Studi" data-title="Program Studi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span class="nav-text">Program Studi</span>
            @if($unreadKurikulum > 0)
                <span class="menu-badge">{{ $unreadKurikulum > 99 ? '99+' : $unreadKurikulum }}</span>
            @endif
        </a>
        <a href="{{ route('dosen.index') }}" class="{{ request()->routeIs('dosen.*') && !request()->routeIs('dosen.self*') ? 'active' : '' }}" title="Dosen" data-title="Dosen"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="nav-text">Dosen</span></a>
        <a href="{{ route('mahasiswa.index') }}" class="{{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}" title="Mahasiswa" data-title="Mahasiswa"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg><span class="nav-text">Mahasiswa</span></a>
        <a href="{{ route('pengampu.index') }}" class="{{ request()->routeIs('pengampu.*') ? 'active' : '' }}" title="Pengampu" data-title="Pengampu"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg><span class="nav-text">Pengampu</span></a>
        <a href="{{ route('krs.index') }}" class="{{ request()->routeIs('krs.*') ? 'active' : '' }}" title="KRS" data-title="KRS">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span class="nav-text">KRS</span>
            @if($unreadKrs > 0)
                <span class="menu-badge">{{ $unreadKrs > 99 ? '99+' : $unreadKrs }}</span>
            @endif
        </a>
        <a href="{{ route('khs.cetak-pilih') }}" class="{{ request()->routeIs('khs.*') ? 'active' : '' }}" title="KHS" data-title="KHS">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span class="nav-text">KHS</span>
        </a>
        <a href="{{ route('lms.monitor') }}" class="{{ request()->routeIs('lms.monitor') ? 'active' : '' }}" title="Kelas LMS" data-title="Kelas LMS"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg><span class="nav-text">Kelas LMS</span></a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}" title="Manajemen User" data-title="Manajemen User"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg><span class="nav-text">Manajemen User</span></a>
        <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}" title="Role &amp; Jabatan" data-title="Role &amp; Jabatan"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span class="nav-text">Role &amp; Jabatan</span></a>
    </nav>
</aside>
