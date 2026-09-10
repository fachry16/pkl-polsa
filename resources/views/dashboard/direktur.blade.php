{{-- Header Banner Direktur --}}
<div class="role-hero-banner" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div class="hero-avatar" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.35rem; font-weight: 700; color: #a7f3d0; border: 1px solid rgba(255, 255, 255, 0.2);">
            🏛️
        </div>
        <div>
            <div style="font-size: 0.72rem; color: #a7f3d0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Dashboard Eksekutif &amp; Tata Kelola</div>
            <div class="hero-title" style="font-size: 1.25rem; font-weight: 700; line-height: 1.2; margin-top: 0.15rem;">Politeknik Sawunggalih Aji (POLSA)</div>
            <div style="font-size: 0.8rem; color: #d1fae5; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span>Direktur: <strong>{{ auth()->user()->name }}</strong></span>
                <span>&bull;</span>
                <span>Semester: <strong>{{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : 'Aktif' }}</strong></span>
            </div>
        </div>
    </div>
    <div>
        <span style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; color: #ecfdf5;">
            Laporan Kinerja Institusi
        </span>
    </div>
</div>

{{-- Baris 1: 4 KPI Cards Eksekutif --}}
<div class="dashboard-kpi-grid">
    <div class="stat-prodi-card" style="border-left: 4px solid #059669;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $programStudis->count() }} Prodi</span>
            <span style="font-size: 0.75rem; color: #059669; font-weight: 600;">Program Studi Aktif</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Vokasi D3 &amp; D4 Sarjana Terapan</div>
        </div>
    </div>

    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ $totalDosen }}</span>
            <span style="font-size: 0.75rem; color: #4f46e5; font-weight: 600;">Dosen Pengajar POLSA</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Tenaga Pengajar Terdaftar</div>
        </div>
    </div>

    <div class="stat-prodi-card" style="border-left: 4px solid #0ea5e9;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #0284c7; display: block; line-height: 1.2;">{{ $totalMahasiswa }}</span>
            <span style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">Total Mahasiswa POLSA</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>Reguler: {{ $mhsKelasA }}</span> &bull; 
                <span>Karyawan: {{ $mhsKelasB }}</span>
            </div>
        </div>
    </div>

    <div class="stat-prodi-card" style="border-left: 4px solid #8b5cf6;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #7c3aed; display: block; line-height: 1.2;">{{ $statKelasA + $statKelasB }} Kelas</span>
            <span style="font-size: 0.75rem; color: #7c3aed; font-weight: 600;">Kelas Paket Berjalan</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>Kelas A: {{ $statKelasA }}</span> &bull; 
                <span>Kelas B: {{ $statKelasB }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: Capaian Mutu OBE & 16 Pertemuan Perkuliahan POLSA --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
    {{-- Kesiapan RPS OBE Institusi --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Kesiapan RPS Kurikulum POLSA</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Standar mutu OBE di seluruh program studi</div>
                </div>
            </div>
            <span style="font-weight: 700; font-size: 1.1rem; color: #059669;">{{ $rpsStats['persen'] }}%</span>
        </div>

        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 0.75rem;">
            @php
                $persenDisetujui = $rpsStats['total_mk'] > 0 ? ($rpsStats['disetujui'] / $rpsStats['total_mk']) * 100 : 0;
                $persenDiajukan = $rpsStats['total_mk'] > 0 ? ($rpsStats['diajukan'] / $rpsStats['total_mk']) * 100 : 0;
                $persenDraft = $rpsStats['total_mk'] > 0 ? ($rpsStats['draft'] / $rpsStats['total_mk']) * 100 : 0;
            @endphp
            <div style="width: {{ $persenDisetujui }}%; background: #10b981;" title="Disetujui: {{ $rpsStats['disetujui'] }} MK"></div>
            <div style="width: {{ $persenDiajukan }}%; background: #f59e0b;" title="Diajukan: {{ $rpsStats['diajukan'] }} MK"></div>
            <div style="width: {{ $persenDraft }}%; background: #cbd5e1;" title="Draft: {{ $rpsStats['draft'] }} MK"></div>
        </div>

        <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: #64748b; flex-wrap: wrap;">
            <span><strong style="color: #059669;">{{ $rpsStats['disetujui'] }}</strong> Disetujui</span>
            <span><strong style="color: #d97706;">{{ $rpsStats['diajukan'] }}</strong> Diajukan</span>
            <span><strong style="color: #64748b;">{{ $rpsStats['draft'] }}</strong> Draft</span>
            <span style="margin-left: auto;">Total: {{ $rpsStats['total_mk'] }} Mata Kuliah</span>
        </div>
    </div>

    {{-- Capaian 16 Pertemuan & Presensi --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0f2fe; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Keterlaksanaan Perkuliahan LMS</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Rata-rata pertemuan &amp; presensi mahasiswa</div>
                </div>
            </div>
            <span style="font-weight: 700; font-size: 1.1rem; color: #0284c7;">{{ $pertemuanStats['persen'] }}%</span>
        </div>

        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-bottom: 0.75rem;">
            <div style="width: {{ $pertemuanStats['persen'] }}%; height: 100%; background: #0284c7; border-radius: 999px;"></div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b;">
            <span>Rata-rata: <strong>{{ $pertemuanStats['rata_rata'] }} / 16 Pertemuan</strong></span>
            <span>Kehadiran Mahasiswa: <strong style="color: #059669;">{{ $pertemuanStats['persen_kehadiran'] }}%</strong></span>
        </div>
    </div>
</div>

{{-- Baris 3: Rekapitulasi per Program Studi POLSA --}}
<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Rekapitulasi Program Studi POLSA</div>
                <div style="font-size: 0.75rem; color: #64748b;">Distribusi dosen, mahasiswa, dan kesiapan kurikulum per program studi</div>
            </div>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">Program Studi</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Jenjang</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Dosen</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Mahasiswa</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Kelas A (Reg)</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Kelas B (Kar)</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Total Rombel</th>
                    <th style="padding: 0.75rem 1rem;">Kesiapan RPS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prodiRecaps as $recap)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 0.75rem 1rem;">
                            <div style="font-weight: 600; color: #0f172a;">{{ $recap->nama_prodi }}</div>
                            <div style="font-size: 0.72rem; color: #64748b;">Kode: {{ $recap->kode_prodi }}</div>
                        </td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center;">
                            <span style="background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">
                                {{ $recap->jenjang }}
                            </span>
                        </td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 600; color: #334155;">{{ $recap->dosen_count }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 600; color: #0369a1;">{{ $recap->mhs_count }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; color: #475569;">{{ $recap->kelas_a }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; color: #475569;">{{ $recap->kelas_b }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 700; color: #4f46e5;">{{ $recap->total_kelas }}</td>
                        <td style="padding: 0.75rem 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ $recap->rps_persen }}%; height: 100%; background: #10b981; border-radius: 999px;"></div>
                                </div>
                                <span style="font-weight: 700; font-size: 0.75rem; color: #059669; width: 35px; text-align: right;">{{ $recap->rps_persen }}%</span>
                            </div>
                            <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.15rem;">{{ $recap->rps_disetujui }}/{{ $recap->total_mk }} Disetujui</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Baris 4: Monitoring Kurikulum --}}
<div class="card" style="margin-top: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/><path d="M8 7h8"/><path d="M8 11h6"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Monitoring Semua Kurikulum</div>
                <div style="font-size: 0.75rem; color: #64748b;">Daftar seluruh kurikulum di setiap program studi</div>
            </div>
        </div>
        <span style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700;">{{ $direkturKurikulums->count() }} Kurikulum</span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">No</th>
                    <th style="padding: 0.75rem 0.75rem;">Program Studi</th>
                    <th style="padding: 0.75rem 0.75rem;">Nama Kurikulum</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Tahun Berlaku</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Beban Studi (SKS)</th>
                    <th style="padding: 0.75rem 0.75rem;">Deskripsi</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($direkturKurikulums as $i => $k)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 0.75rem 1rem; color: #64748b;">{{ $i + 1 }}</td>
                        <td style="padding: 0.75rem 0.75rem; font-weight: 600; color: #0f172a;">{{ $k->programStudi->nama_prodi ?? '-' }}</td>
                        <td style="padding: 0.75rem 0.75rem; color: #334155;">{{ $k->nama_kurikulum }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; color: #475569;">{{ $k->tahun_berlaku }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 600; color: #4f46e5;">{{ $k->beban_studi ?? '-' }}</td>
                        <td style="padding: 0.75rem 0.75rem; color: #64748b; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $k->deskripsi ?? '-' }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center;">
                            @if(($k->status ?? '') === 'Aktif' || ($k->status ?? '') === 'aktif')
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                            @elseif(($k->status ?? '') === 'Draft' || ($k->status ?? '') === 'draft')
                                <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Draft</span>
                            @else
                                <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">{{ $k->status ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 1.5rem; text-align: center; color: #6b7280;">Belum ada data kurikulum.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Baris 5: Monitoring Tahun Akademik --}}
<div class="card" style="margin-top: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0f2fe; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Monitoring Tahun Akademik</div>
                <div style="font-size: 0.75rem; color: #64748b;">Riwayat dan status seluruh tahun akademik</div>
            </div>
        </div>
        <span style="background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700;">{{ $direkturTahunAkademiks->count() }} Semester</span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">No</th>
                    <th style="padding: 0.75rem 0.75rem;">Tahun</th>
                    <th style="padding: 0.75rem 0.75rem;">Semester</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Total Kelas (Rombel)</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Total Mahasiswa</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($direkturTahunAkademiks as $i => $ta)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 0.75rem 1rem; color: #64748b;">{{ $i + 1 }}</td>
                        <td style="padding: 0.75rem 0.75rem; font-weight: 600; color: #0f172a;">{{ $ta->tahun }}</td>
                        <td style="padding: 0.75rem 0.75rem; color: #334155;">{{ $ta->semester }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 600; color: #4f46e5;">{{ $ta->pengampus_count }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center; font-weight: 600; color: #0369a1;">{{ $ta->semesterMahasiswas_count }}</td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center;">
                            @if($ta->is_active)
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                            @else
                                <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1.5rem; text-align: center; color: #6b7280;">Belum ada data tahun akademik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Baris 6: Monitoring Data Dosen --}}
<div class="card" style="margin-top: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Monitoring Data Dosen</div>
                <div style="font-size: 0.75rem; color: #64748b;">Daftar seluruh dosen beserta program studi dan jabatan</div>
            </div>
        </div>
        <span style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700;">{{ $direkturDosens->count() }} Dosen</span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 0.75rem 1rem;">No</th>
                    <th style="padding: 0.75rem 0.75rem;">Nama</th>
                    <th style="padding: 0.75rem 0.75rem;">NIDN</th>
                    <th style="padding: 0.75rem 0.75rem;">Program Studi</th>
                    <th style="padding: 0.75rem 0.75rem;">Jabatan / Role</th>
                    <th style="padding: 0.75rem 0.75rem; text-align: center;">Akun</th>
                </tr>
            </thead>
            <tbody>
                @forelse($direkturDosens as $i => $dsn)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 0.75rem 1rem; color: #64748b;">{{ $i + 1 }}</td>
                        <td style="padding: 0.75rem 0.75rem; font-weight: 600; color: #0f172a;">{{ $dsn->user->name ?? '-' }}</td>
                        <td style="padding: 0.75rem 0.75rem; color: #334155;">{{ $dsn->nidn }}</td>
                        <td style="padding: 0.75rem 0.75rem; color: #475569;">{{ $dsn->programStudi->nama_prodi ?? '-' }}</td>
                        <td style="padding: 0.75rem 0.75rem;">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                @php
                                    $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
                                    $roles = $dsn->user ? $dsn->user->getRolesList() : ['dosen'];
                                @endphp
                                @foreach($roles as $r)
                                    @php
                                        $roleName = $roleMap->get($r) ?? ucfirst(str_replace('_', ' ', $r));
                                    @endphp
                                    @if($r === 'admin')
                                        <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 600;">Admin</span>
                                    @elseif($r === 'direktur' || str_starts_with($r, 'direktur'))
                                        <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 600;">{{ $roleName }}</span>
                                    @elseif($r === 'kaprodi' || str_starts_with($r, 'kaprodi'))
                                        <span style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 600;">{{ $roleName }}</span>
                                    @else
                                        <span style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 600;">{{ $roleName }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td style="padding: 0.75rem 0.75rem; text-align: center;">
                            @if($dsn->user)
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                            @else
                                <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Belum</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1.5rem; text-align: center; color: #6b7280;">Belum ada data dosen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>