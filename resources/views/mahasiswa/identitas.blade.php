@extends('layouts.app')

@php
    $semesterAktif = $mahasiswa->semesterMahasiswas->sortByDesc('id')->first();
@endphp

@section('content')
    <h1 class="page-header">Identitas — {{ $mahasiswa->nama }}</h1>

    <div x-data="{ showDetail: false }">

        <div class="card" style="margin-bottom: 1.25rem;">
            <div style="padding: 1.1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Informasi Mahasiswa</div>
            <div style="padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">NIM</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->nim }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Nama Lengkap</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->nama }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Program Studi</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->programStudi?->nama_prodi ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Angkatan</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $mahasiswa->angkatan }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Semester</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">{{ $semesterAktif?->semester ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Tahun Akademik</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem;">
                        {{ $semesterAktif?->tahunAkademik?->tahun ?? '-' }} {{ $semesterAktif?->tahunAkademik?->semester ?? '' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Program</div>
                    <div style="margin-top: 0.25rem;">
                        @if(($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan')
                            <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Karyawan</span>
                        @else
                            <span style="background: #FFF8E0; color: #B8860B; border: 1px solid #FFE88F; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Reguler</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.25rem; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; gap: 2.5rem; flex-wrap: wrap; align-items: center;">
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">IPK Kumulatif</div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">{{ $ipk !== null ? number_format($ipk, 2) : '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Total SKS</div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">{{ $totalSks }}</div>
                </div>
                <div style="max-width: 340px; min-width: 220px;">
                    <div style="font-size: 0.72rem; color: #64748b; line-height: 1.5;">
                        Rekap nilai di bawah bersifat informatif dari Eduva.
                    </div>
                </div>
            </div>
            <button type="button" @click="showDetail = !showDetail"
                    class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; flex-shrink: 0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <span x-text="showDetail ? 'Tutup Detail' : 'Lihat Detail'"></span>
            </button>
        </div>

        <template x-if="showDetail">
            <div>
                <div style="display: flex; align-items: center; gap: 0.6rem; margin: 0 0 0.5rem;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 1rem;">KHS</span>
                    <span style="font-size: 0.72rem; color: #64748b;">Hasil studi per semester</span>
                </div>

                @forelse($semesters as $semester => $rows)
                    <div class="card" style="margin-bottom: 1.25rem;">
                        <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                            {{ $semester }}
                        </div>
                        <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Kelas</th>
                                        <th>Nilai Komponen</th>
                                        <th>Nilai Akhir</th>
                                        <th>Huruf</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #1e293b;">{{ $row['pengampu']->mataKuliah?->nama ?? '-' }}</div>
                                                <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.1rem;">{{ $row['pengampu']->mataKuliah?->kode ?? '-' }}</div>
                                            </td>
                                            <td>{{ $row['pengampu']->nama_dosen }}</td>
                                            <td>{{ $row['pengampu']->nama_kelas }}</td>
                                            <td>
                                                @forelse($row['komponen'] as $komponen => $nilai)
                                                    <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; border-radius: 999px; padding: 0.12rem 0.55rem; font-size: 0.68rem; font-weight: 600; margin: 0.1rem 0.15rem 0.1rem 0;">
                                                        {{ ucfirst($komponen) }}: {{ number_format($nilai, 2) }}
                                                    </span>
                                                @empty
                                                    <span style="font-size: 0.75rem; color: #94a3b8;">Belum dinilai</span>
                                                @endforelse
                                            </td>
                                            <td style="font-weight: 600;">
                                                {{ $row['akhir'] !== null ? number_format($row['akhir'], 2) : '-' }}
                                            </td>
                                            <td>
                                                @if($row['huruf'])
                                                    @php
                                                        $colorMatch = match($row['huruf']) {
                                                            'A', 'B+' => ['bg' => '#ecfdf5', 'fg' => '#059669', 'bd' => '#a7f3d0'],
                                                            'B', 'C+' => ['bg' => '#FFF8E0', 'fg' => '#B8860B', 'bd' => '#FFE88F'],
                                                            default    => ['bg' => '#fef3c7', 'fg' => '#b45309', 'bd' => '#fde68a'],
                                                        };
                                                    @endphp
                                                    <span style="display: inline-block; background: {{ $colorMatch['bg'] }}; color: {{ $colorMatch['fg'] }}; border: 1px solid {{ $colorMatch['bd'] }}; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.72rem; font-weight: 700;">
                                                        {{ $row['huruf'] }}
                                                    </span>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="card" style="padding: 2.5rem; text-align: center;">
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data nilai</div>
                        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Nilai akan muncul setelah proses penilaian dilakukan.</div>
                    </div>
                @endforelse

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.75rem; color: #92400e; line-height: 1.5;">
                    Rekap nilai di atas hanya sebagai informasi dari Eduva. Nilai asli dan resmi mahasiswa ditetapkan serta dikeluarkan oleh bagian akademik.
                </div>

                <div style="display: flex; align-items: center; gap: 0.6rem; margin: 0 0 0.5rem;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 1rem;">Transkrip</span>
                    <span style="font-size: 0.72rem; color: #64748b;">Rekap prestasi akademik seluruh semester</span>
                </div>

                @forelse($perSemester as $semester => $mks)
                    @php
                        $rowsSem = $mks['rows'];
                        $totalSksSem = $mks['total_sks'];
                        $ips = $mks['ips'];
                    @endphp
                    <div class="card" style="margin-bottom: 1.25rem;">
                        <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem;">
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">{{ $semester }}</div>
                            <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.75rem;">
                                <span style="color: #64748b; font-weight: 600;">SKS: <span style="color: #1e293b;">{{ $totalSksSem }}</span></span>
                                <span style="color: #64748b; font-weight: 600;">IPS: <span style="color: #1e293b; font-weight: 800;">{{ $ips !== null ? number_format($ips, 2) : '-' }}</span></span>
                            </div>
                        </div>
                        <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Mata Kuliah</th>
                                        <th style="text-align:center;">SKS</th>
                                        <th style="text-align:right;">Nilai</th>
                                        <th style="text-align:center;">Huruf</th>
                                        <th style="text-align:right;">Bobot Mutu</th>
                                        <th style="text-align:right;">SKS × Mutu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rowsSem as $index => $mk)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td style="color: #64748b;">{{ $mk['mata_kuliah']->kode ?? '-' }}</td>
                                            <td style="font-weight: 600; color: #1e293b;">{{ $mk['mata_kuliah']->nama ?? '-' }}</td>
                                            <td style="text-align:center;">{{ $mk['sks'] }}</td>
                                            <td style="text-align:right;">{{ number_format($mk['nilai'], 2) }}</td>
                                            <td style="text-align:center;">
                                                @php
                                                    $colorMatch = match($mk['huruf'] ?? '') {
                                                        'A', 'B+' => ['bg' => '#ecfdf5', 'fg' => '#059669', 'bd' => '#a7f3d0'],
                                                        'B', 'C+' => ['bg' => '#FFF8E0', 'fg' => '#B8860B', 'bd' => '#FFE88F'],
                                                        default    => ['bg' => '#fef3c7', 'fg' => '#b45309', 'bd' => '#fde68a'],
                                                    };
                                                @endphp
                                                <span style="display:inline-block; background:{{ $colorMatch['bg'] }}; color:{{ $colorMatch['fg'] }}; border:1px solid {{ $colorMatch['bd'] }}; border-radius:999px; padding:0.15rem 0.6rem; font-size:0.72rem; font-weight:700;">
                                                    {{ $mk['huruf'] ?? '-' }}
                                                </span>
                                            </td>
                                            <td style="text-align:right;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['bobot_mutu'], 2) : '-' }}</td>
                                            <td style="text-align:right; font-weight: 600;">{{ $mk['bobot_mutu'] !== null ? number_format($mk['sks'] * $mk['bobot_mutu'], 2) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="card" style="padding: 2.5rem; text-align: center;">
                        <div style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Belum ada data transkrip</div>
                        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.3rem;">Transkrip akan terisi setelah penilaian akhir proses pembelajaran dilakukan.</div>
                    </div>
                @endforelse

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 0.85rem 1rem; font-size: 0.75rem; color: #92400e; line-height: 1.5;">
                    Data transkrip di atas merupakan rekap sementara dari Eduva. Transkrip akademik resmi dikeluarkan oleh bagian akademik.
                </div>
            </div>
        </template>

    </div>
@endsection