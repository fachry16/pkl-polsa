@extends('layouts.app')

@section('content')

<div class="page-header">
    Rekap Nilai Perkuliahan &amp; LMS
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
        <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Hitung Ulang Nilai</button>
        </form>
    </div>
</div>

{{-- Panel Informasi Formula Penilaian & Konversi Abjad --}}
<div x-data="{ openGuide: true }" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <div @click="openGuide = !openGuide" style="padding: 0.9rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: #e0e7ff; color: #4338ca; font-size: 0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </span>
            <div>
                <strong style="font-size: 0.88rem; color: #1e293b;">Panduan Formula Penilaian &amp; Konversi Abjad Nilai Mutu (KRS/KHS)</strong>
                <div style="font-size: 0.72rem; color: #64748b;">Klik untuk melihat/sembunyikan penjelasan rumus OBE, bobot RPS, dan standar abjad</div>
            </div>
        </div>
        <svg :style="openGuide ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s; color: #64748b;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </div>

    <div x-show="openGuide" style="padding: 1.25rem; font-size: 0.82rem; color: #334155;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            {{-- Kolom 1: Formula Perhitungan --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    1. Formula Nilai Akhir (OBE)
                </div>
                <div style="font-size: 0.76rem; color: #475569; line-height: 1.55;">
                    <p style="margin: 0 0 0.4rem;">
                        <strong>Nilai Tugas:</strong> Rata-rata nilai submisi tugas dikali bobot tugas masing-masing:
                        <br><code>Nilai Tugas = &Sigma;(Nilai &times; Bobot Tugas) &divide; &Sigma;Bobot Tugas</code>
                    </p>
                    <p style="margin: 0 0 0.4rem;">
                        <strong>Nilai Akhir:</strong> Rata-rata tertimbang komponen aktif dari RPS:
                        <br><code>Nilai Akhir = &Sigma;(Nilai Komponen &times; Bobot RPS) &divide; &Sigma;Bobot Terisi</code>
                    </p>
                    <div style="background: #eff6ff; border-left: 3px solid #3b82f6; padding: 0.4rem 0.6rem; border-radius: 4px; font-size: 0.72rem; color: #1e40af; margin-top: 0.4rem;">
                        💡 <em>Normalisasi OBE:</em> Jika UAS belum berlangsung, pembagi disesuaikan hanya dengan bobot yang sudah terisi sehingga nilai sementara mahasiswa tetap adil dan proporsional.
                    </div>
                </div>
            </div>

            {{-- Kolom 2: Bobot RPS & Presensi --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    2. Bobot RPS &amp; Aspek Keaktifan
                </div>
                <div style="font-size: 0.76rem; color: #475569; line-height: 1.55;">
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Bobot RPS Mata Kuliah Ini:</strong>
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.3rem;">
                            @foreach($bobot as $komp => $persen)
                                @if($persen > 0)
                                    <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">
                                        {{ ucfirst($komp) }}: {{ $persen }}%
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <p style="margin: 0 0 0.35rem;">
                        <strong>Presensi:</strong> Berfungsi memantau kehadiran 16 sesi (rekam jejak kedisiplinan dan monitoring kelas).
                    </p>
                    <p style="margin: 0;">
                        <strong>Keaktifan &amp; Sikap (Sopan-Santun):</strong> Dosen dapat mengalokasikan penilaian keaktifan diskusi/partisipasi kelas ke dalam slot <code>Quiz</code> atau <code>Project/Tugas</code>.
                    </p>
                </div>
            </div>

            {{-- Kolom 3: Tabel Konversi Abjad Mutu --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    3. Standar Abjad &amp; Bobot Mutu (KRS/KHS)
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.73rem; text-align: center;">
                        <thead>
                            <tr style="background: #e2e8f0; color: #334155;">
                                <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Rentang</th>
                                <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Huruf</th>
                                <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Bobot</th>
                                <th style="padding: 0.25rem 0.4rem; border: 1px solid #cbd5e1;">Predikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">&ge; 80.00</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #059669;">A</td><td style="border: 1px solid #e2e8f0;">4.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Sangat Baik</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">75.00 - 79.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">B+</td><td style="border: 1px solid #e2e8f0;">3.50</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Antara A &amp; B</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">70.00 - 74.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">B</td><td style="border: 1px solid #e2e8f0;">3.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Baik</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">65.00 - 69.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ca8a04;">C+</td><td style="border: 1px solid #e2e8f0;">2.50</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Antara B &amp; C</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">60.00 - 64.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ca8a04;">C</td><td style="border: 1px solid #e2e8f0;">2.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Cukup (Lulus)</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">50.00 - 59.99</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #ea580c;">D</td><td style="border: 1px solid #e2e8f0;">1.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Kurang (Remedi)</td></tr>
                            <tr><td style="border: 1px solid #e2e8f0; padding: 0.2rem;">&lt; 50.00</td><td style="border: 1px solid #e2e8f0; font-weight: 700; color: #dc2626;">E</td><td style="border: 1px solid #e2e8f0;">0.00</td><td style="border: 1px solid #e2e8f0; text-align: left; padding-left: 0.4rem;">Tidak Lulus</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                @foreach($tugasList as $tugas)
                    <th style="text-align: center; font-size: 0.7rem;">{{ Str::limit($tugas->judul, 15) }}</th>
                @endforeach
                <th style="text-align: center; font-weight: 700;">Nilai Tugas</th>
                @foreach($bobot as $komponen => $persen)
                    @if($komponen !== 'tugas' && $persen > 0)
                        <th style="text-align: center; font-size: 0.7rem;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small></th>
                    @endif
                @endforeach
                <th style="text-align: center; font-weight: 700; background: #f8fafc;">Nilai Angka</th>
                <th style="text-align: center; font-weight: 700; background: #f8fafc;">Huruf Mutu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mahasiswa)
                @php
                    $nilaiTugas = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'tugas')?->nilai;
                    $nilaiAkhir = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    @foreach($tugasList as $tugas)
                        @php
                            $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                            $nilaiSub = $submission?->nilai;
                        @endphp
                        <td style="text-align: center;">
                            @if($nilaiSub !== null)
                                <span style="font-weight: 600; color: {{ $nilaiSub >= 60 ? '#059669' : '#dc2626' }};">{{ $nilaiSub }}</span>
                            @elseif($submission)
                                <span style="color: #d97706; font-size: 0.75rem;">Blm Dinilai</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: 700;">
                        {{ $nilaiTugas !== null ? number_format($nilaiTugas, 2) : '-' }}
                    </td>
                    @foreach($bobot as $komponen => $persen)
                        @if($komponen !== 'tugas' && $persen > 0)
                            @php
                                $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                            @endphp
                            <td style="text-align: center;">
                                {{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2) : '-' }}
                            </td>
                        @endif
                    @endforeach
                    <td style="text-align: center; font-weight: 700; background: #f8fafc;">
                        {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}
                    </td>
                    <td style="text-align: center; background: #f8fafc;">
                        @if($nilaiAkhir !== null)
                            @php
                                $huruf = konversiNilaiHuruf($nilaiAkhir);
                                $bobotM = konversiBobotMutu($nilaiAkhir);
                                $badgeStyle = match($huruf) {
                                    'A' => 'background: #ecfdf5; color: #059669; border-color: #a7f3d0;',
                                    'B+', 'B' => 'background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;',
                                    'C+', 'C' => 'background: #fefce8; color: #a16207; border-color: #fde047;',
                                    'D' => 'background: #fff7ed; color: #c2410c; border-color: #fdba74;',
                                    default => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                                };
                            @endphp
                            <span style="{{ $badgeStyle }} border-width: 1px; border-style: solid; padding: 0.15rem 0.55rem; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <span>{{ $huruf }}</span>
                                <span style="font-size: 0.7rem; opacity: 0.85;">({{ number_format($bobotM, 2) }})</span>
                            </span>
                        @else
                            <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + $tugasList->count() + collect($bobot)->except('tugas')->filter()->count() }}" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($mahasiswas->isNotEmpty())
