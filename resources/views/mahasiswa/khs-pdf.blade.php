<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KHS — {{ $mahasiswa->nama }}</title>
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
        .komponen { font-size: 8pt; color: #334155; }
        .footer { font-size: 8pt; color: #64748b; text-align: right; margin-top: 0.6cm; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>UNIVERSITAS POLITEKNIK POLSA</h2>
        <p>KARTU HASIL STUDI (KHS) — PER SEMESTER</p>
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

    @forelse($semesters as $semester => $rows)
        <div class="semester-title">{{ $semester }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 1.2cm; text-align: center;">No</th>
                    <th>Mata Kuliah</th>
                    <th>Dosen</th>
                    <th style="width: 2.5cm;">Kelas</th>
                    <th>Nilai Komponen</th>
                    <th style="width: 1.8cm; text-align: right;">Nilai Akhir</th>
                    <th style="width: 1.4cm; text-align: center;">Huruf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $row['pengampu']->mataKuliah?->nama ?? '-' }}</td>
                        <td>{{ $row['pengampu']->nama_dosen }}</td>
                        <td>{{ $row['pengampu']->nama_kelas }}</td>
                        <td class="komponen">
                            @forelse($row['komponen'] as $komponen => $nilai)
                                {{ ucfirst($komponen) }}: {{ number_format($nilai, 2) }}@if(! $loop->last) | @endif
                            @empty
                                Belum dinilai
                            @endforelse
                        </td>
                        <td style="text-align: right; font-weight: 600;">{{ $row['akhir'] !== null ? number_format($row['akhir'], 2) : '-' }}</td>
                        <td style="text-align: center;">{{ $row['huruf'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p>Belum ada data nilai.</p>
    @endforelse

    <div class="footer">Dicetak otomatis dari Sistem Akademik/OBE Eduva pada: {{ now()->format('d M Y H:i') }} WIB</div>
</body>
</html>