<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>RPS - {{ $rps->mataKuliah->nama }}</title>
    <style>
        /* ===== Page setup (A4 landscape) ===== */
        @page {
            size: A4 landscape;
            margin-top: 16mm;
            margin-right: 13mm;
            margin-bottom: 16mm;
            margin-left: 13mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body, p, h1, h2, h3, h4, h5, h6, ol, ul, table {
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-semibold { font-weight: 600; }

        /* ===== Cover / Header ===== */
        .cover {
            text-align: center;
            padding: 30px 20px 24px;
            border-bottom: 2.5pt solid #1e293b;
            margin-bottom: 16px;
            page-break-after: always;
        }

        .cover-logo {
            display: block;
            margin: 0 auto 10px;
            width: 70px;
            height: auto;
        }

        .cover-institution {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .cover-address {
            font-size: 8.5px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .cover-title {
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 12px 0 16px;
            color: #0f172a;
        }

        .cover-subject-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cover-subject-name {
            font-size: 17px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .cover-subject-code {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .cover-oleh {
            font-size: 9px;
            color: #64748b;
        }

        .cover-penyusun {
            font-size: 10.5px;
            font-weight: 700;
            color: #334155;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cover-penyusun-name {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            text-decoration: underline;
        }

        .cover-prodi {
            font-size: 10.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .cover-location {
            font-size: 10px;
            color: #475569;
            margin-top: 12px;
        }

        /* ===== Section ===== */
        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            margin: 14px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1.5px solid #1e293b;
            page-break-after: avoid;
        }

        .section:first-child .section-title { margin-top: 0; }

        .note-text {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.5;
        }

        .note-info {
            margin: 6px 0 0;
            padding: 6px 10px;
            border-left: 3px solid #94a3b8;
            background: #f8fafc;
            font-size: 8.5px;
            color: #475569;
            line-height: 1.5;
        }

        /* ===== Tables: base ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        thead { display: table-header-group; }

        /* ===== Info table (Informasi Umum) ===== */
        .info-table {
            margin: 0 0 6px;
        }

        .info-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            line-height: 1.5;
        }

        .info-table tr {
            page-break-inside: avoid;
        }

        .info-table .label-cell {
            width: 24%;
            font-weight: 700;
            background: #f1f5f9;
            color: #334155;
            font-size: 10px;
        }

        .info-table .value-cell {
            color: #1e293b;
            font-size: 10.5px;
        }

        /* ===== Pengesahan ===== */
        .pengesahan {
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .pengesahan-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #1e293b;
            text-align: center;
        }

        .pengesahan-table {
            width: 70%;
            margin: 0 auto;
        }

        .pengesahan-table td {
            width: 50%;
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
            page-break-inside: avoid;
        }

        .pengesahan-role {
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .pengesahan-space {
            height: 34px;
        }

        .pengesahan-sign {
            font-size: 8.5px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .pengesahan-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 2px;
            text-decoration: underline;
        }

        .pengesahan-extra {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 4px;
        }

        .approved-stamp {
            display: inline-block;
            margin: 2px auto 4px;
        }

        .approved-check {
            width: 30px;
            height: 30px;
            margin: 0 auto;
            border-radius: 50%;
            background: #dcfce7;
            border: 1.5px solid #16a34a;
            color: #16a34a;
            font-size: 20px;
            font-weight: 700;
            line-height: 26px;
            text-align: center;
        }

        .approved-text {
            font-size: 8.5px;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .approved-date {
            font-size: 8.5px;
            color: #16a34a;
            margin-top: 2px;
        }

        /* ===== Generic data table (CPL, CPMK, Sub-CPMK, Bahan Kajian, Evaluasi) ===== */
        .data-table {
            margin: 0 0 6px;
            font-size: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 9px;
            vertical-align: top;
            line-height: 1.5;
        }

        .data-table th {
            background: #334155;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            font-size: 9.5px;
        }

        .data-table td {
            color: #1e293b;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* ===== Schedule table (Rencana Pembelajaran & Tugas) ===== */
        .schedule-table {
            margin: 0 0 6px;
            font-size: 9px;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            vertical-align: top;
            line-height: 1.45;
        }

        .schedule-table th {
            background: #334155;
            color: #ffffff;
            font-weight: 600;
            text-align: center;
            font-size: 9px;
        }

        .schedule-table td {
            color: #1e293b;
        }

        .schedule-table tr {
            page-break-inside: avoid;
        }

        .schedule-table tr:nth-child(even) td {
            background: #fbfcfd;
        }

        /* ===== Korelasi table ===== */
        .korelasi-table {
            margin: 0 0 6px;
            font-size: 9px;
        }

        .korelasi-table th,
        .korelasi-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            vertical-align: middle;
            text-align: center;
            line-height: 1.45;
        }

        .korelasi-table th {
            background: #334155;
            color: #fff;
            font-weight: 600;
        }

        .korelasi-table th:first-child,
        .korelasi-table td:first-child {
            text-align: left;
            font-weight: 600;
        }

        .korelasi-table tr {
            page-break-inside: avoid;
        }

        /* ===== Daftar pustaka ===== */
        .daftar-pustaka {
            padding-left: 20px;
            font-size: 10px;
            line-height: 1.6;
        }

        .daftar-pustaka li {
            margin-bottom: 3px;
        }

        /* ===== Total row (Evaluasi) ===== */
        .total-row td {
            background: #eef2f7 !important;
            font-weight: 700;
            border-top: 1.5px solid #64748b;
        }

        /* ===== Empty state ===== */
        .empty-state {
            color: #64748b;
            font-style: italic;
            font-size: 10px;
            padding: 4px 0;
        }

        /* ===== Approval (bottom) ===== */
        .approval-box {
            margin-top: 16px;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 9.5px;
            page-break-inside: avoid;
        }

        .approval-label {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .approval-name {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
        }

        .approval-date {
            font-size: 9px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ===== Footer ===== */
        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 8px;
        }

        /* Running header / footer (repeated on every page by DomPdf) */
        #run-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #64748b;
            padding: 2pt 0;
        }

        .pn:after { content: counter(page); }

        #run-footer .dash {
            color: #94a3b8;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    {{-- Running footer: repeated on every page by DomPdf --}}
    <div id="run-footer">
        POLITEKNIK SAWUNGGALIH AJI <span class="dash">•</span> RENCANA PEMBELAJARAN SEMESTER (RPS) <span class="dash">•</span> Halaman <span class="pn"></span> dari {{ $pageTotal ?? '' }}
    </div>

    @php
        $bulanMap = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $tanggalPenyusunan = $rps->created_at
            ? $bulanMap[$rps->created_at->month - 1].' '.$rps->created_at->year
            : (string) ($rps->mataKuliah->kurikulum->tahun_berlaku ?? '');

        $subCpmkRows = [];
        foreach ($rps->pertemuans as $p) {
            $kode = trim((string) $p->sub_cpmk) ?: ('Minggu '.$p->minggu);
            if (! isset($subCpmkRows[$kode])) {
                $subCpmkRows[$kode] = $p;
            }
        }

        $cpmks = \Illuminate\Support\Facades\Schema::hasTable('cpmk_mata_kuliah')
            ? $rps->mataKuliah->cpmks
            : collect([]);

        $hasKorelasi = $rps->pertemuans->contains(fn ($p) => ! empty($p->cpmk_induk));

        $daftarPustaka = array_values(array_filter(
            array_map('trim', explode("\n", (string) $rps->daftar_pustaka)),
            fn ($item) => $item !== ''
        ));

        $logoPath = public_path('images/logo-kampus.jpg');
        $logoDataUri = \Illuminate\Support\Facades\File::exists($logoPath)
            ? 'data:image/jpeg;base64,'.base64_encode(\Illuminate\Support\Facades\File::get($logoPath))
            : null;
    @endphp

    {{-- COVER --}}
    <div class="cover">
        @if($logoDataUri)
        <img src="{{ $logoDataUri }}" alt="Logo" class="cover-logo">
        @endif
        <div class="cover-institution">POLITEKNIK SAWUNGGALIH AJI</div>
        <div class="cover-title">RENCANA PEMBELAJARAN SEMESTER (RPS)</div>
        <div class="cover-subject-label">Mata Kuliah</div>
        <div class="cover-subject-name">{{ $rps->mataKuliah->nama }}</div>
        <div class="cover-subject-code">Kode: {{ $rps->mataKuliah->kode }}</div>
        <div class="cover-oleh">oleh</div>
        <div class="cover-penyusun">PENYUSUN</div>
        <div class="cover-penyusun-name">{{ $rps->dosen_pengembang_rps ?? $rps->dosen_pengampu }}</div>
        <div class="cover-prodi">Program Studi {{ $rps->mataKuliah->kurikulum->programStudi->jenjang ?? '' }} {{ $rps->mataKuliah->kurikulum->programStudi->nama_prodi ?? '' }}</div>
        <div class="cover-location">Purworejo, {{ $tanggalPenyusunan }}</div>
    </div>

    {{-- SECTION 1: INFORMASI UMUM --}}
    <div class="section">
        <div class="section-title">2. Informasi Umum</div>
        <table class="info-table">
            <tr>
                <td class="label-cell">Mata Kuliah (MK)</td>
                <td class="value-cell">{{ $rps->mataKuliah->nama }}</td>
            </tr>
            <tr>
                <td class="label-cell">Kode</td>
                <td class="value-cell">{{ $rps->mataKuliah->kode }}</td>
            </tr>
            <tr>
                <td class="label-cell">Rumpun MK (RMK)</td>
                <td class="value-cell">{{ $rps->rumpun_mk ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Bobot (SKS)</td>
                <td class="value-cell">{{ $rps->mataKuliah->total_sks }} SKS ({{ $rps->mataKuliah->sks_teori }} Teori + {{ $rps->mataKuliah->sks_praktikum }} Praktik)</td>
            </tr>
            <tr>
                <td class="label-cell">Semester</td>
                <td class="value-cell">{{ $rps->semester }}</td>
            </tr>
            <tr>
                <td class="label-cell">Dosen Pengampu</td>
                <td class="value-cell">{{ $rps->dosen_pengampu }}</td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal Penyusunan</td>
                <td class="value-cell">{{ $rps->created_at?->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label-cell">MK yang Menjadi Prasyarat</td>
                <td class="value-cell">{{ $rps->mk_prasyarat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Menjadi Prasyarat untuk MK</td>
                <td class="value-cell">{{ $rps->prasyarat_untuk ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Integrasi Antar MK</td>
                <td class="value-cell">{{ $rps->integrasi_antar_mk ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Tautan Kelas Daring</td>
                <td class="value-cell">{{ $rps->tautan_daring ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Deskripsi Mata Kuliah</td>
                <td class="value-cell">{{ $rps->deskripsi_mata_kuliah ?? '-' }}</td>
            </tr>
        </table>

        {{-- PENGESAHAN --}}
        <div class="pengesahan">
            <div class="pengesahan-title">Pengesahan</div>
            <table class="pengesahan-table">
                <tr>
                    <td>
                        <div class="pengesahan-role">Ketua Program Studi</div>
                        @if($rps->status === 'Disetujui')
                        <div class="approved-stamp">
                            <div class="approved-check">✓</div>
                        </div>
                        <div class="approved-text">Disetujui</div>
                        @if($rps->disetujuiOleh)
                        <div class="pengesahan-name">{{ $rps->disetujuiOleh->name }}</div>
                        @endif
                        @if($rps->tanggal_disetujui)
                        <div class="approved-date">{{ $rps->tanggal_disetujui->format('d/m/Y') }}</div>
                        @endif
                        @else
                        <div class="pengesahan-space"></div>
                        <div class="pengesahan-sign">(Tanda tangan)</div>
                        <div class="pengesahan-name">{{ $rps->ketua_prodi ?? '-' }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="pengesahan-role">Dosen Pengembang RPS</div>
                        @if($rps->status === 'Disetujui')
                        <div class="approved-stamp">
                            <div class="approved-check">✓</div>
                        </div>
                        <div class="approved-text">Disetujui</div>
                        @if($rps->dosen_pengembang_rps)
                        <div class="pengesahan-name">{{ $rps->dosen_pengembang_rps }}</div>
                        @endif
                        @if($rps->tanggal_disetujui)
                        <div class="approved-date">{{ $rps->tanggal_disetujui->format('d/m/Y') }}</div>
                        @endif
                        @else
                        <div class="pengesahan-space"></div>
                        <div class="pengesahan-sign">(Tanda tangan)</div>
                        <div class="pengesahan-name">{{ $rps->dosen_pengembang_rps ?? '-' }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- SECTION 2: CPL --}}
    @if($rps->mataKuliah->cpls->count())
    <div class="section">
        <div class="section-title">CPL-Prodi yang Dibebankan kepada MK</div>
        <p class="note-text">
            Catatan: rumusan CPL di atas sesuai kode CPL pada matriks kurikulum Program Studi.
            Mohon disesuaikan dengan rumusan resmi dokumen CPL Program Studi.
        </p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 120px;">Kode</th>
                    <th>Deskripsi CPL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rps->mataKuliah->cpls as $cpl)
                <tr>
                    <td class="text-center">{{ $cpl->kode_cpl }}</td>
                    <td>{{ $cpl->deskripsi }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECTION 3: CPMK --}}
    @if($cpmks->count())
    <div class="section">
        <div class="section-title">Capaian Pembelajaran Mata Kuliah (CPMK)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 120px;">Kode</th>
                    <th>Deskripsi CPMK</th>
                    <th style="width: 140px;">CPL Terkait</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cpmks as $cpmk)
                <tr>
                    <td class="text-center">{{ $cpmk->kode_cpmk }}</td>
                    <td>{{ $cpmk->deskripsi }}</td>
                    <td>
                        @php
                            $relatedCpls = $cpmk->cpls->pluck('kode_cpl')->implode(', ');
                        @endphp
                        {{ $relatedCpls ?: '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECTION 4: SUB-CPMK --}}
    @if(count($subCpmkRows))
    <div class="section">
        <div class="section-title">Sub-Capaian Pembelajaran Mata Kuliah (Sub-CPMK)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 120px;">Kode</th>
                    <th style="width: 180px;">CPMK Induk</th>
                    <th>Deskripsi Sub-CPMK</th>
                    <th>Indikator Ketercapaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subCpmkRows as $kode => $sub)
                <tr>
                    <td class="text-center">{{ $kode }}</td>
                    <td>{{ $sub->cpmk_induk ?? '-' }}</td>
                    <td>{{ $sub->pengalaman_belajar ?? '-' }}</td>
                    <td>{{ $sub->indikator ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECTION 5: KORELASI CPMK TERHADAP SUB-CPMK --}}
    @if(count($subCpmkRows) && $cpmks->count())
    <div class="section">
        <div class="section-title">Korelasi CPMK terhadap Sub-CPMK</div>
        <table class="korelasi-table">
            <thead>
                <tr>
                    <th>CPMK</th>
                    @foreach(array_keys($subCpmkRows) as $col)
                    <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($cpmks as $cpmk)
                <tr>
                    <td>{{ $cpmk->kode_cpmk }}</td>
                    @foreach(array_keys($subCpmkRows) as $col)
                    @php
                        $terkait = false;
                        foreach ($rps->pertemuans as $p) {
                            if ((string) trim((string) $p->sub_cpmk) === $col
                                && $p->cpmk_induk
                                && str_contains($p->cpmk_induk, $cpmk->kode_cpmk)) {
                                $terkait = true;
                                break;
                            }
                        }
                    @endphp
                    <td>{{ $terkait ? '√' : '–' }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @unless($hasKorelasi)
        <p class="note-text">Catatan: korelasi ditentukan dari kolom "CPMK Induk" pada tiap pertemuan.</p>
        @endunless
    </div>
    @endif

    {{-- SECTION 6: BAHAN KAJIAN --}}
    @if($rps->mataKuliah->bahanKajians->count())
    <div class="section">
        <div class="section-title">Bahan Kajian: Materi Pembelajaran</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Kode BK</th>
                    <th>Bahan Kajian</th>
                    <th>Materi Pembelajaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rps->mataKuliah->bahanKajians as $bk)
                <tr>
                    <td class="text-center">{{ $bk->kode_bk }}</td>
                    <td>{{ $bk->nama_bk }}</td>
                    <td>{{ $bk->referensi ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECTION 7: DAFTAR PUSTAKA --}}
    <div class="section">
        <div class="section-title">Daftar Pustaka (5 Tahun Terakhir)</div>
        @if(count($daftarPustaka))
        <ol class="daftar-pustaka">
            @foreach($daftarPustaka as $item)
            <li>{{ $item }}</li>
            @endforeach
        </ol>
        @else
        <p class="empty-state">Daftar pustaka belum terisi.</p>
        @endif
    </div>

    {{-- SECTION 8: RENCANA PEMBELAJARAN --}}
    <div class="section page-break">
        <div class="section-title">3. Rencana Pembelajaran</div>
        @if($rps->pertemuans->count())
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Minggu ke-</th>
                    <th style="width: 12%;">Sub-CPMK</th>
                    <th style="width: 13%;">Indikator</th>
                    <th style="width: 13%;">Teknik &amp; Kriteria</th>
                    <th style="width: 12%;">Daring (Online)</th>
                    <th style="width: 12%;">Luring (Offline)</th>
                    <th style="width: 28%;">Materi Pembelajaran</th>
                    <th style="width: 5%;">Bobot (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rps->pertemuans as $pertemuan)
                <tr>
                    <td class="text-center">{{ $pertemuan->minggu }}</td>
                    <td>{{ $pertemuan->sub_cpmk }}</td>
                    <td>{{ $pertemuan->indikator ?? '-' }}</td>
                    <td>{{ $pertemuan->teknik_kriteria ?? '-' }}</td>
                    <td>{{ $pertemuan->metode_daring ?? '-' }}</td>
                    <td>{{ $pertemuan->metode_luring ?? $pertemuan->metode ?? '-' }}</td>
                    <td>{{ $pertemuan->materi }}</td>
                    <td class="text-center">{{ $pertemuan->bobot }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="note-text">
            Sinkron: interaksi pembelajaran antara dosen dan mahasiswa pada waktu bersamaan (audio/video conference).
            Asinkron: interaksi pembelajaran fleksibel, tidak harus dalam waktu yang sama (forum diskusi, belajar mandiri/penugasan).
            Estimasi 1 SKS setara 170 menit belajar per minggu.
        </p>
        @else
        <p class="empty-state">Belum ada data pertemuan.</p>
        @endif
    </div>

    {{-- SECTION 9: RANCANGAN TUGAS DAN LATIHAN --}}
    <div class="section page-break">
        <div class="section-title">4. Rancangan Tugas dan Latihan</div>
        @if($rps->tugas->count())
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Minggu Ke / Topik</th>
                    <th style="width: 14%;">Nama Tugas</th>
                    <th style="width: 10%;">Sub-CPMK</th>
                    <th style="width: 14%;">Penugasan</th>
                    <th style="width: 14%;">Ruang Lingkup</th>
                    <th style="width: 14%;">Cara Pengerjaan</th>
                    <th style="width: 12%;">Batas Waktu</th>
                    <th style="width: 12%;">Luaran Tugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rps->tugas as $tugas)
                <tr>
                    <td class="text-center">{{ $tugas->minggu_topik }}</td>
                    <td class="font-semibold">{{ $tugas->nama_tugas }}</td>
                    <td>{{ $tugas->sub_cpmk ?? '-' }}</td>
                    <td>{{ $tugas->penugasan ?? '-' }}</td>
                    <td>{{ $tugas->ruang_lingkup ?? '-' }}</td>
                    <td>{{ $tugas->cara_pengerjaan ?? '-' }}</td>
                    <td>{{ $tugas->batas_waktu ?? '-' }}</td>
                    <td>{{ $tugas->luaran_tugas ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="empty-state">Belum ada data rancangan tugas.</p>
        @endif
    </div>

    {{-- SECTION 10: RANCANGAN EVALUASI --}}
    <div class="section">
        <div class="section-title">5. Rancangan Evaluasi</div>
        @if($rps->bentukEvaluasis->count())
        @php
            $totalBobotEval = $rps->bentukEvaluasis->sum('bobot');
        @endphp
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Bentuk Evaluasi</th>
                    <th style="width: 14%;">Sub-CPMK</th>
                    <th style="width: 16%;">Instrumen Formatif</th>
                    <th style="width: 16%;">Instrumen Sumatif</th>
                    <th style="width: 26%;">Tagihan (Bukti)</th>
                    <th style="width: 10%;">Bobot (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rps->bentukEvaluasis as $be)
                <tr>
                    <td>{{ $be->bentuk_evaluasi }}</td>
                    <td>{{ $be->sub_cpmk ?? '-' }}</td>
                    <td>{{ $be->formatif ? ($be->instrumen ?? '-') : '-' }}</td>
                    <td>{{ $be->sumatif ? ($be->instrumen ?? '-') : '-' }}</td>
                    <td>{{ $be->tagihan ?? '-' }}</td>
                    <td class="text-center">{{ $be->bobot }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td class="font-semibold">Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center font-semibold">{{ number_format((float) $totalBobotEval, 0) }}%</td>
                </tr>
            </tbody>
        </table>
        <p class="note-text">
            Bentuk evaluasi dapat berupa identifikasi masalah, penyusunan dokumen, presentasi, ujian tulis, penilaian keaktifan diskusi, dan bentuk lainnya.
            Instrumen asesmen formatif berupa umpan balik; instrumen asesmen sumatif dapat berupa rubrik penilaian atau borang.
        </p>
        @else
        <p class="empty-state">Belum ada data penilaian.</p>
        @endif
    </div>

    {{-- APPROVAL --}}
    @if($rps->disetujuiOleh)
    <div class="approval-box">
        <div class="approval-label">Disetujui oleh:</div>
        <div class="approval-name">{{ $rps->disetujuiOleh->name }}</div>
        @if($rps->tanggal_disetujui)
        <div class="approval-date">{{ $rps->tanggal_disetujui->format('d/m/Y H:i') }}</div>
        @endif
    </div>
    @endif

    <div class="footer">
        Dokumen ini diekstrak dari EDUVA Polsa pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>