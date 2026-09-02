@php
    $user = auth()->user();
    $taAktif = $globalTahunAkademik ?? null;
@endphp

@auth
<header class="global-sticky-header no-print">
    @if($user->isMahasiswa())
        @php
            $mhs = $user->mahasiswa;
            $isKaryawan = $mhs?->isKaryawan() ?? false;
            $totalSks = $mhs ? $mhs->pengampus()->when($taAktif, fn($q) => $q->where('tahun_akademik_id', $taAktif->id))->with('mataKuliah')->get()->sum(fn($p) => (int)$p->total_sks) : 0;
        @endphp
        <div class="global-header-inner theme-mahasiswa">
            <div class="header-left">
                <div class="header-avatar">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-role-tag">Portal Perkuliahan &amp; LMS OBE POLSA</div>
                    <div class="header-name">{{ $user->name }}</div>
                    <div class="header-chips">
                        <span>NIM: <strong>{{ $mhs->nim ?? '-' }}</strong></span>
                        <span class="dot">&bull;</span>
                        <span>Prodi: <strong>{{ $mhs->programStudi->nama_prodi ?? 'Politeknik Sawunggalih Aji' }}</strong></span>
                        @if($mhs?->angkatan)
                            <span class="dot">&bull;</span>
                            <span>Angkatan: <strong>{{ $mhs->angkatan }}</strong></span>
                        @endif
                        <span class="dot">&bull;</span>
                        @if($isKaryawan)
                            <span class="badge-karyawan">Kelas Karyawan (Kelas B)</span>
                        @else
                            <span class="badge-reguler">Kelas Reguler (Kelas A)</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('khs.self') }}" class="btn-header-action btn-header-khs">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    KHS Saya
                </a>
                <span class="badge-semester">
                    {{ $taAktif ? $taAktif->tahun.' '.ucfirst($taAktif->semester) : 'Semester Aktif' }}
                </span>
                <span class="badge-semester" style="background: rgba(16, 185, 129, 0.3); border-color: #34d399; font-weight: 700;">
                    {{ $totalSks }} SKS Ditempuh
                </span>
            </div>
        </div>

    @elseif($user->isAdmin())
        <div class="global-header-inner theme-admin">
            <div class="header-left">
                <div class="header-avatar avatar-admin">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-role-tag">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Pusat Kendali Akademik POLSA
                    </div>
                    <div class="header-name">{{ $user->name }}</div>
                    <div class="header-chips">
                        <span class="badge-role-admin">Administrator Sistem</span>
                        <span class="dot">&bull;</span>
                        <span>Status: <strong style="color: #34d399;">Sistem Normal</strong></span>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('krs.index') }}" class="btn-header-action btn-header-subtle">
                    KRS
                </a>
                <a href="{{ route('khs.index') }}" class="btn-header-action btn-header-subtle">
                    Validasi KHS
                </a>
                <span class="badge-semester">
                    {{ $taAktif ? $taAktif->tahun.' '.ucfirst($taAktif->semester) : 'TA Aktif' }}
                </span>
            </div>
        </div>

    @elseif($user->isKaprodi())
        @php
            $dosen = $user->dosen;
            $prodi = $dosen?->programStudi;
        @endphp
        <div class="global-header-inner theme-kaprodi">
            <div class="header-left">
                <div class="header-avatar avatar-kaprodi">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-role-tag">Pusat Kendali Program Studi</div>
                    <div class="header-name">{{ $user->name }}</div>
                    <div class="header-chips">
                        <span class="badge-role-kaprodi">Kaprodi {{ $prodi->nama_prodi ?? 'POLSA' }}</span>
                        @if($prodi?->jenjang)
                            <span class="dot">&bull;</span>
                            <span>Jenjang: <strong>{{ $prodi->jenjang }}</strong></span>
                        @endif
                        @if($prodi?->akreditasi)
                            <span class="dot">&bull;</span>
                            <span>Akreditasi: <strong style="color: #6ee7b7;">{{ $prodi->akreditasi }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('khs.index') }}" class="btn-header-action btn-header-khs">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Validasi KHS
                </a>
                <a href="{{ route('rps.pengajuan') }}" class="btn-header-action btn-header-subtle">
                    Review RPS
                </a>
                <span class="badge-semester">
                    {{ $taAktif ? $taAktif->tahun.' '.ucfirst($taAktif->semester) : 'Semester Aktif' }}
                </span>
            </div>
        </div>

    @elseif($user->isDirektur())
        <div class="global-header-inner theme-direktur">
            <div class="header-left">
                <div class="header-avatar avatar-direktur">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-role-tag">Dashboard Eksekutif &amp; Tata Kelola</div>
                    <div class="header-name">{{ $user->name }}</div>
                    <div class="header-chips">
                        <span class="badge-role-direktur">Direktur Politeknik Sawunggalih Aji</span>
                        <span class="dot">&bull;</span>
                        <span>Tata Kelola OBE Terpadu</span>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('dashboard-direktur') }}" class="btn-header-action btn-header-subtle">
                    Dashboard Eksekutif
                </a>
                <span class="badge-semester">
                    {{ $taAktif ? $taAktif->tahun.' '.ucfirst($taAktif->semester) : 'TA Aktif' }}
                </span>
            </div>
        </div>

    @else
        {{-- Dosen Biasa --}}
        @php
            $dosen = $user->dosen;
            $prodi = $dosen?->programStudi;
        @endphp
        <div class="global-header-inner theme-dosen">
            <div class="header-left">
                <div class="header-avatar avatar-dosen">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-role-tag">Selamat Datang, Dosen Pengajar POLSA</div>
                    <div class="header-name">{{ $user->name }}</div>
                    <div class="header-chips">
                        <span>NIDN: <strong>{{ $dosen->nidn ?? '-' }}</strong></span>
                        <span class="dot">&bull;</span>
                        <span>Prodi: <strong>{{ $prodi->nama_prodi ?? 'Politeknik Sawunggalih Aji' }}</strong></span>
                        <span class="dot">&bull;</span>
                        <span class="badge-role-dosen">Dosen Pengajar POLSA</span>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="{{ route('lms.index') }}" class="btn-header-action btn-header-khs">
                    Kelas LMS
                </a>
                <a href="{{ route('dosen.self.riwayat') }}" class="btn-header-action btn-header-subtle">
                    Riwayat RPS
                </a>
                <span class="badge-semester">
                    {{ $taAktif ? $taAktif->tahun.' '.ucfirst($taAktif->semester) : 'Semester Aktif' }}
                </span>
            </div>
        </div>
    @endif
</header>
@endauth
