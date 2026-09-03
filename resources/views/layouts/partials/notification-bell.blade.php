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
                @php 
                    $data = $notification->data;
                    $type = class_basename($notification->type);
                @endphp
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="notif-item {{ $notification->unread() ? 'unread' : '' }}"
                   style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 1rem; text-decoration: none; border-bottom: 1px solid #f8fafc; transition: background 0.15s; {{ $notification->unread() ? 'background: #f8fafc;' : '' }}"
                   onmouseover="this.style.background='#f1f5f9';"
                   onmouseout="this.style.background='{{ $notification->unread() ? '#f8fafc' : '#ffffff' }}';">
                    
                    {{-- Dynamic Icon based on Notification Type --}}
                    @if($type === 'MateriBaru')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        </div>
                    @elseif($type === 'TugasBaru')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                        </div>
                    @elseif($type === 'SubmissionBaru')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                    @elseif($type === 'NilaiDiberikan')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                    @elseif($type === 'PengumumanBaru')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #faf5ff; color: #9333ea; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                        </div>
                    @elseif($type === 'RpsDisetujui')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                    @elseif($type === 'RpsDirevisi')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #fff1f2; color: #e11d48; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                    @elseif($type === 'ForumDiskusiBaru')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                    @elseif($type === 'KrsBaruAdmin')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                    @elseif($type === 'KurikulumBaruAdmin')
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        </div>
                    @else
                        <div style="width: 2.2rem; height: 2.2rem; border-radius: 8px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                    @endif

                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <span style="font-weight: {{ $notification->unread() ? 700 : 500 }}; font-size: 0.8rem; color: #0f172a; line-height: 1.35;">{{ $data['judul'] ?? 'Notifikasi' }}</span>
                            @if($notification->unread())
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #4f46e5; flex-shrink: 0;"></span>
                            @endif
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">
                            @if(!empty($data['mata_kuliah']))
                                {{ $data['mata_kuliah_kode'] ?? '' }} {{ $data['mata_kuliah'] }} &middot; Kelas {{ $data['kelas'] ?? '-' }}
                            @elseif(!empty($data['catatan']))
                                Catatan: {{ Str::limit($data['catatan'], 40) }}
                            @elseif(!empty($data['pengaju']))
                                Diajukan oleh {{ $data['pengaju'] }}
                            @endif
                            @isset($data['nilai'])
                                <span style="color: #059669; font-weight: 600;">&middot; Nilai: {{ $data['nilai'] }}</span>
                            @endisset
                        </div>
                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.2rem;">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div class="notif-empty" style="padding: 2.5rem 1rem; text-align: center; color: #94a3b8; font-size: 0.85rem;">Belum ada notifikasi.</div>
            @endforelse
        </div>
    </div>
</div>