<div class="card" style="margin-top: 1.5rem;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
        Input Nilai Komponen (Quiz / UTS / UAS / Praktikum / Project)
    </div>
    <form action="{{ route('lms.tugas.komponen', $pengampu->id) }}" method="POST">
        @csrf
        <div class="table-container" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($bobot as $komponen => $persen)
                            @if($komponen !== 'tugas' && $persen > 0)
                                <th style="text-align: center;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small></th>
                            @endif
                        @endforeach
                        <th style="text-align: center; font-weight: 700;">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mahasiswa)
                        @php
                            $nilaiAkhirForm = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $mahasiswa->nim }}</td>
                            <td>{{ $mahasiswa->nama }}</td>
                            @foreach($bobot as $komponen => $persen)
                                @if($komponen !== 'tugas' && $persen > 0)
                                    @php
                                        $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                                    @endphp
                                    <td style="text-align: center;">
                                        <input type="number" name="nilai[{{ $mahasiswa->id }}][{{ $komponen }}]"
                                               value="{{ $nilaiKomponen !== null ? $nilaiKomponen : '' }}"
                                               min="0" max="100" step="0.01"
                                               style="width: 80px; text-align: center;">
                                    </td>
                                @endif
                            @endforeach
                            <td style="text-align: center; font-weight: 700;">
                                {{ $nilaiAkhirForm !== null ? number_format($nilaiAkhirForm, 2) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 1rem 1.25rem; text-align: right;">
            <button type="submit" class="btn btn-primary btn-sm">Simpan Nilai Komponen</button>
        </div>
    </form>
</div>
@endif

@endsection
