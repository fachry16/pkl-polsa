@extends('layouts.app')

@section('content')

<div class="page-header">Monitoring LMS {{ $programStudi ? '- '.$programStudi->nama_prodi : '' }}</div>

<x-alert type="success" :message="session('success')" />

@if($pengampus->count())
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; margin-top: 1.25rem;">
        @foreach($pengampus as $kelas)
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; transition: all 0.2s;"
                 onmouseover="this.style.borderColor='#FFE88F';this.style.boxShadow='0 4px 16px rgba(254,194,0,0.08)';"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #FFF3C4; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0A0D40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: #0f172a;">
                            {{ $kelas->mataKuliah->kode ?? '' }} - {{ $kelas->mataKuliah->nama ?? '-' }}
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;">
                            {{ $kelas->label_semester }} &middot; Kelas {{ $kelas->kelas ?? '-' }}
                            <span style="color: #94a3b8;">&middot; {{ $kelas->tahunAkademik?->tahun }} {{ ucfirst($kelas->tahunAkademik?->semester ?? '') }}</span>
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem;">
                            {{ $kelas->dosen?->user?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; padding: 0.5rem 0; margin-top: 0.75rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #A16207;">{{ $kelas->mahasiswas_count }}</span>
                        <span>Mahasiswa</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #A16207;">{{ $kelas->lms_materis_count }}</span>
                        <span>Materi</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #059669;">{{ $kelas->lms_tugas_count }}</span>
                        <span>Tugas</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #A16207;">{{ $kelas->lms_forum_diskusis_count }}</span>
                        <span>Diskusi</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                        <span style="font-weight: 600; color: #d97706;">{{ $kelas->submissions_belum_dinilai }}</span>
                        <span>Blm Dinilai</span>
                    </div>
                    @if(($kelas->pengajuan_menunggu ?? 0) > 0)
                        <a href="{{ route('assessment.pengajuan') }}" title="Ada pengajuan nilai menunggu persetujuan" style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #dc2626; text-decoration: none;">
                            <span style="width: 20px; height: 20px; border-radius: 50%; background: #dc2626; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">!</span>
                            <span style="font-weight: 600;">Pengajuan Nilai</span>
                        </a>
                    @endif
                </div>

                <div style="margin-top: 0.75rem;">
                    <a href="{{ route('lms.show', $kelas->id) }}" class="btn btn-primary btn-sm">Buka Kelas</a>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top: 1rem;">
        {{ $pengampus->links() }}
    </div>
@else
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem; margin-top: 1.25rem;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
        </svg>
        <p style="color: #94a3b8; font-size: 0.95rem;">Belum ada data kelas LMS pada prodi ini.</p>
    </div>
@endif

@endsection