@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $recentNotifications = auth()->user()->notifications->take(10);
@endphp

<div class="notif-wrap" x-data="{ open: false }">
    <button type="button" class="notif-bell" @click="open = !open" aria-label="Notifikasi">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        @if($unreadCount > 0)
            <span class="notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    <div class="notif-dropdown" x-show="open" @click.outside="open = false" x-transition style="display: none;">
        <div class="notif-header">
            <span style="font-weight: 600; font-size: 0.85rem;">Notifikasi</span>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="notif-read-all">Tandai dibaca</button>
                </form>
            @endif
        </div>

        <div class="notif-list">
            @forelse($recentNotifications as $notification)
                @php $data = $notification->data; @endphp
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="notif-item {{ $notification->unread() ? 'unread' : '' }}">
                    <div style="width: 2rem; height: 2rem; border-radius: 8px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: {{ $notification->unread() ? 600 : 500 }}; font-size: 0.78rem; color: #0f172a; line-height: 1.35;">{{ $data['judul'] ?? 'Notifikasi' }}</div>
                        <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.1rem;">
                            {{ $data['mata_kuliah_kode'] ?? '' }} {{ $data['mata_kuliah'] ?? '' }} &middot; Kelas {{ $data['kelas'] ?? '-' }}
                            @isset($data['nilai'])
                                <span style="color: #059669; font-weight: 600;">&middot; Nilai: {{ $data['nilai'] }}</span>
                            @endisset
                        </div>
                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem;">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div class="notif-empty">Belum ada notifikasi.</div>
            @endforelse
        </div>
    </div>
</div>
