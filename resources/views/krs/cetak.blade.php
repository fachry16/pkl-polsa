@extends('layouts.app')

@section('content')

<div class="no-print" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; gap: 0.5rem; flex-wrap: wrap;">
    <h1 class="page-header" style="margin: 0;">Cetak KRS - {{ $mahasiswa->nama }}</h1>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('krs.cetak-pilih') }}" class="btn btn-secondary">Pilih Ulang</a>
        <a href="{{ route('krs.cetak-pdf', [
            $mahasiswa->id,
            'tahun_akademik_id' => $tahunAkademik?->id,
        ]) }}" class="btn btn-primary">Unduh PDF</a>
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
            <div class="krs-institusi">POLITEKNIK SAWUNGGALIH AJI (POLSA)</div>
            <div class="krs-alamat">Jl. W.R. Supratman No. 5 Kutoarjo, Purworejo, Jawa Tengah</div>
            <div class="krs-judul">KARTU RENCANA STUDI (KRS)</div>
            <div class="krs-semester">
                {{ $tahunAkademik ? 'Tahun Akademik '.$tahunAkademik->tahun.' / Semester '.ucfirst($tahunAkademik->semester) : 'Semua Tahun Akademik' }}
            </div>
        </div>
        <div class="krs-kop-nip">NIP. _________________</div>
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
                <td class="krs-info-value" colspan="3">{{ $mahasiswa->nama }}</td>
            </tr>
            <tr>
                <th class="krs-info-label">Program Studi</th>
                <td class="krs-info-value" colspan="3">
                    {{ $mahasiswa->programStudi->nama_prodi ?? '-' }} ({{ $mahasiswa->programStudi->kode_prodi ?? '-' }})
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ====== Struktur Mata Kuliah ====== --}}
    <div class="krs-section-title">II. Struktur Mata Kuliah</div>

    @if($kelas->isEmpty())
        <div class="krs-empty">
            Mahasiswa belum terdaftar pada kelas manapun
            {{ $tahunAkademik ? 'pada tahun akademik ini' : '' }}.
        </div>
    @else
        <table class="krs-table">
            <thead>
                <tr>
                    <th style="width: 36px;">No</th>
                    <th style="width: 80px;">Kode MK</th>
                    <th class="krs-mk-name">Nama Mata Kuliah</th>
                    <th style="width: 70px;">SKS Teori</th>
                    <th style="width: 70px;">SKS Praktik</th>
                    <th style="width: 60px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalTeori = 0; $totalPraktik = 0; @endphp
                @foreach($kelas as $pengampu)
                    @php
                        $totalTeori += $pengampu->mataKuliah?->sks_teori ?? 0;
                        $totalPraktik += $pengampu->mataKuliah?->sks_praktikum ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $pengampu->kode_mata_kuliah }}</td>
                        <td class="krs-mk-name">{{ $pengampu->nama_mata_kuliah }}</td>
                        <td class="text-center">{{ $pengampu->mataKuliah?->sks_teori ?? 0 }}</td>
                        <td class="text-center">{{ $pengampu->mataKuliah?->sks_praktikum ?? 0 }}</td>
                        <td class="text-center">{{ $pengampu->total_sks }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right krs-total-label">Total SKS</td>
                    <td class="text-center krs-total-value">{{ $totalTeori }}</td>
                    <td class="text-center krs-total-value">{{ $totalPraktik }}</td>
                    <td class="text-center krs-total-value">{{ $totalTeori + $totalPraktik }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ====== Tanda Tangan ====== --}}
    <div class="krs-signature">
        <div class="krs-ttd-block">
            <div>Purworejo, {{ now()->format('d F Y') }}</div>
            <div>Kaprodi,</div>
            <div class="krs-ttd-space"></div>
            <div class="krs-ttd-name">( ___________________ )</div>
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
.krs-logo { flex-shrink: 0; }.krs-institusi { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.krs-alamat { font-size: 0.75rem; color: #64748b; }
.krs-judul { font-size: 1.4rem; font-weight: 700; color: #1e293b; letter-spacing: 0.02em; margin-top: 0.15rem; }
.krs-semester { font-size: 0.8rem; color: #4f46e5; font-weight: 600; }
.krs-kop-nip {
    margin-left: auto;
    align-self: flex-start;
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
}
.krs-kop-text { flex: 1; }

/* Judul section */
.krs-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    margin: 1.25rem 0 0.5rem;
}

/* Identitas mahasiswa */
.krs-info {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.krs-info th, .krs-info td {
    padding: 0.4rem 0.6rem;
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
    font-size: 0.85rem;
}
.krs-table th, .krs-table td {
    border: 1px solid #cbd5e1;
    padding: 0.5rem 0.6rem;
    text-align: left;
    vertical-align: top;
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
.krs-total-label { text-align: right; }
.krs-total-value { color: #4f46e5; }
.krs-total-note { font-weight: 500; color: #64748b; }
.text-center { text-align: center !important; }
.text-right { text-align: right !important; }

.krs-empty {
    text-align: center;
    color: #6b7280;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 2.5rem;
    font-size: 0.9rem;
}

/* Tanda tangan */
.krs-signature {
    margin-top: 2.5rem;
    display: flex;
    justify-content: flex-end;
}
.krs-ttd-block { text-align: center; font-size: 0.85rem; }
.krs-ttd-space { height: 70px; }
.krs-ttd-name { font-weight: 600; }
.krs-ttd-nip { font-size: 0.75rem; color: #64748b; }

@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-area { border: none; padding: 0; }
}
</style>
@endpush

@endsection
