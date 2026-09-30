@extends('layouts.app')

@section('content')

<style>
@media print {
    body { background: #fff !important; color: #000 !important; }
    .no-print, header, nav, sidebar, footer, .page-header { display: none !important; }
    .card, .table-container { border: none !important; box-shadow: none !important; padding: 0 !important; }
    .data-table th, .data-table td { border: 1px solid #64748b !important; padding: 4px 6px !important; font-size: 8pt !important; }
}
</style>

<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
    <div>
        <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'presensi']) }}" class="btn btn-secondary btn-sm">Kembali ke Presensi LMS</a>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Jurnal Presensi
        </button>
    </div>
</div>

<div class="card" style="padding: 1.5rem; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <div style="text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 1rem; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0; text-transform: uppercase;">JURNAL &amp; REKAPITULASI PRESENSI PERKULIAHAN</h2>
        <p style="font-size: 0.85rem; color: #475569; margin: 0.25rem 0 0;">
            {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }} &middot; {{ $pengampu->tahunAkademik->tahun ?? '' }} ({{ ucfirst($pengampu->tahunAkademik->semester ?? '') }})
        </p>
        <p style="font-size: 0.8rem; color: #64748b; margin: 0.1rem 0 0;">
            Dosen Pengampu: {{ $pengampu->dosen->user->name ?? '-' }}
        </p>
    </div>

    <div style="margin-bottom: 1rem; font-size: 0.78rem; color: #475569; background: #f8fafc; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid #e2e8f0;">
        <strong>Ketentuan Aturan Skor Presensi:</strong> Hadir = 100% (1.0) &middot; Sakit / Izin = 50% (0.5) &middot; Alpa = 0% (0.0) &middot; <strong>Syarat Ujian (UTS/UAS):</strong> Minimal 75% Kehadiran.
    </div>

    <div class="table-container" style="overflow-x: auto;">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th rowspan="2" style="text-align: center; width: 35px;">No</th>
                    <th rowspan="2" style="width: 90px;">NIM</th>
                    <th rowspan="2">Nama Mahasiswa</th>
                    <th colspan="{{ max(1, $pertemuans->count()) }}" style="text-align: center;">Pertemuan / Sesi Perkuliahan</th>
                    <th colspan="4" style="text-align: center;">Total</th>
                    <th rowspan="2" style="text-align: center; width: 65px;">Skor (%)</th>
                    <th rowspan="2" style="text-align: center; width: 85px;">Syarat Ujian</th>
                </tr>
                <tr>
                    @forelse($pertemuans as $pertemuan)
                        <th style="text-align: center; font-size: 0.7rem; width: 30px;" title="Minggu {{ $pertemuan->minggu }}: {{ $pertemuan->materi }}">
                            P{{ $pertemuan->minggu }}
                        </th>
                    @empty
                        <th style="text-align: center; font-size: 0.7rem;">-</th>
                    @endforelse
                    <th style="text-align: center; font-size: 0.7rem; color: #059669;">H</th>
                    <th style="text-align: center; font-size: 0.7rem; color: #A16207;">S</th>
                    <th style="text-align: center; font-size: 0.7rem; color: #d97706;">I</th>
                    <th style="text-align: center; font-size: 0.7rem; color: #dc2626;">A</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswas as $mahasiswa)
                    @php
                        $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;
                        $totalSesiDibuka = $sesis->count();
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="font-weight: 600;">{{ $mahasiswa->nim }}</td>
                        <td>{{ $mahasiswa->nama }}</td>
                        @forelse($pertemuans as $pertemuan)
                            @php
                                $sesi = $sesis->get($pertemuan->id);
                                $status = $sesi ? ($sesi->absensis->firstWhere('mahasiswa_id', $mahasiswa->id)?->status) : null;
                                if ($status === 'hadir') $hadir++;
                                elseif ($status === 'sakit') $sakit++;
                                elseif ($status === 'izin') $izin++;
                                elseif ($status === 'alpa') $alpa++;
                            @endphp
                            <td style="text-align: center; font-size: 0.75rem; font-weight: 700;">
                                @if($status === 'hadir')
                                    <span style="color: #059669;">H</span>
                                @elseif($status === 'sakit')
                                    <span style="color: #A16207;">S</span>
                                @elseif($status === 'izin')
                                    <span style="color: #d97706;">I</span>
                                @elseif($status === 'alpa')
                                    <span style="color: #dc2626;">A</span>
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                        @empty
                            <td style="text-align: center;">-</td>
                        @endforelse
                        @php
                            $skorPoints = $hadir + (0.5 * ($sakit + $izin));
                            $persen = $totalSesiDibuka > 0 ? round(($skorPoints / $totalSesiDibuka) * 100, 1) : null;
                        @endphp
                        <td style="text-align: center; font-weight: 600; color: #059669;">{{ $hadir }}</td>
                        <td style="text-align: center; font-weight: 600; color: #A16207;">{{ $sakit }}</td>
                        <td style="text-align: center; font-weight: 600; color: #d97706;">{{ $izin }}</td>
                        <td style="text-align: center; font-weight: 600; color: #dc2626;">{{ $alpa }}</td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $persen !== null ? $persen.'%' : '-' }}
                        </td>
                        <td style="text-align: center;">
                            @if($persen === null)
                                <span style="color: #94a3b8; font-size: 0.7rem;">-</span>
                            @elseif($persen >= 75)
                                <span style="display: inline-block; padding: 0.15rem 0.4rem; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Lulus (≥75%)</span>
                            @else
                                <span style="display: inline-block; padding: 0.15rem 0.4rem; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Kritis (&lt;75%)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 9 + max(1, $pertemuans->count()) }}" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            Belum ada mahasiswa di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 2rem; display: flex; justify-content: space-between; font-size: 0.8rem; color: #334155;">
        <div>
            Dicetak otomatis dari Sistem LMS Eduva pada: {{ now()->format('d M Y H:i') }} WIB
        </div>
        <div style="text-align: center; width: 200px;">
            Dosen Pengampu,<br><br><br><br>
            <strong><u>{{ $pengampu->dosen->user->name ?? '....................' }}</u></strong><br>
            NIDN: {{ $pengampu->dosen->nidn ?? '-' }}
        </div>
    </div>
</div>

@endsection
