{{-- Baris 1: 4 Quick KPI Cards Mahasiswa --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Kelas Kuliah Aktif --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #4f46e5;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #4f46e5; display: block; line-height: 1.2;">{{ $statKelas }} Kelas</span>
            <span style="font-size: 0.75rem; color: #4f46e5; font-weight: 600;">Mata Kuliah Diambil</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                <span>Beban {{ $totalSksSemester }} SKS Semester Ini</span>
            </div>
        </div>
    </div>

    {{-- Tugas Belum Dikumpul (Urgensi) --}}
    <div class="stat-prodi-card" style="border-left: 4px solid {{ $statBelumDikumpul > 0 ? '#f59e0b' : '#10b981' }};">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $statBelumDikumpul > 0 ? '#fffbeb' : '#ecfdf5' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statBelumDikumpul > 0 ? '#d97706' : '#059669' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: {{ $statBelumDikumpul > 0 ? '#d97706' : '#059669' }}; display: block; line-height: 1.2;">
                {{ $statBelumDikumpul }} Tugas
            </span>
            <span style="font-size: 0.75rem; color: {{ $statBelumDikumpul > 0 ? '#d97706' : '#059669' }}; font-weight: 600;">Tugas Perlu Dikerjakan</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                {{ $statBelumDikumpul > 0 ? 'Periksa tenggat deadline' : 'Semua tugas beres!' }}
            </div>
        </div>
    </div>

    {{-- Tugas Selesai / Terkumpul --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #10b981;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #059669; display: block; line-height: 1.2;">
                {{ $statTugasSelesai }} Selesai
            </span>
            <span style="font-size: 0.75rem; color: #059669; font-weight: 600;">Tugas Terkumpul</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                Telah diunggah ke dosen
            </div>
        </div>
    </div>

    {{-- Tingkat Kehadiran Presensi --}}
    <div class="stat-prodi-card" style="border-left: 4px solid #0284c7;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <span style="font-weight: 700; font-size: 1.35rem; color: #0284c7; display: block; line-height: 1.2;">
                {{ $persenKehadiran }}%
            </span>
            <span style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">Presensi Perkuliahan</span>
            <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem;">
                {{ $totalPresensi > 0 ? "$totalHadir dari $totalPresensi sesi hadir" : 'Belum ada sesi presensi' }}
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: 2 Kolom Dinamis (Utama 1.3fr & Timeline 1fr) --}}
<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 1.25rem; align-items: start; margin-bottom: 1.5rem;">
    {{-- Kolom Kiri: Tugas Mendesak & Katalog Kelas --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        {{-- Tugas Mendekati Deadline --}}
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Tugas Mendekati Deadline</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Tenggat waktu dalam 7 hari ke depan</div>
                    </div>
                </div>
                @if($tugasMendekati->count())
                    <span style="font-size: 0.72rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.2rem 0.55rem; border-radius: 6px;">
                        {{ $tugasMendekati->count() }} Tugas Mendesak
                    </span>
                @endif
            </div>

            @if($tugasMendekati->count())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($tugasMendekati as $tugas)
                        <div style="border: 1px solid #fed7aa; background: #fffaf5; border-radius: 10px; padding: 0.85rem; transition: all 0.2s;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #0f172a; line-height: 1.35;">{{ $tugas->judul }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                                        {{ $tugas->pengampu->mataKuliah->kode ?? '' }} {{ $tugas->pengampu->mataKuliah->nama ?? 'Mata Kuliah' }} · {{ $tugas->pengampu->label_lengkap ?? "Kelas {$tugas->pengampu->kelas}" }}
                                    </div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; background: #ea580c; color: #fff; padding: 0.2rem 0.6rem; border-radius: 6px; flex-shrink: 0;">
                                    {{ $tugas->deadline->diffForHumans(['parts' => 1]) }}
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.75rem; border-top: 1px solid #ffedd5; padding-top: 0.65rem;">
                                <span style="font-size: 0.75rem; color: #9a3412; font-weight: 500;">
                                    Batas: {{ $tugas->deadline->format('d M Y, H:i') }} WIB
                                </span>
                                <a href="{{ route('mahasiswa.lms.show', $tugas->pengampu_id) }}" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.85rem; border-radius: 6px;">
                                    Kumpulkan Tugas &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.75rem; background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p style="color: #0f172a; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.2rem;">Tidak ada tugas yang mendesak!</p>
                    <p style="color: #64748b; font-size: 0.75rem; margin-bottom: 0;">Semua tugas dalam 7 hari ke depan telah selesai atau belum ada tenggat baru.</p>
                </div>
            @endif
        </div>

        {{-- Katalog Mata Kuliah Semester Ini --}}
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Mata Kuliah Semester Ini</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Kelas perkuliahan aktif yang Anda ikuti</div>
                    </div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 0.2rem 0.6rem; border-radius: 6px;">
                    {{ $kelasSaya->count() }} Kelas
                </span>
            </div>

            @if($kelasSaya->count())
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0.85rem;">
                    @foreach($kelasSaya as $kelas)
                        @php
                            $sks = ($kelas->mataKuliah->sks_teori ?? 0) + ($kelas->mataKuliah->sks_praktikum ?? 0);
                        @endphp
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; background: #fff; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s;" onmouseover="this.style.borderColor='#a5b4fc'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.04)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem;">
                                    <span style="font-size: 0.7rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                        {{ $kelas->mataKuliah->kode ?? 'MK' }}
                                    </span>
                                    <span style="font-size: 0.7rem; font-weight: 600; color: #64748b;">
                                        {{ $sks }} SKS
                                    </span>
                                </div>
                                <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a; line-height: 1.35; margin-bottom: 0.35rem;">
                                    {{ $kelas->mataKuliah->nama ?? 'Mata Kuliah' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #0284c7; font-weight: 600; margin-bottom: 0.4rem;">
                                    {{ $kelas->label_lengkap }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 0.35rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $kelas->dosen->user->name ?? 'Dosen Pengampu' }}</span>
                                </div>
                            </div>

                            <div style="margin-top: 0.85rem; pt-3; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                <div style="font-size: 0.7rem; color: #64748b; display: flex; align-items: center; gap: 0.5rem;">
                                    <span><strong>{{ $kelas->lms_materis_count }}</strong> Materi</span>
                                    <span>&bull;</span>
                                    <span><strong>{{ $kelas->lms_tugas_count }}</strong> Tugas</span>
                                </div>
                                <a href="{{ route('mahasiswa.lms.show', $kelas->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.72rem; padding: 0.3rem 0.65rem; border-radius: 6px;">
                                    Masuk Kelas &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0;">Anda belum terdaftar dalam kelas pengampu semester ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Materi Kuliah Terbaru & Feed Diskusi --}}
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        {{-- Materi Kuliah Terbaru --}}
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Materi Kuliah Baru</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Rilis 7 hari terakhir</div>
                    </div>
                </div>
                <span style="font-size: 0.7rem; font-weight: 600; background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 6px;">
                    {{ $materiBaru->count() }} Baru
                </span>
            </div>

            @if($materiBaru->count())
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($materiBaru as $materi)
                        <a href="{{ route('mahasiswa.lms.show', $materi->pengampu_id) }}" style="display: flex; align-items: center; gap: 0.65rem; text-decoration: none; padding: 0.6rem 0.75rem; border-radius: 8px; background: #f8fafc; border: 1px solid #f1f5f9; transition: all 0.15s;" onmouseover="this.style.background='#ecfdf5'; this.style.borderColor='#a7f3d0';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#f1f5f9';">
                            <div style="width: 34px; height: 34px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #059669;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.82rem; color: #0f172a; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $materi->judul }}
                                </div>
                                <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.1rem;">
                                    {{ $materi->pengampu->mataKuliah->kode ?? '' }} &middot; {{ $materi->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.25rem; background: #f8fafc; border-radius: 8px;">
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 0;">Belum ada materi baru dalam 7 hari terakhir.</p>
                </div>
            @endif
        </div>

        {{-- Forum Diskusi Terkini --}}
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0f9ff; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">Forum Diskusi Kelas</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Tanya-jawab &amp; obrolan perkuliahan</div>
                    </div>
                </div>
            </div>

            @if($forumTerbaru->count())
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    @foreach($forumTerbaru as $diskusi)
                        <a href="{{ route('mahasiswa.lms.show', $diskusi->pengampu_id) }}" style="display: flex; align-items: flex-start; gap: 0.65rem; text-decoration: none; padding: 0.65rem; border-radius: 8px; background: #f8fafc; border: 1px solid #f1f5f9; transition: all 0.15s;" onmouseover="this.style.background='#f0f9ff'; this.style.borderColor='#bae6fd';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#f1f5f9';">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ ($diskusi->user->role ?? '') === 'dosen' ? '#ecfdf5' : '#eef2ff' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: {{ ($diskusi->user->role ?? '') === 'dosen' ? '#059669' : '#4f46e5' }};">
                                    {{ strtoupper(substr($diskusi->user->name ?? '?', 0, 1)) }}
                                </span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.8rem; color: #0f172a; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $diskusi->pesan }}
                                </div>
                                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                    <span style="font-weight: 600; color: #334155;">{{ $diskusi->user->name ?? 'Pengguna' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $diskusi->pengampu->mataKuliah->kode ?? '' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $diskusi->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.25rem; background: #f8fafc; border-radius: 8px;">
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 0;">Belum ada diskusi baru di kelas Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>
