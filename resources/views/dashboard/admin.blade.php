{{-- Executive Admin Hero Banner --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%); border-radius: 14px; padding: 1.5rem; margin-bottom: 1.5rem; color: #fff; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.18);">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.5rem; font-weight: 700; color: #38bdf8;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-size: 0.72rem; color: #38bdf8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; display: flex; align-items: center; gap: 0.4rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Pusat Kendali Akademik POLSA
                </div>
                <div style="font-size: 1.35rem; font-weight: 800; line-height: 1.25; margin-top: 0.15rem; color: #f8fafc;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                    <span style="background: rgba(99, 102, 241, 0.25); color: #c7d2fe; border: 1px solid rgba(99, 102, 241, 0.4); padding: 0.1rem 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.72rem;">
                        Administrator Sistem
                    </span>
                    <span>&bull;</span>
                    <span>Status: <strong style="color: #34d399;">Sistem Operasional Normal</strong></span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); padding: 0.45rem 0.85rem; border-radius: 10px; text-align: right;">
                <div style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Tahun Akademik Aktif</div>
                <div style="font-size: 0.88rem; font-weight: 700; color: #f1f5f9;">
                    {{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : 'Belum Diatur' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Buttons Bar --}}
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.12); flex-wrap: wrap;">
        <a href="{{ route('krs.create') }}" class="btn btn-sm" style="background: #4f46e5; color: #fff; border: 1px solid #6366f1; display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Kelas Paket (KRS)
        </a>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-sm" style="background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Tambah Mahasiswa
        </a>
        <a href="{{ route('lms.monitor') }}" class="btn btn-sm" style="background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            Monitor Kelas LMS
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-sm" style="background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Kelola Role
        </a>
    </div>
</div>

