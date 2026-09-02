@extends('layouts.app')

@section('content')

<div class="no-print" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; gap: 0.5rem; flex-wrap: wrap;">
    <h1 class="page-header" style="margin: 0;">Kartu Hasil Studi (KHS) - {{ $mahasiswa->nama }}</h1>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        @if(auth()->user()->isMahasiswa())
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        @else
            <a href="{{ route('khs.cetak-pilih') }}" class="btn btn-secondary">Pilih Ulang</a>
        @endif
        <button onclick="window.print()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak
        </button>
        <a href="{{ route('khs.cetak-pdf', [
            $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik?->id,
        ]) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Unduh PDF
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />

<div class="print-area">

    {{-- ====== Kop Surat ====== --}}
    <div class="krs-kop">
        <div class="krs-logo">
            <svg width="48" height="48" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 4L26 10V18L16 24L6 18V10L16 4Z" fill="#eef2ff" stroke="#4f46e5" stroke-width="1.5"/>
                <path d="M16 24L26 18V22L16 28L6 22V18L16 24Z" fill="#e0e7ff" stroke="#4f46e5" stroke-width="1.5"/>
                <path d="M16 14L21 11V15L16 18L11 15V11L16 14Z" fill="#4f46e5" opacity="0.3"/>
            </svg>
        </div>
        <div class="krs-kop-text">
            <div class="krs-institusi">PIKOBE - Politeknik Sawunggaling Aji</div>
            <div class="krs-alamat">Jl. Khatib Tegal No. 01 Kutoarjo, Purworejo, Jawa Tengah</div>
            <div class="krs-judul">KARTU HASIL STUDI (KHS)</div>
            <div class="krs-semester">
                {{ $tahunAkademik ? 'Tahun Akademik '.$tahunAkademik->tahun.' / Semester '.ucfirst($tahunAkademik->semester) : 'Semua Tahun Akademik' }}
            </div>
        </div>
        <div class="krs-kop-nip">DOKUMEN RESMI AKADEMIK</div>
    </div>

    {{-- ====== Identitas Mahasiswa ====== --}}
    <div class="krs-section-title">I. Identitas Mahasiswa</div>
    <table class="krs-info">
        <tbody>
            <tr>
                <th class="krs-info-label">NIM</th>
                <td class="krs-info-value">{{ $mahasiswa->nim }}</td>
                <th class="krs-info-label">Angkatan</th>
                <td class="krs-info-value">{{ $mahasiswa->angkatan }}</td>
            </tr>
            <tr>
                <th class="krs-info-label">Nama Mahasiswa</th>
                <td class="krs-info-value">{{ $mahasiswa->nama }}</td>
                <th class="krs-info-label">Jenis Kelas</th>
                <td class="krs-info-value">
                    <span style="font-weight: 600; color: #4338ca;">{{ $mahasiswa->jenis_kelas ? ucfirst($mahasiswa->jenis_kelas) : 'Reguler' }}</span>
                </td>
            </tr>
            <tr>
                <th class="krs-info-label">Program Studi</th>
                <td class="krs-info-value" colspan="3">
                    {{ $mahasiswa->programStudi->nama_prodi ?? '-' }} ({{ $mahasiswa->programStudi->jenjang ?? 'D3' }})
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ====== Rincian Hasil Studi ====== --}}
    <div class="krs-section-title">II. Rincian Hasil Studi &amp; Penilaian Mata Kuliah</div>

    @if(empty($khsData['items']))
        <div class="krs-empty">
            Mahasiswa belum terdaftar pada kelas mata kuliah manapun
            {{ $tahunAkademik ? 'pada tahun akademik ini' : '' }}.
        </div>
    @else
        <table class="krs-table">
            <thead>
                <tr>
                    <th style="width: 32px;">No</th>
                    <th style="width: 75px;">Kode MK</th>
                    <th class="krs-mk-name">Nama Mata Kuliah</th>
                    <th style="width: 45px; text-align: center;">SKS</th>
                    <th style="width: 70px; text-align: center;">Nilai Angka</th>
                    <th style="width: 65px; text-align: center;">Huruf Mutu</th>
                    <th style="width: 65px; text-align: center;">Bobot Mutu</th>
                    <th style="width: 75px; text-align: center;">Nilai Mutu (K&times;M)</th>
                    <th style="width: 85px; text-align: center;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($khsData['items'] as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center font-mono">{{ $item['kode'] }}</td>
                        <td class="krs-mk-name">
                            <strong style="color: #0f172a;">{{ $item['nama'] }}</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">Dosen: {{ $item['dosen'] }}</div>
                        </td>
                        <td class="text-center font-semibold">{{ $item['sks'] }}</td>
                        <td class="text-center">{{ $item['nilai_angka'] }}</td>
                        <td class="text-center">
                            @if($item['nilai_huruf'] !== '-')
                                <span style="font-weight: 700; color: {{ in_array($item['nilai_huruf'], ['A', 'B+', 'B']) ? '#059669' : (in_array($item['nilai_huruf'], ['C+', 'C']) ? '#d97706' : '#dc2626') }};">
                                    {{ $item['nilai_huruf'] }}
                                </span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item['bobot_mutu'] }}</td>
                        <td class="text-center font-semibold" style="color: #1e40af;">{{ $item['nilai_mutu'] }}</td>
                        <td class="text-center" style="font-size: 0.75rem;">
                            @if($item['nilai_huruf'] !== '-')
                                @if($item['lulus'])
                                    <span style="background: #ecfdf5; color: #047857; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;">Lulus</span>
                                @else
                                    <span style="background: #fef2f2; color: #b91c1c; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;">Tidak Lulus</span>
                                @endif
                            @else
                                <span style="color: #94a3b8; font-style: italic;">Berjalan</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 700;">
                    <td colspan="3" class="text-right">Total SKS &amp; Nilai Mutu:</td>
                    <td class="text-center" style="color: #4f46e5; font-size: 0.95rem;">{{ $khsData['total_sks'] }}</td>
                    <td colspan="3" class="text-right">Jumlah (K &times; M):</td>
                    <td class="text-center" style="color: #4f46e5; font-size: 0.95rem;">{{ $khsData['total_poin'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        {{-- ====== Rekapitulasi Prestasi Akademik ====== --}}
        <div style="margin-top: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Indeks Prestasi Semester (IPS)</div>
                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-size: 2rem; font-weight: 800; color: #1e1b4b; line-height: 1;">{{ $khsData['ips'] }}</span>
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">/ 4.00</span>
                </div>
                <div style="font-size: 0.75rem; color: #475569; margin-top: 0.35rem;">
                    Rumus: <code style="font-family: monospace; background: #e2e8f0; padding: 0.1rem 0.3rem; border-radius: 3px;">IPS = &Sigma;(SKS &times; Bobot) &divide; &Sigma;SKS = {{ $khsData['total_poin'] }} &divide; {{ $khsData['total_sks'] }}</code>
                </div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #166534; text-transform: uppercase;">Predikat Capaian Semester</div>
                @php
                    $ipsNum = (float) $khsData['ips'];
                    $predikatSemester = match(true) {
                        $ipsNum >= 3.51 => 'Dengan Pujian (Cumlaude)',
                        $ipsNum >= 3.00 => 'Sangat Memuaskan',
                        $ipsNum >= 2.76 => 'Memuaskan',
                        $ipsNum >= 2.00 => 'Cukup',
                        default => 'Perlu Peningkatan',
                    };
                @endphp
                <div style="font-size: 1.15rem; font-weight: 800; color: #15803d; margin-top: 0.25rem;">
                    {{ $predikatSemester }}
                </div>
                <div style="font-size: 0.75rem; color: #166534; margin-top: 0.2rem;">
                    Maksimum beban SKS semester berikutnya: <strong>{{ $ipsNum >= 3.00 ? '24 SKS' : ($ipsNum >= 2.50 ? '21 SKS' : '18 SKS') }}</strong>
                </div>
            </div>
        </div>
    @endif

    {{-- ====== Tanda Tangan Tiga Pihak ====== --}}
    <div class="krs-signature" style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; text-align: center;">
        <div>
            <div style="font-size: 0.8rem; color: #64748b;">Mengetahui,</div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Dosen Pembimbing Akademik,</div>
            <div style="height: 60px;"></div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">( ________________________ )</div>
            <div style="font-size: 0.72rem; color: #64748b;">NIDN. ........................................</div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b;">Purworejo, {{ now()->format('d F Y') }}</div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">Ketua Program Studi,</div>
            <div style="height: 60px;"></div>
            <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">( ________________________ )</div>
            <div style="font-size: 0.72rem; color: #64748b;">NIDN. ........................................</div>
        </div>
    </div>

</div>

@push('styles')
<style>
.print-area {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 2rem 2.5rem;
}
/* Kop surat */
.krs-kop {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #1e293b;
}
.krs-logo { flex-shrink: 0; }
.krs-institusi { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.krs-alamat { font-size: 0.75rem; color: #64748b; }
.krs-judul { font-size: 1.35rem; font-weight: 800; color: #1e293b; letter-spacing: 0.03em; margin-top: 0.15rem; }
.krs-semester { font-size: 0.8rem; color: #4f46e5; font-weight: 600; }
.krs-kop-nip {
    margin-left: auto;
    align-self: flex-start;
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    letter-spacing: 0.05em;
    background: #f1f5f9;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    white-space: nowrap;
}
.krs-kop-text { flex: 1; }

/* Judul section */
.krs-section-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #1e293b;
    margin: 1.25rem 0 0.5rem;
}

/* Identitas mahasiswa */
.krs-info {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}
.krs-info th, .krs-info td {
    padding: 0.35rem 0.6rem;
    vertical-align: top;
}
.krs-info-label {
    width: 160px;
    background: #f8fafc;
    font-weight: 600;
    color: #475569;
    text-align: left;
}
.krs-info-value { font-weight: 500; color: #0f172a; }

/* Tabel struktur mata kuliah */
.krs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.krs-table th, .krs-table td {
    border: 1px solid #cbd5e1;
    padding: 0.45rem 0.6rem;
    text-align: left;
    vertical-align: middle;
}
.krs-table thead th {
    background: #eef2ff;
    color: #3730a3;
    font-weight: 700;
    text-align: center;
}
.krs-table th.krs-mk-name,
.krs-table td.krs-mk-name {
    text-align: left;
    vertical-align: middle;
}
.krs-table tbody tr:nth-child(even) { background: #fafafe; }
.krs-table tfoot td {
    border-top: 2px solid #1e293b;
    background: #f8fafc;
    font-weight: 700;
}
.text-center { text-align: center !important; }
.text-right { text-align: right !important; }
.font-mono { font-family: monospace; }
.font-semibold { font-weight: 600; }

.krs-empty {
    text-align: center;
    color: #6b7280;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 2.5rem;
    font-size: 0.9rem;
}

@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-area { border: none; padding: 0; }
}
</style>
@endpush

@endsection
