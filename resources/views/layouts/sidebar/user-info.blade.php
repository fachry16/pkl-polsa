<div class="sidebar-user">
    <div class="user-info">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-detail">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">{{ auth()->user()->dosen?->jabatan ?? ucfirst(auth()->user()->role) }}</div>
        </div>
    </div>
    <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
    </a>
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>
</div>
