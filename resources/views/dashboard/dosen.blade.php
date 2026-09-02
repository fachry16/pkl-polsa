{{-- Header Sambutan Personal Dosen --}}
<div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.15);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.35rem; font-weight: 700; color: #a5b4fc; border: 1px solid rgba(255, 255, 255, 0.2);">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <div style="font-size: 0.75rem; color: #a5b4fc; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Selamat Datang, Dosen Pengajar POLSA</div>
            <div style="font-size: 1.25rem; font-weight: 700; line-height: 1.2; margin-top: 0.15rem;">{{ auth()->user()->name }}</div>
            <div style="font-size: 0.8rem; color: #cbd5e1; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span>NIDN: <strong>{{ auth()->user()->dosen->nidn ?? '-' }}</strong></span>
                <span>&bull;</span>
                <span>Prodi: <strong>{{ auth()->user()->dosen->programStudi->nama_prodi ?? 'Politeknik Sawunggalih Aji' }}</strong></span>
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 0.6rem;">
        <span style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; color: #e0e7ff;">
            {{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : 'Semester Aktif' }}
        </span>
        @if(auth()->user()->isKaprodi())
            <span style="background: #10b981; padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: #fff;">
                Kaprodi
            </span>
        @endif
    </div>
</div>

{{-- Baris 1: 4 KPI Cards Mengajar Dosen --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Kelas Diampu --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ $pengampus->count() }} Kelas</span>
            <span style="font-size: 0.75rem; color: #4f46e5; font-weight: 600;">Kelas Paket Diampu</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>Kelas A: {{ $dosenKelasA }}</span> &bull; 
                <span>Kelas B: {{ $dosenKelasB }}</span>
            </div>
        </div>
    </div>

    {{-- Total Mahasiswa Diajar --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #0ea5e9;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #0284c7; display: block; line-height: 1.2;">{{ $dosenTotalMahasiswa }}</span>
            <span style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">Total Mahasiswa Diajar</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">Akumulasi Mahasiswa Rombel</div>
        </div>
    </div>

    {{-- Tugas Menunggu Penilaian --}}
    <div class="stat-prodi-card" style="border-left: 4px solid {{ $dosenSubmissionsBelumDinilai > 0 ? '#ef4444' : '#10b981' }};">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $dosenSubmissionsBelumDinilai > 0 ? '#fee2e2' : '#ecfdf5' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $dosenSubmissionsBelumDinilai > 0 ? '#dc2626' : '#059669' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: {{ $dosenSubmissionsBelumDinilai > 0 ? '#dc2626' : '#059669' }}; display: block; line-height: 1.2;">
                {{ $dosenSubmissionsBelumDinilai }}
            </span>
            <span style="font-size: 0.75rem; color: {{ $dosenSubmissionsBelumDinilai > 0 ? '#dc2626' : '#059669' }}; font-weight: 600;">Tugas Perlu Dinilai</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                {{ $dosenSubmissionsBelumDinilai > 0 ? 'Perlu evaluasi berkala' : 'Semua tugas telah dinilai' }}
            </div>
        </div>
    </div>

    {{-- Kesiapan RPS Saya --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">{{ $dosenRpsStats['persen'] }}%</span>
            <span style="font-size: 0.75rem; color: #059669; font-weight: 600;">Kesiapan RPS Saya</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>{{ $dosenRpsStats['disetujui'] }} / {{ $dosenRpsStats['total_mk'] }} MK Disetujui</span>
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: To-Do & Peringatan Dosen (Antrean Penilaian & Validasi RPS) --}}
<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
    {{-- Antrean Penilaian Tugas --}}
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fee2e2; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Antrean Penilaian Tugas Mahasiswa</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Submission yang butuh diperiksa dan diberi nilai</div>
                    </div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.15rem 0.5rem; border-radius: 6px;">
                    {{ $dosenSubmissionsBelumDinilai }} Menunggu
                </span>
            </div>

            @if($dosenTugasNeedGrading->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    @foreach($dosenTugasNeedGrading as $tugas)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px;">
                            <div style="min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $tugas->judul }}</div>
                                <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">
                                    {{ $tugas->pengampu->mataKuliah->nama ?? 'Mata Kuliah' }} (Kelas {{ $tugas->pengampu->kelas ?? '-' }}) &bull; Deadline: {{ $tugas->deadline->format('d M Y') }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                                <span style="font-size: 0.72rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                    {{ $tugas->belum_dinilai_count }} Blm Dinilai
                                </span>
                                <a href="{{ route('lms.tugas.detail', [$tugas->pengampu_id, $tugas->id]) }}" class="btn btn-primary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                    Nilai
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.75rem 1rem; color: #059669;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div style="font-weight: 600; font-size: 0.85rem;">Semua Tugas Selesai Dinilai!</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Tidak ada submission tugas mahasiswa yang tertunda.</div>
                </div>
            @endif
        </div>
        <div style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem; font-size: 0.72rem; color: #64748b; text-align: right;">
            Evaluasi berkala membantu mahasiswa memantau perkembangan nilai.
        </div>
    </div>

    {{-- Kesiapan RPS & Info Akademik Dosen --}}
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Kesiapan RPS Mata Kuliah</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Status validasi RPS yang Anda ampu</div>
                    </div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 6px;">
                    {{ $dosenRpsStats['disetujui'] }} / {{ $dosenRpsStats['total_mk'] }} Disetujui
                </span>
            </div>

            @if($dosenMkBelumRps->count() > 0)
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.6rem;">
                    Mata kuliah berikut memerlukan pembaruan atau persetujuan RPS dari Kaprodi:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($dosenMkBelumRps as $mk)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px;">
                            <div style="min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.82rem; color: #92400e;">{{ $mk->kode }} - {{ $mk->nama }}</div>
                                <div style="font-size: 0.7rem; color: #b45309;">
                                    Status: <strong>{{ $mk->rps ? $mk->rps->status : 'Belum Ada RPS' }}</strong>
                                </div>
                            </div>
                            @if(auth()->user()->isKaprodi())
                                <a href="{{ route('rps.pengajuan') }}" class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                    Review
                                </a>
                            @else
                                <a href="{{ route('dosen.self.riwayat') }}" class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                    Lihat
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.75rem 1rem; color: #059669;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div style="font-weight: 600; font-size: 0.85rem;">RPS Lengkap &amp; Disetujui!</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Semua mata kuliah yang diampu siap diajarkan sesuai standar OBE.</div>
                </div>
            @endif
        </div>
        <div style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
            <a href="{{ route('dosen.self.riwayat') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Riwayat Mengajar &amp; RPS &rarr;</a>
            <span style="color: #64748b;">OBE System POLSA</span>
        </div>
    </div>
</div>

{{-- Baris 3: Ruang Kelas LMS yang Diampu Dosen --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Ruang Kelas LMS yang Diampu</div>
                <div style="font-size: 0.75rem; color: #64748b;">Pantau progres materi, tugas, dan pertemuan rombel per kelas</div>
            </div>
        </div>
        <span style="font-size: 0.75rem; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.6rem; border-radius: 6px;">
            {{ $pengampus->count() }} Kelas Aktif
        </span>
    </div>

    @if($pengampus->count())
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
            @foreach($pengampus as $pengampu)
                @php
                    $isKelasB = preg_match('/B|karyawan|sore|malam/i', $pengampu->kelas);
                    $persenPertemuan = min(100, round(($pengampu->sesi_absensi_count / 16) * 100));
                @endphp
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.1rem; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s;" onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 12px rgba(79,70,229,0.06)';" onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                    <div>
                        {{-- Header Kartu Kelas --}}
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="min-width: 0;">
                                <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a; line-height: 1.3;">
                                    {{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
                                    {{ $pengampu->mataKuliah->kode ?? '-' }} &bull; {{ $pengampu->label_semester }}
                                </div>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px; flex-shrink: 0; background: {{ $isKelasB ? '#fef3c7' : '#e0f2fe' }}; color: {{ $isKelasB ? '#b45309' : '#0369a1' }};">
                                Kelas {{ $pengampu->kelas }} ({{ $isKelasB ? 'Karyawan' : 'Reguler' }})
                            </span>
                        </div>

                        {{-- Progres 16 Pertemuan Kelas Ini --}}
                        <div style="margin-bottom: 0.85rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.72rem; margin-bottom: 0.3rem;">
                                <span style="color: #64748b; font-weight: 500;">Capaian Pertemuan</span>
                                <span style="font-weight: 700; color: #1e293b;">{{ $pengampu->sesi_absensi_count }} / 16 Sesi</span>
                            </div>
                            <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $persenPertemuan }}%; height: 100%; background: #4f46e5; border-radius: 999px;"></div>
                            </div>
                        </div>

                        {{-- Statistik Mahasiswa & Materi --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.4rem; padding: 0.5rem 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; margin-bottom: 0.75rem; text-align: center;">
                            <div>
                                <div style="font-size: 0.68rem; color: #64748b;">Mahasiswa</div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">{{ $pengampu->mahasiswas_count }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.68rem; color: #64748b;">Materi</div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #4f46e5;">{{ $pengampu->lms_materis_count }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.68rem; color: #64748b;">Tugas</div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #059669;">{{ $pengampu->lms_tugas_count }}</div>
                            </div>
                        </div>

                        {{-- Alert Submission Belum Dinilai --}}
                        @if($pengampu->submissions_belum_dinilai > 0)
                            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; color: #dc2626; background: #fee2e2; padding: 0.35rem 0.6rem; border-radius: 6px; margin-bottom: 0.75rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>{{ $pengampu->submissions_belum_dinilai }} submission belum dinilai</span>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Aksi --}}
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                        <a href="{{ route('lms.show', $pengampu->id) }}" class="btn btn-primary btn-sm" style="flex: 1; text-align: center; font-size: 0.75rem;">
                            Buka Kelas LMS
                        </a>
                        <a href="{{ route('lms.absensi.index', $pengampu->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;" title="Presensi Perkuliahan">
                            Presensi
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 2.5rem 1rem;">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
            </svg>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 0;">Belum ada kelas yang diampu pada semester ini.</p>
            <div style="font-size: 0.75rem; color: #cbd5e1; margin-top: 0.25rem;">Admin/Akademik akan mem-plotting pengampu pada KRS paket.</div>
        </div>
    @endif
</div>

{{-- Baris 4: Forum Diskusi Terbaru di Kelas Saya --}}
@if($dosenForumTerbaru->count() > 0)
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Diskusi Mahasiswa Terbaru di Kelas Anda</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Interaksi dan pertanyaan mahasiswa yang perlu ditanggapi</div>
                </div>
            </div>
            <span style="font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 6px;">
                {{ $dosenForumTerbaru->count() }} Diskusi Aktif
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
            @foreach($dosenForumTerbaru as $diskusi)
                <a href="{{ route('lms.show', $diskusi->pengampu_id) }}" style="display: flex; align-items: flex-start; gap: 0.75rem; text-decoration: none; padding: 0.75rem; border-radius: 8px; background: #f8fafc; border: 1px solid #f1f5f9; transition: all 0.15s;" onmouseover="this.style.background='#eef2ff';this.style.borderColor='#c7d2fe';" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#f1f5f9';">
                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.8rem; font-weight: 700; color: #4f46e5;">
                        {{ strtoupper(substr($diskusi->user->name ?? 'M', 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <span style="font-weight: 600; font-size: 0.82rem; color: #0f172a;">{{ $diskusi->user->name ?? 'Mahasiswa' }}</span>
                            <span style="font-size: 0.7rem; color: #94a3b8;">{{ $diskusi->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: 0.78rem; color: #475569; margin-top: 0.2rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $diskusi->pesan }}
                        </div>
                        <div style="font-size: 0.7rem; color: #4f46e5; margin-top: 0.25rem; font-weight: 500;">
                            Kelas: {{ $diskusi->pengampu->mataKuliah->nama ?? '-' }} ({{ $diskusi->pengampu->kelas ?? '-' }}) &rarr;
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif