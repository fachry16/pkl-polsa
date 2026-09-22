@extends('layouts.app')

@section('content')

    <h1 class="page-header">Transkrip Mahasiswa</h1>

    <div class="card" style="padding: 1rem; margin-bottom: 1.25rem;">
        <form method="GET"
              action="{{ route('transkrip.saya') }}"
              style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">

            <div class="filter-group">
                <label class="filter-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select filter-select">
                    <option value="">Semua</option>
                    @foreach($tahunAkademiks as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun }} {{ $ta->semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Semester Akademik</label>
                <select name="semester_akademik" class="form-select filter-select">
                    <option value="">Semua</option>
                    @foreach($semesterAkademiks as $sm)
                        <option value="{{ $sm }}" {{ request('semester_akademik') === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Cari Kode / Nama MK</label>
                <input type="text" name="cari" value="{{ request('cari') }}" class="form-input filter-select" placeholder="Contoh: MATEMATIKA">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.7rem; font-size: 0.7rem;">Terapkan Filter</button>
                <a href="{{ route('transkrip.saya') }}" class="btn btn-secondary" style="padding: 0.2rem 0.7rem; font-size: 0.7rem;">Reset</a>
            </div>
        </form>
    </div>

    @if($mahasiswa)
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
                <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">{{ $mahasiswa->nama }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">
                    {{ $mahasiswa->nim }}@if($mahasiswa->programStudi) · {{ $mahasiswa->programStudi->nama_prodi }}@endif
                    · Angkatan {{ $mahasiswa->angkatan ?? '-' }}
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak
                </button>
            </div>
        </div>

        @if($byTa && count($byTa) > 0)
            <div class="card" style="margin-bottom: 1.25rem;">
                <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Rekap Nilai per Tahun Akademik</div>
                    <span style="font-size: 0.7rem; color: #64748b;">Berlaku untuk MK berstatus Sudah Sync</span>
                </div>

                <div class="table-container" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-bottom: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tahun Akademik</th>
                                <th style="text-align: center;">Jumlah MK</th>
                                <th style="text-align: right;">Total SKS</th>
                                <th style="text-align: right;">Total Nilai (SKS × Indeks)</th>
                                <th style="text-align: right;">IPS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byTa as $tahun)
                                <tr>
                                    <td style="font-weight: 600; color: #1e293b;">{{ $tahun['label'] }}</td>
                                    <td style="text-align: center;">{{ $tahun['jumlah_mk'] }}</td>
                                    <td style="text-align: right;">{{ number_format($tahun['total_sks'], 2) }}</td>
                                    <td style="text-align: right;">{{ number_format($tahun['total_nilai'], 2) }}</td>
                                    <td style="text-align: right;">
                                        <span style="display: inline-block; background: #FFF8E0; color: #B8860B; border: 1px solid #FFE88F; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.72rem; font-weight: 700;">
                                            {{ number_format($tahun['ips'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @include('transkrip._tabel', ['rows' => $rowsPage, 'summary' => $summary])

        @if(count($rowsPage))
            {{ $rowsPage->links() }}
        @endif
    @endif

@endsection