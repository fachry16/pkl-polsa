@php
    $bulanMap = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $tanggalPenyusunan = $rps->created_at
        ? $bulanMap[$rps->created_at->month - 1].' '.$rps->created_at->year
        : (string) ($rps->mataKuliah->kurikulum->tahun_berlaku ?? '');
@endphp

    {{-- COVER --}}
    <div class="rps-cover">
        <div class="rps-cover-institution">
            <img src="{{ asset('images/logo-kampus.png') }}" alt="Logo Kampus" class="rps-cover-logo">
            <div class="rps-cover-institution-name">POLITEKNIK SAWUNGGALIH AJI</div>
        </div>
        <h1 class="rps-cover-title">RENCANA PEMBELAJARAN SEMESTER (RPS)</h1>
        <div class="rps-cover-subject">
            <div class="rps-cover-subject-label">Mata Kuliah</div>
            <div class="rps-cover-subject-name">{{ $rps->mataKuliah->nama }}</div>
            <div class="rps-cover-subject-code">Kode: {{ $rps->mataKuliah->kode }}</div>
        </div>
        <div class="rps-cover-by">
            <div class="rps-cover-oleh">oleh</div>
            <div class="rps-cover-penyusun">PENYUSUN</div>
            <div class="rps-cover-penyusun-name">{{ $rps->dosen_pengembang_rps ?? $rps->dosen_pengampu }}</div>
        </div>
        <div class="rps-cover-prodi">
            Program Studi {{ $rps->mataKuliah->kurikulum->programStudi->jenjang ?? '' }} {{ $rps->mataKuliah->kurikulum->programStudi->nama_prodi ?? '' }}
        </div>
        <div class="rps-cover-info-grid">
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Program Studi</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->programStudi->nama_prodi ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Jenjang</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->programStudi->jenjang ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Kurikulum</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->nama_kurikulum ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Dosen Pengampu</span>
                <span class="rps-cover-info-value">{{ $rps->dosen_pengampu }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Tahun Penyusunan</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->tahun_berlaku ?? '-' }}</span>
            </div>
        </div>
        <div class="rps-cover-location">Purworejo, {{ $tanggalPenyusunan }}</div>
    </div>
