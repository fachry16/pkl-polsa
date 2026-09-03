@php
    $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
    $rolesNames = array_map(function($r) use ($roleMap) {
        return $roleMap->get($r) ?? ucfirst(str_replace('_', ' ', $r));
    }, auth()->user()->getRolesList());
    $rolesStr = implode(' & ', $rolesNames);
    $taAktif = $globalTahunAkademik ?? null;
@endphp

@auth
<header class="global-campus-sticky-header no-print">
    <div class="campus-header-card" x-data="{ openKebab: false }" @click.outside="openKebab = false">
        {{-- Sisi Kiri: Identitas Kampus POLSA & Mobile Hamburger --}}
        <div class="campus-header-left">
            {{-- Tombol Hamburger khusus layar Mobile/Tablet --}}
            <button type="button"
                    class="mobile-menu-toggle-btn"
                    @click="$dispatch('toggle-sidebar')"
                    aria-label="Toggle Sidebar Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <div class="campus-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('dashboard') }}" class="campus-title-link">
                        POLITEKNIK SAWUNGGALIH AJI
                    </a>
                    <span class="campus-badge">
                        POLSA PURWOREJO
                    </span>
                </div>
                <div class="campus-subtitle">
                    <span>Sistem Informasi Kurikulum &amp; Perkuliahan Digital (LMS)</span>
                    @if($taAktif)
                        <span>&bull;</span>
                        <span style="color: #059669; font-weight: 600;">TA {{ $taAktif->tahun }} ({{ ucfirst($taAktif->semester) }})</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Profil User & Kebab Menu (Titik 3) --}}
        <div @click.outside="openKebab = false" style="position: relative;">
            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.35rem 0.5rem 0.35rem 0.75rem; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;">
                {{-- Avatar & Info --}}
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover; flex-shrink: 0; border: 1px solid #e2e8f0;">
                @else
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div style="min-width: 0; max-width: 180px;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size: 0.7rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $rolesStr }}
                    </div>
                </div>

                {{-- Tombol Titik 3 (Kebab Menu) --}}
                <button type="button"
                        @click="openKebab = !openKebab"
                        style="background: transparent; border: none; padding: 0.35rem; border-radius: 6px; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.15s;"
                        onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';"
                        onmouseout="this.style.background='transparent'; this.style.color='#64748b';"
                        title="Menu Akun">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="1"/>
                        <circle cx="12" cy="5" r="1"/>
                        <circle cx="12" cy="19" r="1"/>
                    </svg>
                </button>
            </div>

            {{-- Dropdown Menu Kebab --}}
            <div x-show="openKebab"
                 x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 style="position: absolute; right: 0; top: calc(100% + 0.5rem); width: 220px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12); z-index: 100; overflow: hidden;">
                
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; background: #fafafa; display: flex; align-items: center; gap: 0.65rem;">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" style="width: 34px; height: 34px; border-radius: 8px; object-fit: cover; flex-shrink: 0; border: 1px solid #e2e8f0;">
                    @else
                        <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #818cf8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-weight: 700; font-size: 0.82rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 0.72rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div style="padding: 0.35rem 0;">
                    <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 1rem; font-size: 0.8rem; color: #334155; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='transparent';">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Pengaturan Akun</span>
                    </a>

                    @if(auth()->user()->isDosen())
                        <a href="{{ route('dosen.self') }}" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 1rem; font-size: 0.8rem; color: #334155; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='transparent';">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="12" y2="16"/></svg>
                            <span>Data Diri Dosen</span>
                        </a>
                    @endif

                    <div style="border-top: 1px solid #f1f5f9; margin: 0.35rem 0;"></div>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 1rem; font-size: 0.8rem; color: #dc2626; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#fef2f2';" onmouseout="this.style.background='transparent';">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span style="font-weight: 600;">Keluar (Logout)</span>
                    </a>
                </div>
            </div>

            <form id="global-logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</header>
@endauth
