<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>KHS - {{ $mahasiswa->nama }}</title>
    <style>
        html, body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000000;
            line-height: 1.4;
        }
        /* Kop surat */
        .kop {
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .institusi { font-size: 13px; font-weight: bold; color: #000; }
        .alamat { font-size: 9px; color: #000; }
        .judul { font-size: 16px; font-weight: bold; margin-top: 3px; color: #000; }
        .semester { font-size: 10px; font-weight: bold; color: #000; }

        /* Section title */
        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin: 10px 0 5px;
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
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
            color: #000;
            font-size: 9.5px;
        }
        table.info th {
            width: 130px;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Tabel KHS */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: left;
            vertical-align: middle;
            color: #000;
            font-size: 9px;
        }
        table.data thead th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
        }
        table.data tfoot td {
            border-top: 2px solid #000;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .empty {
            text-align: center;
            color: #000;
            border: 1px dashed #000;
            padding: 25px;
        }

        /* Summary Box */
        table.summary {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        table.summary td {
            padding: 6px 8px;
            border: 1px solid #000;
            font-size: 9.5px;
        }

        /* Signatures */
        table.ttd-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        table.ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9.5px;
        }
        .space { height: 45px; }

        .validation-stamp {
            border: 1px dashed #000;
            padding: 3px 6px;
            font-size: 8px;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="kop">
        <div class="institusi">POLITEKNIK SAWUNGGALIH AJI (POLSA)</div>
        <div class="alamat">Jl. W.R. Supratman No. 5 Kutoarjo, Purworejo, Jawa Tengah 54212 | Telp. (0275) 640123 | www.polsa.ac.id</div>
        <div class="judul">KARTU HASIL STUDI (KHS)</div>
        <div class="semester">
            {{ $tahunAkademik ? 'Tahun Akademik '.$tahunAkademik->tahun.' / Semester '.ucfirst($tahunAkademik->semester) : 'Semua Tahun Akademik' }}
        </div>
    </div>

    <div class="section-title">I. Identitas Mahasiswa</div>
    <table class="info">
        <tr>
            <th>NIM</th>
            <td>{{ $mahasiswa->nim }}</td>
            <th style="width: 90px;">Angkatan</th>
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

    <div class="section-title">II. Rincian Hasil Penilaian Mata Kuliah</div>

    @if(empty($khsData['items']))
        <div class="empty">
            Mahasiswa belum terdaftar pada kelas mata kuliah manapun.
        </div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th style="width: 35px;">SKS</th>
                    <th style="width: 55px;">Nilai Angka</th>
                    <th style="width: 50px;">Huruf Mutu</th>
                    <th style="width: 50px;">Bobot Mutu</th>
                    <th style="width: 65px;">Nilai Mutu (K&times;M)</th>
                    <th style="width: 65px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($khsData['items'] as $item)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $item['kode'] }}</td>
                        <td>
                            <strong>{{ $item['nama'] }}</strong>
                            <div style="font-size: 8px; color: #555;">Dosen: {{ $item['dosen'] }}</div>
                        </td>
                        <td class="center">{{ $item['sks'] }}</td>
                        <td class="center">{{ $item['nilai_angka'] }}</td>
                        <td class="center"><strong>{{ $item['nilai_huruf'] }}</strong></td>
                        <td class="center">{{ $item['bobot_mutu'] }}</td>
                        <td class="center"><strong>{{ $item['nilai_mutu'] }}</strong></td>
                        <td class="center">{{ $item['nilai_huruf'] !== '-' ? ($item['lulus'] ? 'Lulus' : 'Tidak Lulus') : 'Berjalan' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="right">Total SKS &amp; Nilai Mutu:</td>
                    <td class="center">{{ $khsData['total_sks'] }}</td>
                    <td colspan="3" class="right">Jumlah (K &times; M):</td>
                    <td class="center">{{ $khsData['total_poin'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

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

        <table class="summary">
            <tr>
                <td style="width: 50%; background-color: #f2f2f2;">
                    <strong>Indeks Prestasi Semester (IPS):</strong>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 2px;">{{ $khsData['ips'] }} / 4.00</div>
                    <div style="font-size: 8.5px; color: #333;">Rumus: {{ $khsData['total_poin'] }} &divide; {{ $khsData['total_sks'] }} SKS</div>
                </td>
                <td style="width: 50%;">
                    <strong>Predikat Capaian:</strong>
                    <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">{{ $predikatSemester }}</div>
                    <div style="font-size: 8.5px; color: #333;">Beban SKS Semester Berikutnya: {{ $ipsNum >= 3.00 ? '24 SKS' : ($ipsNum >= 2.50 ? '21 SKS' : '18 SKS') }}</div>
                </td>
            </tr>
        </table>
    @endif

    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Dosen Pembimbing Akademik</strong>
                <div class="space"></div>
                <div style="text-decoration: underline; font-weight: bold;">( ________________________ )</div>
                <div>NIDN. ........................................</div>
            </td>
            <td>
                Purworejo, {{ $approval?->approved_at ? $approval->approved_at->format('d F Y') : now()->format('d F Y') }}<br>
                <strong>Ketua Program Studi</strong>
                <div class="space">
                    @if($approval && $approval->isDisetujui())
                        <div class="validation-stamp">
                            TERVERIFIKASI RESMI<br>
                            {{ $approval->approver?->name }} ({{ $approval->approved_at?->format('d/m/Y') }})
                        </div>
                    @endif
                </div>
                <div style="text-decoration: underline; font-weight: bold;">
                    ( {{ $approval?->approver?->name ?? '________________________' }} )
                </div>
                <div>NIDN. ........................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