{{-- Baris 1: 4 Kartu KPI Utama POLSA --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Tahun Akademik --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.1rem; color: #1e293b; display: block; line-height: 1.2;">
                {{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : 'Belum Diatur' }}
            </span>
            <span style="font-size: 0.75rem; color: #4f46e5; font-weight: 600;">Tahun Akademik Aktif</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Status Perkuliahan Berjalan</div>
        </div>
    </div>

    {{-- Total Mahasiswa Rombel --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $totalMahasiswa }}</span>
            <span style="font-size: 0.75rem; color: #059669; font-weight: 600;">Total Mahasiswa Rombel</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span style="color: #0284c7; font-weight: 600;">Reguler (A): {{ $mhsKelasA }}</span> &bull; 
                <span style="color: #d97706; font-weight: 600;">Karyawan (B): {{ $mhsKelasB }}</span>
            </div>
        </div>
    </div>

    {{-- Total Dosen Pengajar --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #0ea5e9;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #0284c7; display: block; line-height: 1.2;">{{ $totalDosen }}</span>
            <span style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">Total Dosen Pengajar</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Civitas Akademika Dosen POLSA</div>
        </div>
    </div>

    {{-- Total Kelas Paket Aktif --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #f59e0b;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #fffbeb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #d97706; display: block; line-height: 1.2;">{{ $statKelasLMS }}</span>
            <span style="font-size: 0.75rem; color: #d97706; font-weight: 600;">Total Kelas Paket Aktif</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>Kelas A: {{ $statKelasA }}</span> &bull; 
                <span>Kelas B: {{ $statKelasB }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: Tata Layout 2 Kolom Terstruktur (Kolom Kiri 1.3fr & Kolom Kanan 1fr) --}}
<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 1.25rem; align-items: start; margin-bottom: 1.5rem;">
    
    {{-- KOLOM KIRI: Mutu RPS, Pertemuan LMS & Rekapitulasi Prodi --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        {{-- Card Gabungan: Kesiapan RPS & 16 Pertemuan LMS --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Monitoring Kesiapan Perkuliahan &amp; LMS</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Standar Kepatuhan Mutu OBE &amp; Realisasi Pertemuan</div>
                    </div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; color: #4f46e5; background: #eef2ff; padding: 0.2rem 0.6rem; border-radius: 6px;">
                    Semester Berjalan
                </span>
            </div>

            {{-- 2 Sub-Panel: Kesiapan RPS & Progres Pertemuan --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                
                {{-- Panel A: Kesiapan RPS Mata Kuliah --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 700; font-size: 0.85rem; color: #0f172a;">Kesiapan RPS Mata Kuliah</span>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 6px;">
                            {{ $rpsStats['persen'] }}% Siap Ajar
                        </span>
                    </div>

                    {{-- Progress Bar RPS --}}
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 0.75rem;">
                        <div style="width: {{ $rpsStats['persen'] }}%; background: #10b981; transition: width 0.3s;" title="Disetujui: {{ $rpsStats['disetujui'] }}"></div>
                        <div style="width: {{ $rpsStats['total_mk'] > 0 ? round(($rpsStats['diajukan'] / $rpsStats['total_mk']) * 100) : 0 }}%; background: #f59e0b;" title="Diajukan: {{ $rpsStats['diajukan'] }}"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; text-align: center;">
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #059669;">{{ $rpsStats['disetujui'] }}</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Disetujui</div>
                        </div>
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #d97706;">{{ $rpsStats['diajukan'] }}</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Review</div>
                        </div>
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #94a3b8;">{{ $rpsStats['draft'] }}</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Draft</div>
                        </div>
                    </div>
                </div>

                {{-- Panel B: Progres 16 Pertemuan LMS --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 700; font-size: 0.85rem; color: #0f172a;">Progres 16 Pertemuan LMS</span>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 6px;">
                            {{ $pertemuanStats['rata_rata'] }} / 16 Sesi
                        </span>
                    </div>

                    {{-- Progress Bar Pertemuan --}}
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-bottom: 0.75rem;">
                        <div style="width: {{ $pertemuanStats['persen'] }}%; height: 100%; background: #4f46e5; transition: width 0.3s;"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; text-align: center;">
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #4f46e5;">{{ $pertemuanStats['total_sesi'] }}</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Sesi Buka</div>
                        </div>
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #0284c7;">{{ $statMateriLMS }}</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Materi</div>
                        </div>
                        <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 6px; padding: 0.45rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: #059669;">{{ $pertemuanStats['persen_kehadiran'] }}%</div>
                            <div style="font-size: 0.65rem; color: #64748b;">Hadir</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #64748b;">
                <span>Total <strong>{{ $rpsStats['total_mk'] }}</strong> Mata Kuliah Terdaftar &bull; Target Sesi: <strong>{{ $pertemuanStats['target_sesi'] }}</strong></span>
                <a href="{{ route('lms.monitor') }}" style="color: #4f46e5; font-weight: 600; text-decoration: none;">Monitor Presensi &rarr;</a>
            </div>
        </div>

        {{-- Card: Rekapitulasi Program Studi POLSA --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Rekapitulasi Program Studi POLSA</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Distribusi Rombel Kelas Paket, Dosen, dan Kesiapan RPS</div>
                    </div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; background: #f1f5f9; color: #475569; padding: 0.2rem 0.6rem; border-radius: 6px;">
                    {{ $prodiRecaps->count() }} Program Studi
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="font-size: 0.75rem;">Program Studi</th>
                            <th style="font-size: 0.75rem; text-align: center;">Jenjang</th>
                            <th style="font-size: 0.75rem; text-align: center;">Dosen</th>
                            <th style="font-size: 0.75rem; text-align: center;">Mahasiswa</th>
                            <th style="font-size: 0.75rem; text-align: center;">Kelas A/B</th>
                            <th style="font-size: 0.75rem; min-width: 120px;">Kesiapan RPS</th>
                            <th style="font-size: 0.75rem; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodiRecaps as $pr)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $pr->nama_prodi }}</div>
                                    <div style="font-size: 0.7rem; color: #94a3b8;">Kode: {{ $pr->kode_prodi }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 0.72rem; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 0.12rem 0.45rem; border-radius: 4px;">
                                        {{ $pr->jenjang }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 600; font-size: 0.82rem; color: #334155;">{{ $pr->dosen_count }}</td>
                                <td style="text-align: center; font-weight: 600; font-size: 0.82rem; color: #334155;">{{ $pr->mhs_count }}</td>
                                <td style="text-align: center; font-size: 0.8rem;">
                                    <span style="font-weight: 600; color: #0284c7;">{{ $pr->kelas_a }}</span>
                                    <span style="color: #cbd5e1;">/</span>
                                    <span style="font-weight: 600; color: #d97706;">{{ $pr->kelas_b }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                                            <div style="width: {{ $pr->rps_persen }}%; height: 100%; background: {{ $pr->rps_persen >= 80 ? '#10b981' : ($pr->rps_persen >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                        </div>
                                        <span style="font-size: 0.72rem; font-weight: 700; color: #475569;">{{ $pr->rps_persen }}%</span>
                                    </div>
                                    <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem;">{{ $pr->rps_disetujui }} / {{ $pr->total_mk }} MK</div>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('program-studi.kurikulum', $pr->id) }}" class="btn btn-secondary btn-xs" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                        Kurikulum
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada data program studi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Action Center, Peringatan & Sebaran Akun --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        {{-- Card: Pusat Peringatan Akademik (Rombel & Tugas) --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.6rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <div style="width: 30px; height: 30px; border-radius: 8px; background: {{ $kelasKosong->count() > 0 ? '#fee2e2' : '#ecfdf5' }}; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $kelasKosong->count() > 0 ? '#dc2626' : '#059669' }}" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Peringatan Rombel Mahasiswa</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Keterisian Mahasiswa pada Kelas KRS</div>
                    </div>
                </div>
                @if($kelasKosong->count() > 0)
                    <span style="font-size: 0.7rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.15rem 0.5rem; border-radius: 6px;">
                        {{ $kelasKosong->count() }} Kelas Kosong
                    </span>
                @else
                    <span style="font-size: 0.7rem; font-weight: 700; background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 6px;">
                        Semua Terisi
                    </span>
                @endif
            </div>

            @if($kelasKosong->count() > 0)
                <p style="font-size: 0.78rem; color: #64748b; margin-bottom: 0.65rem;">
                    Kelas berikut dijadwalkan di KRS namun belum ada mahasiswa rombel yang dimasukkan:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 0.85rem;">
                    @foreach($kelasKosong as $kk)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.65rem; background: #fff5f5; border: 1px solid #fee2e2; border-radius: 8px;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-weight: 600; font-size: 0.8rem; color: #991b1b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $kk->mataKuliah->nama ?? 'Mata Kuliah' }} ({{ $kk->kelas }})
                                </div>
                                <div style="font-size: 0.7rem; color: #7f1d1d;">
                                    {{ $kk->dosen->user->name ?? 'Dosen' }}
                                </div>
                            </div>
                            <a href="{{ route('krs.show', $kk->krs_id ?? $kk->id) }}" class="btn btn-danger btn-xs" style="font-size: 0.68rem; padding: 0.2rem 0.5rem; margin-left: 0.5rem;">
                                Plot Rombel
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1rem 0.5rem; color: #059669; background: #f0fdf4; border-radius: 8px; margin-bottom: 0.85rem;">
                    <div style="font-weight: 700; font-size: 0.82rem;">Plotting Rombel Lengkap!</div>
                    <div style="font-size: 0.72rem; color: #166534; margin-top: 0.15rem;">Seluruh kelas paket aktif telah terisi mahasiswa.</div>
                </div>
            @endif

            {{-- Antrean Penilaian Tugas --}}
            <div style="border-top: 1px solid #f1f5f9; padding-top: 0.85rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                    <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">Antrean Penilaian Tugas Mahasiswa</span>
                    <span style="font-size: 0.7rem; font-weight: 700; background: #fffbeb; color: #b45309; padding: 0.15rem 0.45rem; border-radius: 6px;">
                        {{ $statBelumDinilaiLMS }} Menunggu
                    </span>
                </div>

                @if($kelasBelumDinilai->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        @foreach($kelasBelumDinilai->take(3) as $kbd)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="font-weight: 600; font-size: 0.78rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $kbd->mataKuliah->nama ?? 'Mata Kuliah' }} ({{ $kbd->kelas }})
                                    </div>
                                    <div style="font-size: 0.68rem; color: #64748b;">
                                        {{ $kbd->dosen->user->name ?? '-' }}
                                    </div>
                                </div>
                                <span style="font-size: 0.7rem; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.5rem; flex-shrink: 0;">
                                    {{ $kbd->belum_dinilai_count }} Menunggu
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="font-size: 0.75rem; color: #059669; text-align: center; padding: 0.5rem;">
                        ✅ Seluruh tugas mahasiswa telah selesai dinilai dosen.
                    </div>
                @endif
            </div>

            {{-- Status Akun Mahasiswa Login --}}
            <div style="margin-top: 0.85rem; border-top: 1px solid #f1f5f9; padding-top: 0.65rem; font-size: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    @if($mahasiswaTanpaAkun > 0)
                        <span style="color: #dc2626; font-weight: 600;">⚠️ {{ $mahasiswaTanpaAkun }} mhs belum punya akun login</span>
                    @else
                        <span style="color: #059669; font-weight: 600;">✅ Semua mahasiswa punya akun login</span>
                    @endif
                </div>
                <a href="{{ route('lms.monitor') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">Buka Monitor &rarr;</a>
            </div>
        </div>

        {{-- Card: Sebaran Akun Pengguna Terdaftar POLSA --}}
        <div class="card">
            <div style="font-weight: 700; font-size: 0.88rem; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <span>Sebaran Akun Pengguna Terdaftar POLSA</span>
                </div>
                <a href="{{ route('roles.index') }}" style="font-size: 0.72rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Role &rarr;</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 0.5rem;">
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.4rem; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">Admin</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #1e293b; margin-top: 0.1rem;">{{ $userRoleStats['admin'] ?? 0 }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.4rem; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">Dosen</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #4f46e5; margin-top: 0.1rem;">{{ $userRoleStats['dosen'] ?? 0 }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.4rem; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">Kaprodi</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #0284c7; margin-top: 0.1rem;">{{ $userRoleStats['kaprodi'] ?? 0 }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.4rem; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">Direktur</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #059669; margin-top: 0.1rem;">{{ $userRoleStats['direktur'] ?? 0 }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.4rem; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">Mahasiswa</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #d97706; margin-top: 0.1rem;">{{ $userRoleStats['mahasiswa'] ?? 0 }}</div>
                </div>
            </div>
        </div>

    </div>
</div>