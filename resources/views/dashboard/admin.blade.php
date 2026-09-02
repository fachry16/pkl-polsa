{{-- Quick Action Bar --}}
<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Pusat Kendali Akademik POLSA</div>
            <div style="font-size: 0.75rem; color: #64748b;">Monitoring Sistem Paket, Kesiapan RPS &amp; Pembelajaran LMS</div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('krs.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Kelas Paket (KRS)
        </a>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Tambah Mahasiswa
        </a>
        <a href="{{ route('lms.monitor') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            Monitor Kelas LMS
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Kelola Role
        </a>
    </div>
</div>

{{-- Baris 1: 4 Kartu KPI Utama POLSA --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Tahun Akademik --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.15rem; color: #1e293b; display: block; line-height: 1.2;">
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

{{-- Baris 2: Kesiapan RPS & Progres 16 Pertemuan LMS --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
    {{-- Widget Kesiapan RPS OBE --}}
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Kesiapan RPS Mata Kuliah</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Standar Mutu Kurikulum OBE POLSA</div>
                    </div>
                </div>
                <span style="font-size: 0.8rem; font-weight: 700; background: #ecfdf5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 6px;">
                    {{ $rpsStats['persen'] }}% Siap Ajar
                </span>
            </div>

            {{-- Progress Bar RPS --}}
            <div style="width: 100%; height: 10px; background: #f1f5f9; border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 1rem;">
                <div style="width: {{ $rpsStats['persen'] }}%; background: #10b981; transition: width 0.3s;" title="Disetujui: {{ $rpsStats['disetujui'] }}"></div>
                <div style="width: {{ $rpsStats['total_mk'] > 0 ? round(($rpsStats['diajukan'] / $rpsStats['total_mk']) * 100) : 0 }}%; background: #f59e0b;" title="Diajukan: {{ $rpsStats['diajukan'] }}"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; text-align: center;">
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #059669;">{{ $rpsStats['disetujui'] }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Disetujui Kaprodi</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #d97706;">{{ $rpsStats['diajukan'] }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Menunggu Review</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #94a3b8;">{{ $rpsStats['draft'] }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Draft / Belum Ada</div>
                </div>
            </div>
        </div>
        <div style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; font-size: 0.75rem; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
            <span>Total {{ $rpsStats['total_mk'] }} Mata Kuliah di Kurikulum</span>
            <span style="color: #4f46e5; font-weight: 500;">OBE Quality Gate</span>
        </div>
    </div>

    {{-- Widget Progres 16 Pertemuan & Kehadiran LMS --}}
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Progres 16 Pertemuan LMS</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Ketercapaian Sesi Perkuliahan &amp; Presensi</div>
                    </div>
                </div>
                <span style="font-size: 0.8rem; font-weight: 700; background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.6rem; border-radius: 6px;">
                    Rata-rata Pertemuan {{ $pertemuanStats['rata_rata'] }} / 16
                </span>
            </div>

            {{-- Progress Bar Pertemuan --}}
            <div style="width: 100%; height: 10px; background: #f1f5f9; border-radius: 999px; overflow: hidden; margin-bottom: 1rem;">
                <div style="width: {{ $pertemuanStats['persen'] }}%; height: 100%; background: #4f46e5; transition: width 0.3s;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; text-align: center;">
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #4f46e5;">{{ $pertemuanStats['total_sesi'] }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Sesi Dibuka</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #0284c7;">{{ $statMateriLMS }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Total Materi Ajar</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.6rem;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #059669;">{{ $pertemuanStats['persen_kehadiran'] }}%</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">Rata-rata Hadir</div>
                </div>
            </div>
        </div>
        <div style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; font-size: 0.75rem; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
            <span>Target Total: {{ $pertemuanStats['target_sesi'] }} Sesi (16/kelas)</span>
            <a href="{{ route('lms.monitor') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Monitor Presensi &rarr;</a>
        </div>
    </div>
</div>

{{-- Baris 3: Peringatan Rombel Kosong & Antrean Penilaian LMS --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
    {{-- Peringatan Rombel Kosong --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $kelasKosong->count() > 0 ? '#ef4444' : '#10b981' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Peringatan Rombel Mahasiswa</span>
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
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">
                Kelas berikut sudah dijadwalkan di KRS namun belum ada mahasiswa rombel yang dimasukkan:
            </p>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($kelasKosong as $kk)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #fff5f5; border: 1px solid #fee2e2; border-radius: 8px;">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.82rem; color: #991b1b;">
                                {{ $kk->mataKuliah->nama ?? 'Mata Kuliah' }} (Kelas {{ $kk->kelas }})
                            </div>
                            <div style="font-size: 0.72rem; color: #7f1d1d;">
                                Dosen: {{ $kk->dosen->user->name ?? '-' }}
                            </div>
                        </div>
                        <a href="{{ route('krs.show', $kk->krs_id ?? $kk->id) }}" class="btn btn-danger btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.6rem;">
                            Plot Rombel
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 1.5rem 1rem; color: #059669;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div style="font-weight: 600; font-size: 0.85rem;">Plotting Rombel Lengkap!</div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Seluruh kelas paket aktif telah terisi mahasiswa rombel.</div>
            </div>
        @endif
    </div>

    {{-- Antrean Penilaian Tugas & Integritas Akun --}}
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Antrean Penilaian Tugas Mahasiswa</span>
                </div>
                <span style="font-size: 0.7rem; font-weight: 700; background: #fffbeb; color: #b45309; padding: 0.15rem 0.5rem; border-radius: 6px;">
                    {{ $statBelumDinilaiLMS }} Menunggu
                </span>
            </div>

            @if($kelasBelumDinilai->count() > 0)
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">
                    Kelas dengan submission tugas terbanyak yang belum diperiksa dosen:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($kelasBelumDinilai->take(3) as $kbd)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px;">
                            <div style="min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.82rem; color: #1e293b;">
                                    {{ $kbd->mataKuliah->nama ?? 'Mata Kuliah' }} ({{ $kbd->kelas }})
                                </div>
                                <div style="font-size: 0.72rem; color: #64748b;">
                                    Dosen: {{ $kbd->dosen->user->name ?? '-' }}
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                {{ $kbd->belum_dinilai_count }} Submission
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.5rem 1rem; color: #059669;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div style="font-weight: 600; font-size: 0.85rem;">Penilaian Tertib!</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Tidak ada penumpukan submission tugas di LMS.</div>
                </div>
            @endif
        </div>

        {{-- Status Akun Mahasiswa Login --}}
        <div style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem;">
                <span style="font-weight: 600; color: #1e293b;">Status Akun Login:</span>
                @if($mahasiswaTanpaAkun > 0)
                    <span style="color: #dc2626; font-weight: 600;">⚠️ {{ $mahasiswaTanpaAkun }} mhs belum punya user login</span>
                @else
                    <span style="color: #059669; font-weight: 600;">✅ Semua mhs punya user login</span>
                @endif
            </div>
            <a href="{{ route('lms.monitor') }}" style="font-size: 0.75rem; color: #4f46e5; text-decoration: none; font-weight: 500;">Buka Monitor &rarr;</a>
        </div>
    </div>
</div>

{{-- Baris 4: Tabel Rekapitulasi per Program Studi POLSA Purworejo --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Rekapitulasi Program Studi POLSA</div>
                <div style="font-size: 0.75rem; color: #64748b;">Distribusi Rombel Kelas Paket, Dosen, dan Kesiapan RPS</div>
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
                    <th style="font-size: 0.75rem; text-align: center;">Kelas A (Pagi)</th>
                    <th style="font-size: 0.75rem; text-align: center;">Kelas B (Sore)</th>
                    <th style="font-size: 0.75rem; text-align: center;">Total Paket</th>
                    <th style="font-size: 0.75rem; min-width: 140px;">Kesiapan RPS</th>
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
                            <span style="font-size: 0.75rem; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                {{ $pr->jenjang }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: 600; font-size: 0.85rem; color: #334155;">{{ $pr->dosen_count }}</td>
                        <td style="text-align: center; font-weight: 600; font-size: 0.85rem; color: #334155;">{{ $pr->mhs_count }}</td>
                        <td style="text-align: center; font-size: 0.85rem;">
                            <span style="font-weight: 600; color: #0284c7;">{{ $pr->kelas_a }}</span>
                        </td>
                        <td style="text-align: center; font-size: 0.85rem;">
                            <span style="font-weight: 600; color: #d97706;">{{ $pr->kelas_b }}</span>
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 0.85rem; color: #1e293b;">{{ $pr->total_kelas }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ $pr->rps_persen }}%; height: 100%; background: {{ $pr->rps_persen >= 80 ? '#10b981' : ($pr->rps_persen >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">{{ $pr->rps_persen }}%</span>
                            </div>
                            <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.15rem;">{{ $pr->rps_disetujui }} / {{ $pr->total_mk }} MK</div>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('program-studi.kurikulum', $pr->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                Kurikulum
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada data program studi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Baris 5: Sebaran Role Pengguna POLSA --}}
<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.25rem;">
    <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Sebaran Akun Pengguna Terdaftar POLSA
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem;">
        <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.75rem; text-align: center;">
            <div style="font-size: 0.7rem; color: #64748b;">Admin</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #1e293b;">{{ $userRoleStats['admin'] ?? 0 }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.75rem; text-align: center;">
            <div style="font-size: 0.7rem; color: #64748b;">Dosen Biasa</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #4f46e5;">{{ $userRoleStats['dosen'] ?? 0 }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.75rem; text-align: center;">
            <div style="font-size: 0.7rem; color: #64748b;">Kaprodi</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #0284c7;">{{ $userRoleStats['kaprodi'] ?? 0 }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.75rem; text-align: center;">
            <div style="font-size: 0.7rem; color: #64748b;">Direktur</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #059669;">{{ $userRoleStats['direktur'] ?? 0 }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 0.5rem 0.75rem; text-align: center;">
            <div style="font-size: 0.7rem; color: #64748b;">Mahasiswa</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #d97706;">{{ $userRoleStats['mahasiswa'] ?? 0 }}</div>
        </div>
    </div>
</div>