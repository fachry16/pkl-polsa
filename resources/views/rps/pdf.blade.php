<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>RPS - {{ $rps->mataKuliah->nama }}</title>
    <style>
        @page {
            margin: 2cm 1.8cm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-semibold { font-weight: 600; }

        /* Cover */
        .cover {
            text-align: center;
            padding: 40px 20px 30px;
            border-bottom: 3px double #1e293b;
            margin-bottom: 20px;
        }

        .cover-institution {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 30px;
        }

        .cover-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 20px;
            color: #0f172a;
        }

        .cover-subject-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .cover-subject-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .cover-subject-code {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 18px;
        }

        .cover-oleh {
            font-size: 9px;
            color: #64748b;
        }

        .cover-penyusun {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cover-penyusun-name {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .cover-prodi {
            font-size: 10px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .cover-location {
            font-size: 10px;
            color: #475569;
            margin-top: 14px;
        }

        .cover-info {
            width: 350px;
            margin: 0 auto;
            text-align: left;
        }

        .cover-info-row {
            display: table;
            width: 100%;
            padding: 4px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
        }

        .cover-info-label {
            display: table-cell;
            width: 140px;
            font-weight: 600;
            color: #94a3b8;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: top;
        }

        .cover-info-value {
            display: table-cell;
            color: #1e293b;
            font-weight: 500;
            font-size: 10px;
        }

        /* Section */
        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #1e293b;
        }

        .note-text {
            font-size: 8px;
            color: #64748b;
            margin: 0 0 8px;
            line-height: 1.5;
        }

        /* Info table (key-value) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .info-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 10px;
        }

        .info-table .label-cell {
            width: 200px;
            font-weight: 600;
            background: #f8fafc;
            color: #334155;
            white-space: nowrap;
        }

        .info-table .value-cell {
            color: #1e293b;
        }

        /* Pengesahan / signature block */
        .pengesahan {
            margin-top: 14px;
        }

        .pengesahan-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
        }

        .pengesahan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pengesahan-table td {
            width: 33.33%;
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
        }

        .pengesahan-role {
            font-size: 10px;
            font-weight: 600;
            color: #334155;
        }

        .pengesahan-space {
            height: 40px;
        }

        .pengesahan-sign {
            font-size: 8px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .pengesahan-name {
            font-size: 10px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Data tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
            font-size: 9px;
        }

        .data-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
            text-align: left;
            font-size: 9px;
        }

        .data-table td {
            color: #1e293b;
        }

        /* Schedule table */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 8px;
        }

        .schedule-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
            text-align: left;
        }

        /* Korelasi table */
        .korelasi-table {
            width: 100%;
            border-collapse: collapse;
        }

        .korelasi-table th,
        .korelasi-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 8px;
            text-align: center;
        }

        .korelasi-table th:first-child,
        .korelasi-table td:first-child {
            text-align: left;
            font-weight: 600;
        }

        .korelasi-table th {
            background: #f1f5f9;
        }

        /* Daftar pustaka */
        .daftar-pustaka {
            padding-left: 18px;
            font-size: 9px;
        }

        .daftar-pustaka li {
            margin-bottom: 3px;
        }

        /* Total row */
        .total-row td {
            background: #f1f5f9;
            font-weight: 600;
            border-top: 2px solid #94a3b8;
        }

        /* Empty state */
        .empty-state {
            color: #94a3b8;
            font-style: italic;
            font-size: 10px;
        }

        /* Approval */
        .approval-box {
            margin-top: 20px;
            padding: 10px 12px;
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            border-radius: 4px;
            font-size: 9px;
        }

        .approval-label {
            font-weight: 600;
            color: #065f46;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .approval-name {
            font-size: 11px;
            font-weight: 600;
            color: #065f46;
        }

        .approval-date {
            font-size: 9px;
            color: #047857;
            margin-top: 1px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

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
    @endphp

    {{-- COVER --}}
    <div class="cover">
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
                        <div class="pengesahan-role">Dosen Pengembang RPS</div>
                        <div class="pengesahan-space"></div>
                        <div class="pengesahan-sign">(Tanda tangan)</div>
                        <div class="pengesahan-name">{{ $rps->dosen_pengembang_rps ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="pengesahan-role">Koordinator RMK</div>
                        <div class="pengesahan-space"></div>
                        <div class="pengesahan-sign">(Jika ada)</div>
                        <div class="pengesahan-name">{{ $rps->koordinator_rmk ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="pengesahan-role">Ketua Program Studi</div>
                        <div class="pengesahan-space"></div>
                        <div class="pengesahan-sign">(Tanda tangan)</div>
                        <div class="pengesahan-name">{{ $rps->ketua_prodi ?? '-' }}</div>
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
    <div class="section">
        <div class="section-title">3. Rencana Pembelajaran</div>
        @if($rps->pertemuans->count())
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 40px;">Minggu ke-</th>
                    <th>Sub-CPMK</th>
                    <th colspan="2">Penilaian</th>
                    <th colspan="2">Metode Pembelajaran (Estimasi Waktu)</th>
                    <th>Materi Pembelajaran</th>
                    <th style="width: 40px;">Bobot (%)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Indikator</th>
                    <th>Teknik &amp; Kriteria</th>
                    <th>Daring (Online)</th>
                    <th>Luring (Offline)</th>
                    <th></th>
                    <th></th>
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
    <div class="section">
        <div class="section-title">4. Rancangan Tugas dan Latihan</div>
        @if($rps->tugas->count())
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 55px;">Minggu Ke / Topik</th>
                    <th>Nama Tugas</th>
                    <th style="width: 90px;">Sub-CPMK</th>
                    <th style="width: 110px;">Penugasan</th>
                    <th>Ruang Lingkup</th>
                    <th>Cara Pengerjaan</th>
                    <th style="width: 80px;">Batas Waktu</th>
                    <th>Luaran Tugas yang Dihasilkan</th>
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
        @if($rps->penilaians->count())
        @php
            $penilaian = $rps->penilaians->first();
            $evalRows = [
                'Partisipasi & Keaktifan' => ['sub_cpmk' => 'Seluruh Sub-CPMK', 'bobot' => null],
                'Tugas' => ['sub_cpmk' => null, 'bobot' => $penilaian->tugas],
                'Kuis' => ['sub_cpmk' => null, 'bobot' => $penilaian->quiz],
                'Praktikum' => ['sub_cpmk' => null, 'bobot' => $penilaian->praktikum],
                'Proyek' => ['sub_cpmk' => null, 'bobot' => $penilaian->project],
                'Tes Tulis (UTS)' => ['sub_cpmk' => null, 'bobot' => $penilaian->uts],
                'Tes Tulis (UAS)' => ['sub_cpmk' => null, 'bobot' => $penilaian->uas],
            ];
        @endphp
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bentuk Evaluasi</th>
                    <th>Sub-CPMK</th>
                    <th colspan="2">Instrumen Penilaian</th>
                    <th>Tagihan (Bukti)</th>
                    <th style="width: 60px;">Bobot (%)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Formatif</th>
                    <th>Sumatif</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($evalRows as $nama => $row)
                @if($row['bobot'] === null || $row['bobot'] > 0)
                <tr>
                    <td>{{ $nama }}</td>
                    <td>{{ $row['sub_cpmk'] ?? '-' }}</td>
                    <td>{{ $row['bobot'] === null ? 'Observasi keaktifan diskusi, workshop, dan presentasi' : '-' }}</td>
                    <td>{{ $row['bobot'] === null ? 'Presensi dan catatan keaktifan' : '-' }}</td>
                    <td>{{ $row['bobot'] === null ? 'Presensi dan catatan kontribusi' : '-' }}</td>
                    <td class="text-center">{{ $row['bobot'] === null ? '-' : $row['bobot'] }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="total-row">
                    <td class="font-semibold">Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center font-semibold">{{ number_format((float) $penilaian->tugas + (float) $penilaian->quiz + (float) $penilaian->uts + (float) $penilaian->uas + (float) $penilaian->praktikum + (float) $penilaian->project, 0) }}%</td>
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
        Dokumen ini diekstrak dari PIKOBE Polsa pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>