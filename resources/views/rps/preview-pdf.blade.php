@extends('layouts.app')

@section('content')

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

<h1 class="page-header">
    RPS - {{ $rps->mataKuliah->nama }}
</h1>

<p class="page-subtitle">
    {{ $rps->mataKuliah->kode }} — {{ $rps->mataKuliah->kurikulum->nama_kurikulum ?? '-' }}
</p>

<div class="mb-4">
    <a href="{{ route('rps.ekstrak-pdf', ['rps' => $rps, 'download' => 1]) }}"
       class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:6px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download PDF
    </a>

    <a href="{{ route('mata-kuliah.rps.index', $rps->mataKuliah) }}"
       class="btn btn-secondary">
        Kembali
    </a>
</div>

<div class="rps-document">

    {{-- COVER --}}
    <div class="rps-cover">
        <div class="rps-cover-institution">
            <div class="rps-cover-institution-name">POLITEKNIK SAWUNGGALIH AJI</div>
        </div>
        <h1 class="rps-cover-title">RENCANA PEMBELAJARAN SEMESTER (RPS)</h1>
        <div class="rps-cover-subject">
            <div class="rps-cover-subject-label">Mata Kuliah</div>
            <div class="rps-cover-subject-name">{{ $rps->mataKuliah->nama }}</div>
            <div class="rps-cover-subject-code">Kode: {{ $rps->mataKuliah->kode }}</div>
        </div>
        <div class="rps-cover-by">
            <div class="rps-cover-oleh">oleh</div>
            <div class="rps-cover-penyusun">PENYUSUN</div>
            <div class="rps-cover-penyusun-name">{{ $rps->dosen_pengembang_rps ?? $rps->dosen_pengampu }}</div>
        </div>
        <div class="rps-cover-prodi">
            Program Studi {{ $rps->mataKuliah->kurikulum->programStudi->jenjang ?? '' }} {{ $rps->mataKuliah->kurikulum->programStudi->nama_prodi ?? '' }}
        </div>
        <div class="rps-cover-info-grid">
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Program Studi</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->programStudi->nama_prodi ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Jenjang</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->programStudi->jenjang ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Kurikulum</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->nama_kurikulum ?? '-' }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Dosen Pengampu</span>
                <span class="rps-cover-info-value">{{ $rps->dosen_pengampu }}</span>
            </div>
            <div class="rps-cover-info-item">
                <span class="rps-cover-info-label">Tahun Penyusunan</span>
                <span class="rps-cover-info-value">{{ $rps->mataKuliah->kurikulum->tahun_berlaku ?? '-' }}</span>
            </div>
        </div>
        <div class="rps-cover-location">Purworejo, {{ $tanggalPenyusunan }}</div>
    </div>

    {{-- SECTION 1: INFORMASI UMUM --}}
    <div class="rps-section">
        <h2 class="rps-section-title">2. Informasi Umum</h2>
        <table class="rps-info-table">
            <tbody>
                <tr>
                    <td class="rps-info-label">Mata Kuliah (MK)</td>
                    <td class="rps-info-value">{{ $rps->mataKuliah->nama }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Kode</td>
                    <td class="rps-info-value">{{ $rps->mataKuliah->kode }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Rumpun MK (RMK)</td>
                    <td class="rps-info-value">{{ $rps->rumpun_mk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Bobot (SKS)</td>
                    <td class="rps-info-value">
                        {{ $rps->mataKuliah->total_sks }} SKS
                        ({{ $rps->mataKuliah->sks_teori }} Teori + {{ $rps->mataKuliah->sks_praktikum }} Praktik)
                    </td>
                </tr>
                <tr>
                    <td class="rps-info-label">Semester</td>
                    <td class="rps-info-value">{{ $rps->semester }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Dosen Pengampu</td>
                    <td class="rps-info-value">{{ $rps->dosen_pengampu }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Tanggal Penyusunan</td>
                    <td class="rps-info-value">{{ $rps->created_at?->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">MK yang Menjadi Prasyarat</td>
                    <td class="rps-info-value">{{ $rps->mk_prasyarat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Menjadi Prasyarat untuk MK</td>
                    <td class="rps-info-value">{{ $rps->prasyarat_untuk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Integrasi Antar MK</td>
                    <td class="rps-info-value">{{ $rps->integrasi_antar_mk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="rps-info-label">Tautan Kelas Daring</td>
                    <td class="rps-info-value">
                        @if($rps->tautan_daring)
                            <a href="{{ $rps->tautan_daring }}" target="_blank" rel="noopener">{{ $rps->tautan_daring }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="rps-info-label">Deskripsi Mata Kuliah</td>
                    <td class="rps-info-value">{{ $rps->deskripsi_mata_kuliah ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- PENGESAHAN --}}
        <div class="rps-pengesahan">
            <div class="rps-pengesahan-title">Pengesahan</div>
            <table class="rps-pengesahan-table">
                <tbody>
                    <tr>
                        <td class="rps-pengesahan-col">
                            <div class="rps-pengesahan-role">Dosen Pengembang RPS</div>
                            @if($rps->status === 'Disetujui')
                            <div class="rps-approved-stamp">
                                <div class="rps-approved-check">✓</div>
                            </div>
                            <div class="rps-approved-text">Disetujui</div>
                            @if($rps->dosen_pengembang_rps)
                            <div class="rps-pengesahan-name">{{ $rps->dosen_pengembang_rps }}</div>
                            @endif
                            @if($rps->tanggal_disetujui)
                            <div class="rps-approved-date">{{ $rps->tanggal_disetujui->format('d/m/Y') }}</div>
                            @endif
                            @else
                            <div class="rps-pengesahan-space"></div>
                            <div class="rps-pengesahan-sign">(Tanda tangan)</div>
                            <div class="rps-pengesahan-name">{{ $rps->dosen_pengembang_rps ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="rps-pengesahan-col">
                            <div class="rps-pengesahan-role">Koordinator RMK</div>
                            <div class="rps-pengesahan-space"></div>
                            <div class="rps-pengesahan-sign">(Jika ada)</div>
                            <div class="rps-pengesahan-name">{{ $rps->koordinator_rmk ?? '-' }}</div>
                        </td>
                        <td class="rps-pengesahan-col">
                            <div class="rps-pengesahan-role">Ketua Program Studi</div>
                            @if($rps->status === 'Disetujui')
                            <div class="rps-approved-stamp">
                                <div class="rps-approved-check">✓</div>
                            </div>
                            <div class="rps-approved-text">Disetujui</div>
                            @if($rps->disetujuiOleh)
                            <div class="rps-pengesahan-name">{{ $rps->disetujuiOleh->name }}</div>
                            @endif
                            @if($rps->tanggal_disetujui)
                            <div class="rps-approved-date">{{ $rps->tanggal_disetujui->format('d/m/Y') }}</div>
                            @endif
                            @else
                            <div class="rps-pengesahan-space"></div>
                            <div class="rps-pengesahan-sign">(Tanda tangan)</div>
                            <div class="rps-pengesahan-name">{{ $rps->ketua_prodi ?? '-' }}</div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 2: CPL --}}
    @if($rps->mataKuliah->cpls->count())
    <div class="rps-section">
        <h2 class="rps-section-title">CPL-Prodi yang Dibebankan kepada MK</h2>
        <p class="rps-note">
            Catatan: rumusan CPL di atas sesuai kode CPL pada matriks kurikulum Program Studi.
            Mohon disesuaikan dengan rumusan resmi dokumen CPL Program Studi.
        </p>
        <div class="rps-table-wrap">
            <table class="rps-data-table">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 120px;">Kode</th>
                        <th class="rps-th">Deskripsi CPL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rps->mataKuliah->cpls as $cpl)
                    <tr>
                        <td class="rps-td text-center">{{ $cpl->kode_cpl }}</td>
                        <td class="rps-td">{{ $cpl->deskripsi }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- SECTION 3: CPMK --}}
    @if($cpmks->count())
    <div class="rps-section">
        <h2 class="rps-section-title">Capaian Pembelajaran Mata Kuliah (CPMK)</h2>
        <div class="rps-table-wrap">
            <table class="rps-data-table">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 120px;">Kode</th>
                        <th class="rps-th">Deskripsi CPMK</th>
                        <th class="rps-th" style="width: 140px;">CPL Terkait</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cpmks as $cpmk)
                    <tr>
                        <td class="rps-td text-center">{{ $cpmk->kode_cpmk }}</td>
                        <td class="rps-td">{{ $cpmk->deskripsi }}</td>
                        <td class="rps-td">
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
    </div>
    @endif

    {{-- SECTION 4: SUB-CPMK --}}
    @if(count($subCpmkRows))
    <div class="rps-section">
        <h2 class="rps-section-title">Sub-Capaian Pembelajaran Mata Kuliah (Sub-CPMK)</h2>
        <div class="rps-table-wrap">
            <table class="rps-data-table">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 120px;">Kode</th>
                        <th class="rps-th" style="width: 180px;">CPMK Induk</th>
                        <th class="rps-th">Deskripsi Sub-CPMK</th>
                        <th class="rps-th">Indikator Ketercapaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subCpmkRows as $kode => $sub)
                    <tr>
                        <td class="rps-td text-center">{{ $kode }}</td>
                        <td class="rps-td">{{ $sub->cpmk_induk ?? '-' }}</td>
                        <td class="rps-td">{{ $sub->pengalaman_belajar ?? '-' }}</td>
                        <td class="rps-td">{{ $sub->indikator ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- SECTION 5: KORELASI CPMK TERHADAP SUB-CPMK --}}
    @if(count($subCpmkRows) && $cpmks->count())
    <div class="rps-section">
        <h2 class="rps-section-title">Korelasi CPMK terhadap Sub-CPMK</h2>
        <div class="rps-table-wrap rps-table-wide">
            <table class="rps-data-table rps-korelasi-table">
                <thead>
                    <tr>
                        <th class="rps-th">CPMK</th>
                        @foreach(array_keys($subCpmkRows) as $col)
                        <th class="rps-th text-center">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($cpmks as $cpmk)
                    <tr>
                        <td class="rps-td font-semibold">{{ $cpmk->kode_cpmk }}</td>
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
                        <td class="rps-td text-center">{{ $terkait ? '√' : '–' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @unless($hasKorelasi)
        <p class="rps-note">Catatan: korelasi ditentukan dari kolom "CPMK Induk" pada tiap pertemuan.</p>
        @endunless
    </div>
    @endif

    {{-- SECTION 6: BAHAN KAJIAN --}}
    @if($rps->mataKuliah->bahanKajians->count())
    <div class="rps-section">
        <h2 class="rps-section-title">Bahan Kajian: Materi Pembelajaran</h2>
        <div class="rps-table-wrap">
            <table class="rps-data-table">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 100px;">Kode BK</th>
                        <th class="rps-th">Bahan Kajian</th>
                        <th class="rps-th">Materi Pembelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rps->mataKuliah->bahanKajians as $bk)
                    <tr>
                        <td class="rps-td text-center">{{ $bk->kode_bk }}</td>
                        <td class="rps-td">{{ $bk->nama_bk }}</td>
                        <td class="rps-td">{{ $bk->referensi ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- SECTION 7: DAFTAR PUSTAKA --}}
    <div class="rps-section">
        <h2 class="rps-section-title">Daftar Pustaka (5 Tahun Terakhir)</h2>
        @if(count($daftarPustaka))
        <ol class="rps-olipustaka">
            @foreach($daftarPustaka as $item)
            <li>{{ $item }}</li>
            @endforeach
        </ol>
        @else
        <p class="rps-empty">Daftar pustaka belum terisi.</p>
        @endif
    </div>

    {{-- SECTION 8: RENCANA PEMBELAJARAN --}}
    <div class="rps-section">
        <h2 class="rps-section-title">3. Rencana Pembelajaran</h2>
        @if($rps->pertemuans->count())
        <div class="rps-table-wrap rps-table-wide">
            <table class="rps-data-table rps-schedule-table">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 55px;">Minggu ke-</th>
                        <th class="rps-th">Sub-CPMK</th>
                        <th class="rps-th" colspan="2">Penilaian</th>
                        <th class="rps-th" colspan="2">Metode Pembelajaran (Estimasi Waktu)</th>
                        <th class="rps-th">Materi Pembelajaran</th>
                        <th class="rps-th" style="width: 60px;">Bobot (%)</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th class="rps-th">Indikator</th>
                        <th class="rps-th">Teknik &amp; Kriteria</th>
                        <th class="rps-th">Daring (Online)</th>
                        <th class="rps-th">Luring (Offline)</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rps->pertemuans as $pertemuan)
                    <tr>
                        <td class="rps-td text-center">{{ $pertemuan->minggu }}</td>
                        <td class="rps-td">{{ $pertemuan->sub_cpmk }}</td>
                        <td class="rps-td">{{ $pertemuan->indikator ?? '-' }}</td>
                        <td class="rps-td">{{ $pertemuan->teknik_kriteria ?? '-' }}</td>
                        <td class="rps-td">{{ $pertemuan->metode_daring ?? '-' }}</td>
                        <td class="rps-td">{{ $pertemuan->metode_luring ?? $pertemuan->metode ?? '-' }}</td>
                        <td class="rps-td">{{ $pertemuan->materi }}</td>
                        <td class="rps-td text-center">{{ $pertemuan->bobot }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="rps-note">
            Sinkron: interaksi pembelajaran antara dosen dan mahasiswa pada waktu bersamaan (audio/video conference).
            Asinkron: interaksi pembelajaran fleksibel, tidak harus dalam waktu yang sama (forum diskusi, belajar mandiri/penugasan).
            Estimasi 1 SKS setara 170 menit belajar per minggu.
        </p>
        @else
        <p class="rps-empty">Belum ada data pertemuan.</p>
        @endif
    </div>

    {{-- SECTION 9: RANCANGAN TUGAS DAN LATIHAN --}}
    <div class="rps-section">
        <h2 class="rps-section-title">4. Rancangan Tugas dan Latihan</h2>
        @if($rps->tugas->count())
        <div class="rps-table-wrap rps-table-wide">
            <table class="rps-data-table rps-schedule-table" style="min-width: 1100px;">
                <thead>
                    <tr>
                        <th class="rps-th" style="width: 80px;">Minggu Ke / Topik</th>
                        <th class="rps-th">Nama Tugas</th>
                        <th class="rps-th" style="width: 120px;">Sub-CPMK</th>
                        <th class="rps-th" style="width: 150px;">Penugasan</th>
                        <th class="rps-th">Ruang Lingkup</th>
                        <th class="rps-th">Cara Pengerjaan</th>
                        <th class="rps-th" style="width: 110px;">Batas Waktu</th>
                        <th class="rps-th">Luaran Tugas yang Dihasilkan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rps->tugas as $tugas)
                    <tr>
                        <td class="rps-td text-center">{{ $tugas->minggu_topik }}</td>
                        <td class="rps-td font-semibold">{{ $tugas->nama_tugas }}</td>
                        <td class="rps-td">{{ $tugas->sub_cpmk ?? '-' }}</td>
                        <td class="rps-td">{{ $tugas->penugasan ?? '-' }}</td>
                        <td class="rps-td">{{ $tugas->ruang_lingkup ?? '-' }}</td>
                        <td class="rps-td">{{ $tugas->cara_pengerjaan ?? '-' }}</td>
                        <td class="rps-td">{{ $tugas->batas_waktu ?? '-' }}</td>
                        <td class="rps-td">{{ $tugas->luaran_tugas ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="rps-empty">Belum ada data rancangan tugas.</p>
        @endif
    </div>

    {{-- SECTION 10: RANCANGAN EVALUASI --}}
    <div class="rps-section">
        <h2 class="rps-section-title">5. Rancangan Evaluasi</h2>
        @if($rps->bentukEvaluasis->count())
        @php
            $totalBobotEval = $rps->bentukEvaluasis->sum('bobot');
        @endphp
        <div class="rps-table-wrap">
            <table class="rps-data-table">
                <thead>
                    <tr>
                        <th class="rps-th">Bentuk Evaluasi</th>
                        <th class="rps-th">Sub-CPMK</th>
                        <th class="rps-th" colspan="2">Instrumen Penilaian</th>
                        <th class="rps-th">Tagihan (Bukti)</th>
                        <th class="rps-th" style="width: 70px;">Bobot (%)</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th class="rps-th">Formatif</th>
                        <th class="rps-th">Sumatif</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rps->bentukEvaluasis as $be)
                    <tr>
                        <td class="rps-td">{{ $be->bentuk_evaluasi }}</td>
                        <td class="rps-td">{{ $be->sub_cpmk ?? '-' }}</td>
                        <td class="rps-td">{{ $be->formatif ? ($be->instrumen ?? '-') : '-' }}</td>
                        <td class="rps-td">{{ $be->sumatif ? ($be->instrumen ?? '-') : '-' }}</td>
                        <td class="rps-td">{{ $be->tagihan ?? '-' }}</td>
                        <td class="rps-td text-center">{{ $be->bobot }}</td>
                    </tr>
                    @endforeach
                    <tr class="rps-total-row">
                        <td class="rps-td font-semibold">Total</td>
                        <td class="rps-td"></td>
                        <td class="rps-td"></td>
                        <td class="rps-td"></td>
                        <td class="rps-td"></td>
                        <td class="rps-td text-center font-semibold">{{ number_format((float) $totalBobotEval, 0) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="rps-note">
            Bentuk evaluasi dapat berupa identifikasi masalah, penyusunan dokumen, presentasi, ujian tulis, penilaian keaktifan diskusi, dan bentuk lainnya.
            Instrumen asesmen formatif berupa umpan balik; instrumen asesmen sumatif dapat berupa rubrik penilaian atau borang.
        </p>
        @else
        <p class="rps-empty">Belum ada data penilaian.</p>
        @endif
    </div>

    {{-- APPROVAL --}}
    @if($rps->disetujuiOleh)
    <div class="rps-approval">
        <div class="rps-approval-label">Disetujui oleh:</div>
        <div class="rps-approval-name">{{ $rps->disetujuiOleh->name }}</div>
        @if($rps->tanggal_disetujui)
        <div class="rps-approval-date">{{ $rps->tanggal_disetujui->format('d/m/Y H:i') }}</div>
        @endif
    </div>
    @endif

</div>

@endsection