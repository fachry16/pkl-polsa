@extends('layouts.app')

@section('content')

<h1 class="page-header">Struktur Kurikulum</h1>

<p class="mb-5" style="color: #64748b;">
    {{ $kurikulum->nama_kurikulum }}
    —
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">

    <a href="{{ route('kurikulum.profil-lulusan.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Profil Lulusan</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Kompetensi & capaian</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.cpl.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#a7f3d0';this.style.boxShadow='0 4px 16px rgba(16,185,129,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">CPL</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Capaian pembelajaran</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.cpmk.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#fecaca';this.style.boxShadow='0 4px 16px rgba(239,68,68,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">CPMK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Sub-capaian</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.bahan-kajian.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Bahan Kajian</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Materi & referensi</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.cpl-pl.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#fde68a';this.style.boxShadow='0 4px 16px rgba(245,158,11,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">PL – CPL</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Matriks mapping</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.bk-mk.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">BK – MK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Matriks mapping</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.cpl-bk-mk.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#fecaca';this.style.boxShadow='0 4px 16px rgba(239,68,68,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5"/><line x1="12" y1="22" x2="12" y2="15.5"/><polyline points="22 8.5 12 15.5 2 8.5"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">CPL – BK – MK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Matriks mapping</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.cpl-cpmk-mk.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#fde68a';this.style.boxShadow='0 4px 16px rgba(245,158,11,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">CPL – CPMK – MK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Matriks mapping</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.pemenuhan-cpl.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Pemenuhan CPL</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Capaian per semester</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.mata-kuliah.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#cbd5e1';this.style.boxShadow='0 4px 16px rgba(71,85,105,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/><path d="M8 7h8"/><path d="M8 11h6"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Mata Kuliah</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Daftar & kelola MK</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.struktur', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#cbd5e1';this.style.boxShadow='0 4px 16px rgba(71,85,105,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Struktur MK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Sebaran semester</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#a7f3d0';this.style.boxShadow='0 4px 16px rgba(16,185,129,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Metode & Bobot Penilaian</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Metode penilaian</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#fde68a';this.style.boxShadow='0 4px 16px rgba(245,158,11,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Rumusan Nilai Akhir MK</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Skor per MK</div>
        </div>
    </a>

    <a href="{{ route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id) }}"
       style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; background: #fff; border: 1px solid #e2e8f0; transition: all 0.1s; text-align: center;"
       onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Rumusan Nilai Akhir CPL</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;">Skor per CPL</div>
        </div>
    </a>

</div>

<div class="mt-4">
    <a href="{{ route('program-studi.kurikulum', $kurikulum->program_studi_id) }}"
       class="btn btn-secondary">Kembali</a>
</div>

@endsection
