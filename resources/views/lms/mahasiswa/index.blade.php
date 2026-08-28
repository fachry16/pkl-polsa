@extends('layouts.app')

@section('content')

<div class="page-header">Kelas LMS Saya</div>

@if($tahunAkademik)
    <p class="page-subtitle">Tahun Akademik: {{ $tahunAkademik->tahun }} {{ ucfirst($tahunAkademik->semester) }}</p>
@endif

@if($pengampus->count())
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        @foreach($pengampus as $pengampu)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; transition: all 0.2s;"
                 onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)';"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: #0f172a;">
                            {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '-' }}
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;">
                            Kelas {{ $pengampu->kelas ?? '-' }} &middot; Semester {{ $pengampu->semester_akademik ?? '-' }}
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.1rem;">
                            Dosen: {{ $pengampu->dosen?->user?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; padding: 0.5rem 0; margin-top: 0.75rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #4f46e5;">{{ $pengampu->lms_materis_count }}</span>
                        <span>Materi</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #059669;">{{ $pengampu->lms_tugas_count }}</span>
                        <span>Tugas</span>
                    </div>
                </div>

                <a href="{{ route('mahasiswa.lms.show', $pengampu->id) }}" class="btn btn-primary btn-sm" style="margin-top: 0.75rem;">Buka Kelas</a>
            </div>
        @endforeach
    </div>
@else
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
        </svg>
        <p style="color: #94a3b8; font-size: 0.95rem;">Belum ada kelas yang diambil pada semester ini.</p>
    </div>
@endif

@endsection
