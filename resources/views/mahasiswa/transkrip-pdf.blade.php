<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip — {{ $mahasiswa->nama }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0.5cm; color: #0f172a; font-size: 10pt; }
        .kop { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 0.25cm; margin-bottom: 0.6cm; }
        .kop h2 { margin: 0; font-size: 13pt; }
        .kop p { margin: 0; font-size: 9pt; color: #475569; }
        .meta { margin-bottom: 0.6cm; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { font-size: 9pt; padding: 0.06cm 0.1cm; }
        .meta .label { color: #64748b; font-size: 8pt; font-weight: 600; }
        .meta .value { font-weight: 700; color: #0f172a; }
        .semester-title { font-size: 10pt; font-weight: 700; margin: 0.5cm 0 0.2cm; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th, table.data-table td { border: 0.4pt solid #64748b; padding: 0.15cm 0.2cm; font-size: 8.5pt; }
        table.data-table thead th { background: #e2e8f0; text-align: left; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 0.5cm; }
        .summary td { border: 0.4pt solid #64748b; padding: 0.2cm 0.3cm; font-size: 9pt; }
        .summary .label { color: #64748b; font-size: 8pt; font-weight: 600; }
        .summary .value { font-weight: 800; font-size: 11pt; }
        .footer { font-size: 8pt; color: #64748b; text-align: right; margin-top: 0.6cm; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>UNIVERSITAS POLITEKNIK POLSA</h2>
        <p>TRANSCRIPT — TRANSKRIP NILAI MAHASISWA</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="20%" class="label">Nama Mahasiswa</td>
                <td width="3%" class="label">:</td>
                <td width="27%" class="value">{{ $mahasiswa->nama }}</td>
                <td width="20%" class="label">Program Studi</td>
                <td width="3%" class="label">:</td>
                <td class="value">{{ $mahasiswa->programStudi?->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="label">:</td>
                <td class="value">{{ $mahasiswa->nim }}</td>
                <td class="label">Angkatan</td>
                <td class="label">:</td>
                <td class="value">{{ $mahasiswa->angkatan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    @forelse($perSemester as $semester => $mks)
        <div class="semester-title">{{ $semester }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 1.2cm; text-align: center;">No</th>
                    <th>Kode</th>
                    <th>Mata Kuliah</th>
                    <th style="width: 1.4cm; text-align: center;">SKS</th>
                    <th style="width: 1.8cm; text-align: right;">Nilai</th>
                    <th style="width: 1.4cm; text-align: center;">Huruf</th>
                    <th style="width: 1.8cm; text-align: right;">Bobot Mutu</th>
                    <th style="width: 2cm; text-align: right;">SKS × Mutu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mks['rows'] as $index => $mk)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $mk['mata_kuliah']->kode ?? '-' }}</td>
                        <td style="font-weight: 600;">{{ $mk['mata_kuliah']->nama ?? '-' }}</td>
                        <td style="text-align: center;">{{ $mk['sks'] }}</td>
                        <td style="text-align: right;">{{ number_format($mk['nilai'], 2) }}</td>
                        <td style="text-align: center;">{{ $mk['huruf'] ?? '-' }}</td>
                        <td style="text-align: right;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['bobot_mutu'], 2) : '-' }}</td>
                        <td style="text-align: right;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['sks'] * $mk['bobot_mutu'], 2) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="summary">
            <tr>
                <td width="50%">
                    <div class="label">SKS Semester {{ $semester }}</div>
                    <div class="value">{{ $mks['total_sks'] }}</div>
                </td>
                <td>
                    <div class="label">IPS</div>
                    <div class="value">{{ $mks['ips'] !== null ? number_format($mks['ips'], 2) : '-' }}</div>
                </td>
            </tr>
        </table>
    @empty
        <p>Belum ada data transkrip.</p>
    @endforelse

    <table class="summary">
        <tr>
            <td width="50%">
                <div class="label">TOTAL SKS</div>
                <div class="value">{{ $totalSks }}</div>
            </td>
            <td>
                <div class="label">IPK KUMULATIF</div>
                <div class="value">{{ $ipk !== null ? number_format($ipk, 2) : '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">Dicetak otomatis dari Sistem Akademik/OBE Eduva pada: {{ now()->format('d M Y H:i') }} WIB</div>
</body>
</html>