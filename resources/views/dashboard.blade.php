@extends('layouts.app')

@section('content')

{{-- Top Header Card: Identitas Kampus POLSA & Profil User dengan Menu Titik 3 --}}
<div class="card" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; margin-bottom: 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); flex-wrap: wrap; gap: 1rem;">
    {{-- Sisi Kiri: Identitas Kampus POLSA --}}
    <div style="display: flex; align-items: center; gap: 0.85rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 4px 10px rgba(30, 58, 138, 0.2); flex-shrink: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em; line-height: 1.2;">
                    POLITEKNIK SAWUNGGALIH AJI
                </h1>
                <span style="font-size: 0.7rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 0.15rem 0.5rem; border-radius: 6px; letter-spacing: 0.02em;">
                    POLSA PURWOREJO
                </span>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>Sistem Informasi Kurikulum &amp; Perkuliahan Digital (LMS)</span>
                @if($tahunAkademik)
                    <span>&bull;</span>
                    <span style="color: #059669; font-weight: 600;">TA {{ $tahunAkademik->tahun }} ({{ $tahunAkademik->semester }})</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Sisi Kanan: Profil User & Kebab Menu (Titik 3) --}}
    @php
        $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
        $rolesNames = array_map(function($r) use ($roleMap) {
            return $roleMap->get($r) ?? ucfirst(str_replace('_', ' ', $r));
        }, auth()->user()->getRolesList());
        $rolesStr = implode(' & ', $rolesNames);
    @endphp

    <div x-data="{ openKebab: false }" @click.outside="openKebab = false" style="position: relative;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.35rem 0.5rem 0.35rem 0.75rem; border-radius: 10px; background: #f8fafc; border: 1px solid #f1f5f9;">
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

                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 1rem; font-size: 0.8rem; color: #dc2626; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#fef2f2';" onmouseout="this.style.background='transparent';">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span style="font-weight: 600;">Keluar (Logout)</span>
                </a>
            </div>
        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
    </div>
</div>

{{-- Running Text Visi & Misi POLSA --}}
<div class="card" style="padding: 0.5rem 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
    {{-- Fixed Badge on Left --}}
    <div style="display: flex; align-items: center; gap: 0.35rem; background: #eef2ff; border: 1px solid #e0e7ff; color: #4338ca; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; white-space: nowrap; flex-shrink: 0; z-index: 2;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polygon points="12 8 8 12 12 16 12 8"/>
        </svg>
        <span>VISI &amp; MISI POLSA</span>
    </div>

    {{-- Marquee Track --}}
    <div style="flex: 1; overflow: hidden; position: relative; white-space: nowrap; mask-image: linear-gradient(to right, transparent, black 15px, black calc(100% - 15px), transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 15px, black calc(100% - 15px), transparent);">
        <div class="marquee-content" style="display: inline-block; white-space: nowrap; font-size: 0.82rem; color: #475569; animation: marquee-scroll 45s linear infinite;">
            <strong style="color: #1e293b;">VISI:</strong> Menjadi politeknik unggulan yang menghasilkan sumber daya manusia profesional, kompeten, dan berdaya saing global di bidang bisnis dan teknologi pada tahun 2030.
            <span style="color: #cbd5e1; margin: 0 0.85rem;">&bull;&bull;&bull;</span>
            <strong style="color: #1e293b;">MISI:</strong> (1) Menyelenggarakan pendidikan vokasi berkualitas &amp; relevan kebutuhan industri
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (2) Melaksanakan penelitian terapan inovatif bagi masyarakat
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (3) Menjalin kemitraan strategis dengan dunia usaha &amp; industri (DUDIKA)
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (4) Mengembangkan tata kelola institusi profesional, transparan, &amp; akuntabel
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (5) Membudayakan nilai Pancasila &amp; kearifan lokal dalam tridharma.
            <span style="color: #cbd5e1; margin: 0 0.85rem;">&bull;&bull;&bull;</span>
            <strong style="color: #1e293b;">VISI:</strong> Menjadi politeknik unggulan yang menghasilkan sumber daya manusia profesional, kompeten, dan berdaya saing global di bidang bisnis dan teknologi pada tahun 2030.
            <span style="color: #cbd5e1; margin: 0 0.85rem;">&bull;&bull;&bull;</span>
            <strong style="color: #1e293b;">MISI:</strong> (1) Menyelenggarakan pendidikan vokasi berkualitas &amp; relevan kebutuhan industri
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (2) Melaksanakan penelitian terapan inovatif bagi masyarakat
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (3) Menjalin kemitraan strategis dengan dunia usaha &amp; industri (DUDIKA)
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (4) Mengembangkan tata kelola institusi profesional, transparan, &amp; akuntabel
            <span style="color: #cbd5e1; margin: 0 0.5rem;">&bull;</span>
            (5) Membudayakan nilai Pancasila &amp; kearifan lokal dalam tridharma.
        </div>
    </div>
