<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RPS - {{ $rps->mataKuliah->nama }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        h2 { font-size: 13px; text-align: center; margin-top: 0; margin-bottom: 20px; font-weight: normal; color: #475569; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #eef2ff; font-weight: 600; font-size: 10px; }
        td { font-size: 10px; }
        .info-table td:first-child { width: 180px; font-weight: 600; background: #f8fafc; }
        .page-break { page-break-before: always; }
        .approval-box { margin-top: 30px; padding: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; border-radius: 4px; font-size: 10px; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RENCANA PEMBELAJARAN SEMESTER (RPS)</h1>
        <h2>PIKOBE Polsa — Politeknik Sawunggalih Aji</h2>
    </div>

    <table class="info-table">
        <tr><td>Kode RPS</td><td>{{ $rps->kode_rps ?? '-' }}</td></tr>
        <tr><td>Mata Kuliah</td><td>{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</td></tr>
        <tr><td>Semester</td><td>{{ $rps->semester }}</td></tr>
        <tr><td>Dosen Pengampu</td><td>{{ $rps->dosen_pengampu }}</td></tr>
        <tr><td>Deskripsi</td><td>{{ $rps->deskripsi_mata_kuliah ?? '-' }}</td></tr>
        <tr><td>Total SKS</td><td>{{ $rps->mataKuliah->total_sks }}</td></tr>
    </table>

    <h3 style="font-size: 12px; margin-top: 20px;">Pertemuan</h3>
    @if($rps->pertemuans->count())
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Minggu</th>
                <th>Sub-CPMK</th>
                <th>Materi</th>
                <th>Metode</th>
                <th>Pengalaman Belajar</th>
                <th>Indikator</th>
                <th style="width: 40px;">Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rps->pertemuans as $pertemuan)
            <tr>
                <td class="text-center">{{ $pertemuan->minggu }}</td>
                <td>{{ $pertemuan->sub_cpmk }}</td>
                <td>{{ $pertemuan->materi }}</td>
                <td>{{ $pertemuan->metode }}</td>
                <td>{{ $pertemuan->pengalaman_belajar }}</td>
                <td>{{ $pertemuan->indikator }}</td>
                <td class="text-center">{{ $pertemuan->bobot }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #94a3b8; font-style: italic;">Belum ada data pertemuan.</p>
    @endif

    <h3 style="font-size: 12px; margin-top: 20px;">Penilaian</h3>
    @if($rps->penilaians->count())
    @php $penilaian = $rps->penilaians->first(); @endphp
    <table>
        <thead>
            <tr>
                <th>Tugas</th>
                <th>Quiz</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Praktikum</th>
                <th>Project</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $penilaian->tugas ?? '-' }}</td>
                <td class="text-center">{{ $penilaian->quiz ?? '-' }}</td>
                <td class="text-center">{{ $penilaian->uts ?? '-' }}</td>
                <td class="text-center">{{ $penilaian->uas ?? '-' }}</td>
                <td class="text-center">{{ $penilaian->praktikum ?? '-' }}</td>
                <td class="text-center">{{ $penilaian->project ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p style="color: #94a3b8; font-style: italic;">Belum ada data penilaian.</p>
    @endif

    @if($rps->disetujuiOleh)
    <div class="approval-box">
        <strong>Disetujui oleh:</strong>
        {{ $rps->disetujuiOleh->name }}
        @if($rps->tanggal_disetujui)
            pada {{ $rps->tanggal_disetujui->format('d/m/Y H:i') }}
        @endif
    </div>
    @endif

    <div class="footer">
        Dokumen ini diekstrak dari PIKOBE Polsa pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>