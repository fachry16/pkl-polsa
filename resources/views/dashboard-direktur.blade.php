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

@include('dashboard.direktur')

@endsection