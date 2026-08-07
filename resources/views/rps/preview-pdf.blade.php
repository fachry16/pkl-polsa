@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Preview RPS
</h1>

<p class="mb-5" style="color: #64748b;">
    {{ $rps->mataKuliah->kode }} — {{ $rps->mataKuliah->nama }}
</p>

<div class="mb-4">
    <a href="{{ route('rps.ekstrak-pdf', ['rps' => $rps, 'download' => 1]) }}"
       class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:6px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download PDF
    </a>
</div>

<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; max-width: 900px;">

    <div style="text-align: center; margin-bottom: 24px; border-bottom: 2px solid #4f46e5; padding-bottom: 12px;">
        <h2 style="font-size: 16px; margin: 0 0 4px;">RENCANA PEMBELAJARAN SEMESTER (RPS)</h2>
        <p style="margin: 0; color: #64748b; font-size: 13px;">PIKOBE Polsa — Politeknik Sawunggalih Aji</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Kode RPS</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->kode_rps ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Mata Kuliah</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</td>
        </tr>
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Semester</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->semester }}</td>
        </tr>
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Dosen Pengampu</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->dosen_pengampu }}</td>
        </tr>
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Deskripsi</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->deskripsi_mata_kuliah ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 180px; font-weight: 600; padding: 6px 8px; background: #f8fafc; border: 1px solid #e2e8f0;">Total SKS</td>
            <td style="padding: 6px 8px; border: 1px solid #e2e8f0;">{{ $rps->mataKuliah->total_sks }}</td>
        </tr>
    </table>

    <h3 style="font-size: 14px; margin: 20px 0 10px;">Pertemuan</h3>
    @if($rps->pertemuans->count())
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <thead>
            <tr>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Minggu</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Sub-CPMK</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Materi</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Metode</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Pengalaman Belajar</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Indikator</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rps->pertemuans as $pertemuan)
            <tr>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $pertemuan->minggu }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px;">{{ $pertemuan->sub_cpmk }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px;">{{ $pertemuan->materi }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px;">{{ $pertemuan->metode }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px;">{{ $pertemuan->pengalaman_belajar }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px;">{{ $pertemuan->indikator }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $pertemuan->bobot }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #94a3b8; font-style: italic;">Belum ada data pertemuan.</p>
    @endif

    <h3 style="font-size: 14px; margin: 20px 0 10px;">Penilaian</h3>
    @if($rps->penilaians->count())
    @php $penilaian = $rps->penilaians->first(); @endphp
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <thead>
            <tr>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Tugas</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Quiz</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">UTS</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">UAS</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Praktikum</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px 8px; background: #eef2ff; font-size: 11px; text-align: left;">Project</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->tugas ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->quiz ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->uts ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->uas ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->praktikum ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 11px; text-align: center;">{{ $penilaian->project ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p style="color: #94a3b8; font-style: italic;">Belum ada data penilaian.</p>
    @endif

    @if($rps->disetujuiOleh)
    <div style="margin-top: 20px; padding: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; border-radius: 8px; font-size: 12px;">
        <strong>Disetujui oleh:</strong>
        {{ $rps->disetujuiOleh->name }}
        @if($rps->tanggal_disetujui)
            pada {{ $rps->tanggal_disetujui->format('d/m/Y H:i') }}
        @endif
    </div>
    @endif

</div>

<div class="mt-4">
    <a href="{{ route('mata-kuliah.rps.index', $rps->mataKuliah) }}"
       class="btn btn-secondary">Kembali</a>
</div>

@endsection
