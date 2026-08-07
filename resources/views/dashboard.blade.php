@extends('layouts.app')

@section('content')

<div class="page-header">Dashboard</div>

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

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ $totalDosen }}</span>
                <span style="font-size: 0.8rem; color: #818cf8; font-weight: 500;">Total Dosen</span>
            </div>
        </div>

        <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $totalMahasiswa }}</span>
                <span style="font-size: 0.8rem; color: #34d399; font-weight: 500;">Total Mahasiswa</span>
            </div>
        </div>

        <div class="stat-prodi-card" style="border-left: 4px solid #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.1rem; color: #d97706; display: block; line-height: 1.3;">{{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : '-' }}</span>
                <span style="font-size: 0.8rem; color: #fbbf24; font-weight: 500;">Tahun Akademik Aktif</span>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span style="font-weight: 700; font-size: 1rem; color: #1e293b;">Program Studi</span>
        </div>
        <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1rem;">Klik program studi untuk melihat kurikulum</p>

        @if($programStudis->count())
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            @foreach($programStudis as $prodi)
                <a href="{{ route('program-studi.kurikulum', $prodi->id) }}"
                   style="display: flex; flex-direction: column; padding: 1.25rem; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.1s; background: #fff;"
                   onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)';"
                   onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-weight: 600; font-size: 1rem; color: #0f172a;">{{ $prodi->nama_prodi }}</span>
                                <span style="font-size: 0.75rem; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 0.1rem 0.5rem; border-radius: 4px;">{{ $prodi->jenjang }}</span>
                            </div>
                            <p style="color: #64748b; font-size: 0.85rem; line-height: 1.6; margin-top: 0.4rem; margin-bottom: 0;">
                                @if($prodi->nama_prodi == 'Teknik Informatika')
                                    Pengembangan perangkat lunak, jaringan komputer, dan teknologi informasi.
                                @elseif($prodi->nama_prodi == 'Administrasi Bisnis')
                                    Administrasi perkantoran, manajemen bisnis, dan komunikasi profesional.
                                @elseif($prodi->nama_prodi == 'Bisnis Digital')
                                    Kewirausahaan digital, e-commerce, dan transformasi bisnis berbasis teknologi.
                                @elseif($prodi->nama_prodi == 'Teknik Rekayasa Perangkat Lunak')
                                    Rekayasa perangkat lunak skala industri dengan standar mutu internasional.
                                @elseif($prodi->nama_prodi == 'Akuntansi')
                                    Akuntansi keuangan, audit, perpajakan, dan sistem informasi akuntansi.
                                @else
                                    {{ $prodi->kode_prodi }}
                                @endif
                            </p>
                        </div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.25rem;">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
        @else
        <div class="card" style="text-align: center; padding: 3rem;">
            <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada data program studi.</p>
        </div>
        @endif
    </div>

@if(auth()->user()->isDosen() || auth()->user()->isKaprodi())
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <span style="font-weight: 700; font-size: 1rem; color: #1e293b;">Ruang Kelas LMS</span>
            @if($tahunAkademik)
                <span style="font-size: 0.75rem; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.6rem; border-radius: 6px; margin-left: auto;">
                    {{ $tahunAkademik->tahun }} {{ ucfirst($tahunAkademik->semester) }}
                </span>
            @endif
        </div>

        @if($pengampus->count())
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                @foreach($pengampus as $pengampu)
                    <div class="stat-prodi-card" style="flex-direction: column; align-items: stretch; gap: 0.75rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.9rem; color: #0f172a; line-height: 1.3;">
                                    {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                                    Kelas {{ $pengampu->kelas ?? '-' }} &middot; Semester {{ $pengampu->semester_akademik ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem; padding: 0.5rem 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                                <span style="font-weight: 600; color: #4f46e5;">{{ $pengampu->lms_materis_count }}</span>
                                <span>Materi</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                                <span style="font-weight: 600; color: #059669;">{{ $pengampu->lms_tugas_count }}</span>
                                <span>Tugas</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #475569;">
                                <span style="font-weight: 600; color: #d97706;">{{ $pengampu->submissions_belum_dinilai }}</span>
                                <span>Blm Dinilai</span>
                            </div>
                        </div>

                        <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-primary btn-sm" style="align-self: flex-start;">
                            Masuk Kelas
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2rem 1rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                </svg>
                <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 0;">Belum ada kelas yang diampu pada semester ini.</p>
            </div>
        @endif
    </div>
@endif

@endsection
