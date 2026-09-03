<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>KRS - {{ $mahasiswa->nama }}</title>
    <style>
        html, body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        /* Kop Surat Resmi */
        .kop {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-align: center;
        }
        .institusi { font-size: 14px; font-weight: bold; color: #000; text-transform: uppercase; letter-spacing: 0.5px; }
        .alamat { font-size: 8.5px; color: #334155; margin-top: 2px; }
        .judul { font-size: 15px; font-weight: bold; margin-top: 8px; color: #000; letter-spacing: 0.5px; text-transform: uppercase; }
        .semester { font-size: 9.5px; font-weight: bold; color: #4338ca; margin-top: 2px; }

        /* Section title */
        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin: 10px 0 5px;
            color: #0f172a;
        }

        /* Identitas */
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.info th, table.info td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
            color: #0f172a;
            font-size: 9.5px;
        }
        table.info th {
            width: 130px;
            font-weight: bold;
            background-color: #f1f5f9;
        }

        /* Tabel struktur mata kuliah */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th, table.data td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            vertical-align: middle;
            color: #0f172a;
            font-size: 9px;
        }
        table.data thead th {
            font-weight: bold;
            text-align: center;
            background-color: #e2e8f0;
            color: #1e293b;
        }
        table.data tfoot td {
            border-top: 2px solid #0f172a;
            font-weight: bold;
            background-color: #f8fafc;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .empty {
            text-align: center;
            color: #64748b;
            border: 1px dashed #cbd5e1;
            padding: 20px;
            font-size: 9.5px;
        }

        /* Signatures Table */
        table.ttd-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table.ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9.5px;
        }
        .space { height: 45px; }
    </style>
</head>
<body>

    <div class="kop">
        <div class="institusi">POLITEKNIK SAWUNGGALIH AJI (POLSA)</div>
        <div class="alamat">Jl. W.R. Supratman No. 5 Kutoarjo, Purworejo, Jawa Tengah 54212 | Telp. (0275) 640123 | www.polsa.ac.id</div>
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
            <td><strong>{{ $mahasiswa->nama }}</strong></td>
            <th>Jenis Kelas</th>
            <td>{{ $mahasiswa->jenis_kelas ? ucfirst($mahasiswa->jenis_kelas) : 'Reguler' }}</td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td colspan="3">{{ $mahasiswa->programStudi->nama_prodi ?? '-' }} ({{ $mahasiswa->programStudi->jenjang ?? 'D3' }})</td>
        </tr>
    </table>

    <div class="section-title">II. Matriks Rencana Pengambilan Mata Kuliah</div>

    @if($kelas->isEmpty())
        <div class="empty">
            Mahasiswa belum terdaftar pada kelas mata kuliah manapun.
        </div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 80px;">Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th style="width: 60px;">SKS Teori</th>
                    <th style="width: 65px;">SKS Praktik</th>
                    <th style="width: 55px;">Total SKS</th>
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
                        <td><strong>{{ $pengampu->nama_mata_kuliah }}</strong></td>
                        <td class="center">{{ $pengampu->mataKuliah?->sks_teori ?? 0 }}</td>
                        <td class="center">{{ $pengampu->mataKuliah?->sks_praktikum ?? 0 }}</td>
                        <td class="center"><strong>{{ $pengampu->total_sks }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="right">Total Beban SKS Ditempuh:</td>
                    <td class="center">{{ $totalTeori }}</td>
                    <td class="center">{{ $totalPraktik }}</td>
                    <td class="center">{{ $totalTeori + $totalPraktik }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="ttd-table">
        <tr>
            <td>
                Menyetujui,<br>
                <strong>Mahasiswa Bersangkutan</strong>
                <div class="space"></div>
                <div style="text-decoration: underline; font-weight: bold;">( {{ $mahasiswa->nama }} )</div>
                <div>NIM. {{ $mahasiswa->nim }}</div>
            </td>
            <td>
                Purworejo, {{ now()->format('d F Y') }}<br>
                <strong>Ketua Program Studi</strong>
                <div class="space"></div>
                <div style="text-decoration: underline; font-weight: bold;">( ________________________ )</div>
                <div>NIDN/NIP. ........................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
