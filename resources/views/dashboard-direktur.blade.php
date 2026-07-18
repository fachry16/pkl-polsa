@extends('layouts.app')

@section('content')

<div class="page-header">Dashboard Direktur</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="border-left: 4px solid #4f46e5;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">VISI</span>
        </div>
        <p style="color: #475569; font-size: 0.9rem; line-height: 1.7; margin-bottom: 0;">
            Menjadi politeknik unggulan yang menghasilkan sumber daya manusia profesional, kompeten, dan berdaya saing global di bidang bisnis dan teknologi pada tahun 2030.
        </p>
    </div>

    <div class="card" style="border-left: 4px solid #10b981;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">MISI</span>
        </div>
        <ul style="color: #475569; font-size: 0.85rem; line-height: 1.8; padding-left: 1.1rem; margin-bottom: 0;">
            <li>Menyelenggarakan pendidikan vokasi yang berkualitas dan relevan dengan kebutuhan industri.</li>
            <li>Melaksanakan penelitian terapan yang inovatif dan bermanfaat bagi masyarakat.</li>
            <li>Menjalin kemitraan strategis dengan dunia usaha, dunia industri, dan dunia kerja.</li>
            <li>Mengembangkan tata kelola institusi yang profesional, transparan, dan akuntabel.</li>
            <li>Membudayakan nilai-nilai Pancasila dan kearifan lokal dalam setiap aktivitas tridharma.</li>
        </ul>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ \App\Models\ProgramStudi::count() }}</span>
            <span style="font-size: 0.8rem; color: #818cf8; font-weight: 500;">Program Studi</span>
        </div>
    </div>
    <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ \App\Models\Dosen::count() }}</span>
            <span style="font-size: 0.8rem; color: #34d399; font-weight: 500;">Dosen</span>
        </div>
    </div>
    <div class="stat-prodi-card" style="border-left: 4px solid #0ea5e9;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #0284c7; display: block; line-height: 1.2;">{{ \App\Models\Mahasiswa::count() }}</span>
            <span style="font-size: 0.8rem; color: #38bdf8; font-weight: 500;">Mahasiswa</span>
        </div>
    </div>
    <div class="stat-prodi-card" style="border-left: 4px solid #f43f5e;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff1f2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #e11d48; display: block; line-height: 1.2;">{{ \App\Models\Cpl::count() }}</span>
            <span style="font-size: 0.8rem; color: #fb7185; font-weight: 500;">CPL</span>
        </div>
    </div>
</div>

@endsection