</div>

<style>
@keyframes marquee-scroll {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}
.marquee-content:hover {
    animation-play-state: paused !important;
    cursor: default;
}
</style>

@if(auth()->user()->isMahasiswa())
    {{-- Mahasiswa Dashboard --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ $statKelas }}</span>
                <span style="font-size: 0.8rem; color: #818cf8; font-weight: 500;">Kelas Diikuti</span>
            </div>
        </div>

        <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $statTugasAktif }}</span>
                <span style="font-size: 0.8rem; color: #34d399; font-weight: 500;">Tugas Aktif</span>
            </div>
        </div>

        <div class="stat-prodi-card" style="border-left: 4px solid #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #d97706; display: block; line-height: 1.2;">{{ $statBelumDikumpul }}</span>
                <span style="font-size: 0.8rem; color: #fbbf24; font-weight: 500;">Belum Dikumpul</span>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 1rem; align-items: start; margin-bottom: 1.5rem;">
        <div class="card">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span style="font-weight: 700; font-size: 1rem; color: #1e293b;">Tugas Mendekati Deadline</span>
                <span style="font-size: 0.7rem; font-weight: 600; background: #fef3c7; color: #b45309; padding: 0.15rem 0.5rem; border-radius: 6px; margin-left: auto;">7 Hari</span>
            </div>

            @if($tugasMendekati->count())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($tugasMendekati as $tugas)
                        <div style="border: 1px solid #f1f5f9; border-radius: 10px; padding: 0.75rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                                <div style="min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a; line-height: 1.3; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $tugas->judul }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                                        {{ $tugas->pengampu->mataKuliah->kode ?? '' }} {{ $tugas->pengampu->mataKuliah->nama ?? 'Mata Kuliah' }} &middot; Kelas {{ $tugas->pengampu->kelas ?? '-' }}
                                    </div>
                                </div>
                                <span style="font-size: 0.7rem; font-weight: 600; background: #fef3c7; color: #b45309; padding: 0.15rem 0.5rem; border-radius: 6px; flex-shrink: 0;">
                                    {{ $tugas->deadline->diffForHumans(['parts' => 1]) }}
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.5rem; border-top: 1px solid #f8fafc; padding-top: 0.5rem;">
                                <span style="font-size: 0.75rem; color: #64748b;">Deadline {{ $tugas->deadline->format('d M Y, H:i') }}</span>
                                <a href="{{ route('mahasiswa.lms.show', $tugas->pengampu_id) }}" class="btn btn-primary btn-sm">Buka Kelas</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.5rem;">
                    <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 0;">Tidak ada tugas mendekati deadline. Aman!</p>
                </div>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="card">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                    <span style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Materi Terbaru</span>
                </div>

                @if($materiBaru->count())
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($materiBaru as $materi)
                            <a href="{{ route('mahasiswa.lms.show', $materi->pengampu_id) }}" style="display: flex; align-items: center; gap: 0.6rem; text-decoration: none; padding: 0.5rem; border-radius: 8px; background: #f8fafc; transition: all 0.1s;" onmouseover="this.style.background='#eef2ff';" onmouseout="this.style.background='#f8fafc';">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.8rem; color: #0f172a; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $materi->judul }}</div>
                                    <div style="font-size: 0.7rem; color: #64748b;">{{ $materi->pengampu->mataKuliah->kode ?? '' }} &middot; {{ $materi->created_at->diffForHumans() }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 0;">Belum ada materi baru 7 hari terakhir.</p>
                @endif
            </div>

            <div class="card">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    <span style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Diskusi Terbaru</span>
                </div>

                @if($forumTerbaru->count())
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($forumTerbaru as $diskusi)
                            <a href="{{ route('mahasiswa.lms.show', $diskusi->pengampu_id) }}" style="display: flex; align-items: flex-start; gap: 0.6rem; text-decoration: none; padding: 0.5rem; border-radius: 8px; background: #f8fafc; transition: all 0.1s;" onmouseover="this.style.background='#eef2ff';" onmouseout="this.style.background='#f8fafc';">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ ($diskusi->user->role ?? '') === 'dosen' ? '#ecfdf5' : '#eef2ff' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ ($diskusi->user->role ?? '') === 'dosen' ? '#059669' : '#4f46e5' }};">{{ strtoupper(substr($diskusi->user->name ?? '?', 0, 1)) }}</span>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.8rem; color: #0f172a; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $diskusi->pesan }}</div>
                                    <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.15rem;">
                                        {{ $diskusi->user->name ?? 'Pengguna' }} &middot; {{ $diskusi->pengampu->mataKuliah->kode ?? '' }} &middot; {{ $diskusi->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 0;">Belum ada diskusi baru 7 hari terakhir.</p>
                @endif
            </div>
        </div>
    </div>
@else
    {{-- Multi-Role Dashboard for Staff (Admin, Kaprodi, Direktur, Dosen) --}}
    @php
        $availableTabs = [];
        if(auth()->user()->isAdmin()) {
            $availableTabs['admin'] = [
                'label' => 'Pusat Kendali Admin',
                'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
            ];
        }
        if(auth()->user()->isKaprodi()) {
            $prodiLabel = $kaprodiProdi->kode_prodi ?? 'Prodi';
            $availableTabs['kaprodi'] = [
                'label' => "Program Studi ($prodiLabel)",
                'badge' => $kaprodiRpsStats['diajukan'] > 0 ? $kaprodiRpsStats['diajukan'] : null,
                'badgeColor' => '#dc2626',
                'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
            ];
        }
        if(auth()->user()->isDirektur()) {
            $availableTabs['direktur'] = [
                'label' => 'Direktur (Eksekutif)',
                'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22.01"/><line x1="15" y1="22" x2="15" y2="22.01"/><line x1="8" y1="6" x2="8" y2="6.01"/><line x1="16" y1="6" x2="16" y2="6.01"/><line x1="8" y1="10" x2="8" y2="10.01"/><line x1="16" y1="10" x2="16" y2="10.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="16" y1="14" x2="16" y2="14.01"/><line x1="8" y1="18" x2="8" y2="18.01"/><line x1="16" y1="18" x2="16" y2="18.01"/></svg>',
            ];
        }
        if(auth()->user()->isDosen() || auth()->user()->isKaprodi() || auth()->user()->isDirektur()) {
            $availableTabs['dosen'] = [
                'label' => 'Mengajar (Dosen)',
                'badge' => ($dosenSubmissionsBelumDinilai ?? 0) > 0 ? $dosenSubmissionsBelumDinilai : null,
                'badgeColor' => '#ea580c',
                'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
            ];
        }

        $defaultTab = request('tab');
        if (!$defaultTab || !array_key_exists($defaultTab, $availableTabs)) {
            if (request()->routeIs('dashboard-direktur') && isset($availableTabs['direktur'])) {
                $defaultTab = 'direktur';
            } elseif (isset($availableTabs['kaprodi'])) {
                $defaultTab = 'kaprodi';
            } elseif (isset($availableTabs['direktur'])) {
                $defaultTab = 'direktur';
            } elseif (isset($availableTabs['admin'])) {
                $defaultTab = 'admin';
            } else {
                $defaultTab = 'dosen';
            }
        }
    @endphp

    <div x-data="{ activeTab: '{{ $defaultTab }}' }">
        {{-- Navigation Tabs (LMS Clean Button Style) --}}
        @if(count($availableTabs) > 1)
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                @foreach($availableTabs as $key => $tab)
                    <button type="button"
                            @click="activeTab = '{{ $key }}'; history.replaceState(null, null, '?tab={{ $key }}')"
                            class="btn btn-secondary btn-sm"
                            :style="activeTab === '{{ $key }}' ? 'background: #cbd5e1; color: #0f172a; font-weight: 600; border-color: #94a3b8;' : ''"
                            style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 6px; transition: all 0.15s; cursor: pointer;">
                        {!! $tab['icon'] !!}
                        <span>{{ $tab['label'] }}</span>
                        @if(isset($tab['badge']) && $tab['badge'])
                            <span style="background: {{ $tab['badgeColor'] ?? '#ef4444' }}; color: #ffffff; font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 999px; line-height: 1.2;">
                                {{ $tab['badge'] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Seksi Tampilan Sesuai Tab Aktif --}}
        @if(isset($availableTabs['admin']))
            <div x-show="activeTab === 'admin'" x-cloak>
                @include('dashboard.admin')
            </div>
        @endif

        @if(isset($availableTabs['kaprodi']))
            <div x-show="activeTab === 'kaprodi'" x-cloak>
                @include('dashboard.kaprodi')
            </div>
        @endif

        @if(isset($availableTabs['direktur']))
            <div x-show="activeTab === 'direktur'" x-cloak>
                @include('dashboard.direktur')
            </div>
        @endif

        @if(isset($availableTabs['dosen']))
            <div x-show="activeTab === 'dosen'" x-cloak>
                @include('dashboard.dosen')
            </div>
        @endif
    </div>
@endif

@endsection