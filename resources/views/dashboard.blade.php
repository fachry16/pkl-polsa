@extends('layouts.app')

@section('content')

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
    @include('dashboard.mahasiswa')
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