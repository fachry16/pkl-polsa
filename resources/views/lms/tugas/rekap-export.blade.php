<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Nilai {{ $info['kodeKelas'] ?: ($pengampu->mataKuliah?->kode ?? '') }}</title>
<style>
    @page { size: A4 landscape; margin: 10mm 8mm; }
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; color: #1e293b; font-size: 9px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    .kop { text-align: center; border-bottom: 2pt solid #1e293b; padding-bottom: 8px; margin-bottom: 10px; }
    .kop-title { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 2px; }
    .kop-semester { font-size: 10.5px; font-weight: 600; color: #334155; }
    .kop-institusi { font-size: 9px; color: #64748b; }

    .info-table { width: 100%; margin-bottom: 10px; }
    .info-table td { border: none; padding: 1px 0; font-size: 9px; }
    .info-label { font-weight: 700; color: #0f172a; padding-right: 8px; }
    .info-value { color: #1e293b; }

    .nilai-table { width: 100%; border-collapse: collapse; table-layout: auto; }
    .nilai-table th, .nilai-table td { border: 1px solid #94a3b8; padding: 3px 4px; font-size: 8.5px; }
    .nilai-table thead th { background: #e2e8f0; color: #0f172a; font-weight: 700; text-align: center; }
    .nilai-table thead .group { font-size: 9px; }
    .nilai-table tbody td { text-align: center; }
    .nilai-table td.nama { text-align: left; }
    .nilai-table td.nilai-angka { font-weight: 700; }
    .nilai-table td.na { font-weight: 700; }

    .ttd { margin-top: 14px; }
    .ttd-date { text-align: right; font-size: 9px; color: #1e293b; margin-bottom: 6px; }
    .ttd-table { width: 100%; border-collapse: collapse; }
    .ttd-table td { width: 50%; border: 1px solid #cbd5e1; padding: 8px; text-align: center; vertical-align: middle; }
    .pengesahan-role { font-size: 10px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .pengesahan-name { font-size: 10px; font-weight: 700; color: #1e293b; margin-top: 2px; text-decoration: underline; }
    .pengesahan-sign { font-size: 8px; color: #94a3b8; margin-bottom: 4px; }
    .approved-stamp { display: inline-block; margin: 2px auto 4px; }
    .approved-check { width: 30px; height: 30px; margin: 0 auto; border-radius: 50%; background: #dcfce7; border: 1.5px solid #16a34a; color: #16a34a; font-size: 20px; font-weight: 700; line-height: 26px; text-align: center; }
    .approved-text { font-size: 8.5px; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 0.5px; }
    .approved-date { font-size: 8.5px; color: #16a34a; margin-top: 2px; }
</style>
</head>
<body>

@php
    $fmt = static function ($v): string {
        if ($v === null || $v === '') return '';
        return rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    };
    $kontribusi = static function ($nilai, $persen) use ($fmt) {
        return $nilai === null ? '' : $fmt((float) $nilai * (float) $persen / 100);
    };
    $tanggal = $info['tanggalDisetujui']?->format('d/m/Y') ?? now()->format('d/m/Y');
@endphp

<div class="kop">
    <div class="kop-title">Daftar Nilai</div>
    <div class="kop-semester">Semester : {{ $info['semester'] }} {{ $info['tahun'] }}</div>
    <div class="kop-institusi">{{ $info['institusi'] }}</div>
</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <table class="info-table">
                <tr><td class="info-label">Dosen</td><td class="info-value">: {{ $info['dosen'] }}</td></tr>
                <tr><td class="info-label">NIDN/NUPN</td><td class="info-value">: {{ $info['nidn'] }}</td></tr>
                <tr><td class="info-label">Program Studi</td><td class="info-value">: {{ $info['prodi'] }}</td></tr>
                <tr><td class="info-label">Nama Matakuliah</td><td class="info-value">: {{ $info['matakuliah'] }}</td></tr>
                <tr><td class="info-label">Kode Matakuliah</td><td class="info-value">: {{ $pengampu->mataKuliah?->kode ?? '-' }}</td></tr>
            </table>
        </td>
        <td style="width: 50%;">
            <table class="info-table">
                <tr><td class="info-label">Kelas</td><td class="info-value">: {{ $pengampu->kelas ?? '-' }}</td></tr>
                <tr><td class="info-label">Semester</td><td class="info-value">: {{ $info['semesterTingkat'] }}</td></tr>
                <tr><td class="info-label">Kode Kelas</td><td class="info-value">: {{ $info['kodeKelas'] ?: '-' }}</td></tr>
                <tr><td class="info-label">Jumlah TM</td><td class="info-value">: {{ $info['jumlahTM'] }}</td></tr>
                <tr><td class="info-label">Rata-rata Kehadiran</td><td class="info-value">: {{ $info['rataKehadiran'] ?? '-' }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<table class="nilai-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 20px;">No</th>
            <th rowspan="2" style="width: 64px;">NIM</th>
            <th rowspan="2" class="nama" style="text-align: left;">Nama Mahasiswa</th>
            <th colspan="{{ $tugasList->count() + 1 }}" class="group">Tugas</th>
            <th colspan="2" class="group">Aktif</th>
            <th colspan="2" class="group">Etika</th>
            <th colspan="2" class="group"></th>
            <th colspan="4" class="group">UJIAN</th>
            <th rowspan="2">Jumlah</th>
            <th rowspan="2" style="width: 30px;">NA</th>
        </tr>
        <tr>
            @foreach($info['romawi'] as $label)
                <th>{{ $label }}</th>
            @endforeach
            <th>% tugas</th>
            <th>K</th>
            <th>% K</th>
            <th>E</th>
            <th>% E</th>
            <th>TOT</th>
            <th>%P</th>
            <th>UTS</th>
            <th>% MID</th>
            <th>UAS</th>
            <th>% UAS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            @php
                $nilai = $row['komponen'];
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['mahasiswa']->nim }}</td>
                <td class="nama">{{ $row['mahasiswa']->nama }}</td>
                @foreach($row['tugas'] as $v)
                    <td>{{ $fmt($v) ?: '-' }}</td>
                @endforeach
                <td>{{ $kontribusi($nilai['tugas'], $bobot['tugas']) ?: '-' }}</td>
                <td>{{ $fmt($nilai['keaktifan']) ?: '-' }}</td>
                <td>{{ $kontribusi($nilai['keaktifan'], $bobot['keaktifan']) ?: '-' }}</td>
                <td>{{ $fmt($nilai['etika']) ?: '-' }}</td>
                <td>{{ $kontribusi($nilai['etika'], $bobot['etika']) ?: '-' }}</td>
                <td>{{ $row['hadir'] === null ? '-' : $row['hadir'] }}</td>
                <td>{{ $kontribusi($nilai['absensi'], $bobot['absensi']) ?: '-' }}</td>
                <td>{{ $fmt($nilai['uts']) ?: '-' }}</td>
                <td>{{ $kontribusi($nilai['uts'], $bobot['uts']) ?: '-' }}</td>
                <td>{{ $fmt($nilai['uas']) ?: '-' }}</td>
                <td>{{ $kontribusi($nilai['uas'], $bobot['uas']) ?: '-' }}</td>
                <td class="nilai-angka">{{ $fmt($row['akhir']) ?: '-' }}</td>
                <td class="na">{{ $row['huruf'] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="ttd">
    <div class="ttd-date">Purworejo, {{ $tanggal }}</div>
    <table class="ttd-table">
        <tr>
            <td>
                <div class="pengesahan-role">Dosen Pengampu</div>
                <div class="approved-stamp"><div class="approved-check">✓</div></div>
                <div class="approved-text">Disetujui</div>
                <div class="pengesahan-name">{{ $info['dosen'] }}</div>
                <div class="approved-date">{{ $tanggal }}</div>
            </td>
            <td>
                <div class="pengesahan-role">Kaprodi</div>
                <div class="approved-stamp"><div class="approved-check">✓</div></div>
                <div class="approved-text">Disetujui</div>
                <div class="pengesahan-name">{{ $info['kaprodiNama'] ?? '-' }}</div>
                <div class="approved-date">{{ $tanggal }}</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>