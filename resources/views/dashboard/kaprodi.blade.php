@if(!$kaprodiProdi)
    <div class="card" style="text-align: center; padding: 2rem;">
        <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 0;">Akun Anda memiliki role Kaprodi, tetapi belum terhubung ke data Master Dosen dan Program Studi.</p>
    </div>
@else
    {{-- Header Banner Kaprodi --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem; font-weight: 700; color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                🎓
            </div>
            <div>
                <div style="font-size: 0.72rem; color: #93c5fd; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pusat Kendali Program Studi</div>
                <div style="font-size: 1.25rem; font-weight: 700; line-height: 1.2; margin-top: 0.15rem;">
                    {{ $kaprodiProdi->nama_prodi }} ({{ $kaprodiProdi->jenjang }})
                </div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span>Kaprodi: <strong>{{ auth()->user()->name }}</strong></span>
                    <span>&bull;</span>
                    <span>Akreditasi: <strong style="color: #6ee7b7;">{{ $kaprodiProdi->akreditasi ?? 'Baik' }}</strong></span>
                    <span>&bull;</span>
                    <span>{{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : 'Semester Aktif' }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <a href="{{ route('krs.index') }}" class="btn btn-primary btn-sm" style="font-size: 0.75rem;">
                + Plotting KRS Paket
            </a>
            <a href="{{ route('program-studi.kurikulum', $kaprodiProdi->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                Kurikulum Prodi
            </a>
            <a href="{{ route('rps.pengajuan') }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                Review RPS ({{ $kaprodiRpsStats['diajukan'] }})
            </a>
        </div>
    </div>

    {{-- Baris 1: 4 KPI Cards Khusus Program Studi --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Total Mahasiswa Prodi --}}
        <div class="stat-prodi-card" style="border-left: 4px solid #3b82f6;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #2563eb; display: block; line-height: 1.2;">{{ $mhsProdiTotal }}</span>
                <span style="font-size: 0.75rem; color: #2563eb; font-weight: 600;">Mahasiswa Aktif Prodi</span>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                    <span>Reguler (A): {{ $mhsProdiKelasA }}</span> &bull; 
                    <span>Karyawan (B): {{ $mhsProdiKelasB }}</span>
                </div>
            </div>
        </div>

        {{-- Total Dosen Homebase --}}
        <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $dosenProdiTotal }}</span>
                <span style="font-size: 0.75rem; color: #059669; font-weight: 600;">Dosen Homebase Prodi</span>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Tenaga Pengajar {{ $kaprodiProdi->kode_prodi }}</div>
            </div>
        </div>

        {{-- Total Kelas Paket KRS --}}
        <div class="stat-prodi-card" style="border-left: 4px solid #8b5cf6;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: #7c3aed; display: block; line-height: 1.2;">{{ $totalKelasPaketProdi }} Kelas</span>
                <span style="font-size: 0.75rem; color: #7c3aed; font-weight: 600;">Kelas Paket KRS</span>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                    <span>Kelas A: {{ $krsProdiKelasA }}</span> &bull; 
                    <span>Kelas B: {{ $krsProdiKelasB }}</span>
                </div>
            </div>
        </div>

        {{-- Antrean Approval RPS --}}
        <div class="stat-prodi-card" style="border-left: 4px solid {{ $kaprodiRpsStats['diajukan'] > 0 ? '#f59e0b' : '#10b981' }};">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $kaprodiRpsStats['diajukan'] > 0 ? '#fffbeb' : '#ecfdf5' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $kaprodiRpsStats['diajukan'] > 0 ? '#d97706' : '#059669' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <span style="font-weight: 700; font-size: 1.35rem; color: {{ $kaprodiRpsStats['diajukan'] > 0 ? '#d97706' : '#059669' }}; display: block; line-height: 1.2;">
                    {{ $kaprodiRpsStats['diajukan'] }}
                </span>
                <span style="font-size: 0.75rem; color: {{ $kaprodiRpsStats['diajukan'] > 0 ? '#d97706' : '#059669' }}; font-weight: 600;">RPS Butuh Review</span>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                    {{ $kaprodiRpsStats['diajukan'] > 0 ? 'Menunggu persetujuan Anda' : 'Semua RPS sudah diperiksa' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Action Items Kaprodi (Antrean Review RPS & Rombel Kosong) --}}
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
        {{-- Antrean Review & Persetujuan RPS --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Pengajuan RPS Menunggu Review</div>
                            <div style="font-size: 0.75rem; color: #64748b;">RPS yang diajukan dosen pengampu di prodi {{ $kaprodiProdi->kode_prodi }}</div>
                        </div>
                    </div>
                    <a href="{{ route('rps.pengajuan') }}" style="font-size: 0.75rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
                </div>

                @if($rpsDiajukanProdi->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                        @foreach($rpsDiajukanProdi->take(4) as $rps)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px;">
                                <div style="min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #92400e;">{{ $rps->mataKuliah->kode }} - {{ $rps->mataKuliah->nama }}</div>
                                    <div style="font-size: 0.72rem; color: #b45309; margin-top: 0.15rem;">
                                        Dosen: <strong>{{ $rps->dosen_pengembang_rps ?? $rps->dosen_pengampu ?? 'Dosen Pengampu' }}</strong> &bull; {{ $rps->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                                <a href="{{ route('mata-kuliah.rps.show', [$rps->mata_kuliah_id, $rps->id]) }}" class="btn btn-primary btn-sm" style="font-size: 0.7rem; padding: 0.25rem 0.6rem;">
                                    Review &amp; Setujui
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 1rem; color: #059669;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <div style="font-weight: 600; font-size: 0.85rem;">Tidak Ada RPS Menunggu Review</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Semua pengajuan RPS mata kuliah telah diproses.</div>
                    </div>
                @endif
            </div>
            <div style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem; font-size: 0.72rem; color: #64748b; text-align: right;">
                Kesiapan RPS menjamin kesesuaian pembelajaran dengan CPL Prodi.
            </div>
        </div>

        {{-- Monitoring Rombel Kosong Prodi (Zero-Student Alert) --}}
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #fee2e2; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Peringatan Rombel Kosong</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Kelas paket KRS yang belum diisi mahasiswa</div>
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; font-weight: 700; background: {{ $rombelKosongProdi->count() > 0 ? '#fee2e2' : '#ecfdf5' }}; color: {{ $rombelKosongProdi->count() > 0 ? '#dc2626' : '#059669' }}; padding: 0.15rem 0.5rem; border-radius: 6px;">
                        {{ $rombelKosongProdi->count() }} Kelas
                    </span>
                </div>

                @if($rombelKosongProdi->count() > 0)
                    <p style="font-size: 0.8rem; color: #dc2626; margin-bottom: 0.6rem;">
                        Perhatian: Rombel berikut telah dibuat tetapi belum di-plot mahasiswanya:
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($rombelKosongProdi->take(4) as $krs)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px;">
                                <div style="min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.82rem; color: #9f1239;">{{ $krs->mataKuliah->nama ?? 'Mata Kuliah' }}</div>
                                    <div style="font-size: 0.7rem; color: #be123c;">
                                        Kelas {{ $krs->kelas }} &bull; Dosen: {{ $krs->dosen->user->name ?? '-' }}
                                    </div>
                                </div>
                                <a href="{{ route('krs.mahasiswa', $krs->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; background: #e11d48; border-color: #e11d48;">
                                    Plot Mhs
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 1rem; color: #059669;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <div style="font-weight: 600; font-size: 0.85rem;">Semua Rombel Terisi!</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Seluruh kelas paket prodi telah memiliki mahasiswa terdaftar.</div>
                    </div>
                @endif
            </div>
            <div style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                <a href="{{ route('krs.index') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Buka Manajemen KRS &rarr;</a>
                <span style="color: #64748b;">Sistem Paket POLSA</span>
            </div>
        </div>
    </div>

    {{-- Baris 3: Kesiapan RPS Kurikulum Program Studi --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Kesiapan RPS Kurikulum {{ $kaprodiProdi->kode_prodi }}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Status RPS seluruh mata kuliah kurikulum aktif</div>
                </div>
            </div>
            <div style="text-align: right;">
                <span style="font-weight: 700; font-size: 1.1rem; color: #059669;">{{ $kaprodiRpsStats['persen'] }}%</span>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">RPS Disetujui</span>
            </div>
        </div>

        {{-- Progress Bar Kesiapan RPS --}}
        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 1rem;">
            @php
                $persenDisetujui = $kaprodiRpsStats['total_mk'] > 0 ? ($kaprodiRpsStats['disetujui'] / $kaprodiRpsStats['total_mk']) * 100 : 0;
                $persenDiajukan = $kaprodiRpsStats['total_mk'] > 0 ? ($kaprodiRpsStats['diajukan'] / $kaprodiRpsStats['total_mk']) * 100 : 0;
                $persenDraft = $kaprodiRpsStats['total_mk'] > 0 ? ($kaprodiRpsStats['draft'] / $kaprodiRpsStats['total_mk']) * 100 : 0;
            @endphp
            <div style="width: {{ $persenDisetujui }}%; background: #10b981;" title="Disetujui: {{ $kaprodiRpsStats['disetujui'] }} MK"></div>
            <div style="width: {{ $persenDiajukan }}%; background: #f59e0b;" title="Menunggu Review: {{ $kaprodiRpsStats['diajukan'] }} MK"></div>
            <div style="width: {{ $persenDraft }}%; background: #cbd5e1;" title="Draft / Belum Ada: {{ $kaprodiRpsStats['draft'] }} MK"></div>
        </div>

        <div style="display: flex; gap: 1.5rem; font-size: 0.75rem; color: #64748b; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                <span><strong>{{ $kaprodiRpsStats['disetujui'] }}</strong> Disetujui</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                <span><strong>{{ $kaprodiRpsStats['diajukan'] }}</strong> Menunggu Review Kaprodi</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1;"></span>
                <span><strong>{{ $kaprodiRpsStats['draft'] }}</strong> Draft / Belum Diajukan</span>
            </div>
            <div style="margin-left: auto;">
                <a href="{{ route('program-studi.kurikulum', $kaprodiProdi->id) }}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">
                    Kelola Kurikulum &amp; RPS &rarr;
                </a>
            </div>
        </div>
    </div>
@endif