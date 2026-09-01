<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>KRS - {{ $mahasiswa->nama }}</title>
    <style>
        html, body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1.5;
        }
        /* Kop surat */
        .kop {
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .institusi { font-size: 14px; font-weight: bold; color: #000; }
        .alamat { font-size: 10px; color: #000; }
        .judul { font-size: 17px; font-weight: bold; margin-top: 3px; color: #000; }
        .semester { font-size: 11px; font-weight: bold; color: #000; }

        /* Section title */
        .section-title {
            font-weight: bold;
            margin: 12px 0 6px;
            color: #000;
        }

        /* Identitas */
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.info th, table.info td {
            border: 1px solid #000;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
            color: #000;
        }
        table.info th {
            width: 150px;
            font-weight: bold;
        }

        /* Tabel struktur mata kuliah */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
            color: #000;
        }
        table.data thead th {
            font-weight: bold;
            text-align: center;
        }
        table.data tfoot td {
            border-top: 2px solid #000;
            font-weight: bold;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .empty {
            text-align: center;
            color: #000;
            border: 1px dashed #000;
            padding: 30px;
        }
        .signature {
            width: 250px;
            margin-top: 30px;
            text-align: center;
            float: right;
            color: #000;
        }
        .signature .space { height: 60px; }
    </style>
</head>
<body>

    <div class="kop">
        <div class="institusi">PIKOBE - Politeknik Sawunggaling Aji</div>
        <div class="alamat">Purworejo, Jawa Tengah</div>
        <div class="judul">KARTU RENCANA STUDI (KRS)</div>
        <div class="semester">
            {{ $tahunAkademik ? 'Tahun Akademik '.$tahunAkademik->tahun.' / Semester '.ucfirst($tahunAkademik->semester) : 'Semua Tahun Akademik' }}
        </div>
    </div>


    <div class="section-title">I. Identitas Mahasiswa</div>
    <table class="info">
        <tr>
            <th>NIM</th>
            <td>{{ $mahasiswa->nim }}</td>
            <th style="width: 100px;">Angkatan</th>
            <td>{{ $mahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <th>Nama Mahasiswa</th>
            <td colspan="3"><strong>{{ $mahasiswa->nama }}</strong></td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td colspan="3">{{ $mahasiswa->programStudi->nama_prodi ?? '-' }} ({{ $mahasiswa->programStudi->kode_prodi ?? '-' }})</td>
        </tr>
    </table>

    <div class="section-title">II. Struktur Mata Kuliah</div>

    @if($kelas->isEmpty())
        <div class="empty">
            Mahasiswa belum terdaftar pada kelas manapun.
        </div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 90px;">Kode MK</th>
                    <th style="text-align: left;">Nama Mata Kuliah</th>
                    <th style="width: 70px;">SKS Teori</th>
                    <th style="width: 70px;">SKS Praktik</th>
                    <th style="width: 50px;">Total</th>
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
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $pengampu->kode_mata_kuliah }}</td>
                        <td>{{ $pengampu->nama_mata_kuliah }}</td>
                        <td class="center">{{ $pengampu->mataKuliah?->sks_teori ?? 0 }}</td>
                        <td class="center">{{ $pengampu->mataKuliah?->sks_praktikum ?? 0 }}</td>
                        <td class="center">{{ $pengampu->total_sks }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="right">Total SKS</td>
                    <td class="center">{{ $totalTeori }}</td>
                    <td class="center">{{ $totalPraktik }}</td>
                    <td class="center">{{ $totalTeori + $totalPraktik }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="signature">
        <div>Purworejo, {{ now()->format('d F Y') }}</div>
        <div style="margin-top: 4px;">Kaprodi,</div>
        <br><br><br>
        <div class="space"></div>
        <div style="font-weight: bold; text-decoration: underline;">( ___________________ )</div>
        <div>NIP. _________________</div>
    </div>

</body>
</html>
