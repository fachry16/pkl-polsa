@extends('layouts.app')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; gap: 0.5rem; flex-wrap: wrap;">
    <div>
        <h1 class="page-header" style="margin: 0;">Persetujuan &amp; Verifikasi KHS</h1>
        <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: #64748b;">
            Validasi dan publikasikan Kartu Hasil Studi (KHS) mahasiswa program studi sebelum dapat diakses secara resmi.
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('khs.cetak-pilih') }}" class="btn btn-secondary">
            Cari &amp; Cetak KHS
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Filter Form --}}
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem; background: #f8fafc; border: 1px solid #e2e8f0;">
    <form method="GET" action="{{ route('khs.index') }}" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        @if(!auth()->user()->isKaprodi())
            <div style="flex: 1; min-width: 220px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; display: block;">Program Studi</label>
                <select name="program_studi_id" class="form-select" onchange="this.form.submit()">
                    @foreach($programStudis as $prodi)
                        <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div style="flex: 1; min-width: 220px;">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; display: block;">Tahun Akademik / Semester</label>
            <select name="tahun_akademik_id" class="form-select" onchange="this.form.submit()">
                @foreach($tahunAkademiks as $tahun)
                    <option value="{{ $tahun->id }}" {{ $selectedTahunId == $tahun->id ? 'selected' : '' }}>
                        {{ $tahun->tahun }} {{ ucfirst($tahun->semester) }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">
                Tampilkan Mahasiswa
            </button>
        </div>
    </form>
</div>

@if(!empty($mahasiswas))
    @php
        $totalMhs = count($mahasiswas);
        $totalApproved = collect($mahasiswas)->where('is_disetujui', true)->count();
        $totalPending = $totalMhs - $totalApproved;
    @endphp

    {{-- KPI Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #4f46e5;">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Mahasiswa</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #1e1b4b; margin-top: 0.25rem;">{{ $totalMhs }}</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #10b981;">
            <div style="font-size: 0.75rem; color: #047857; font-weight: 600; text-transform: uppercase;">KHS Disetujui</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #065f46; margin-top: 0.25rem;">{{ $totalApproved }}</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.75rem; color: #b45309; font-weight: 600; text-transform: uppercase;">Menunggu Persetujuan</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #92400e; margin-top: 0.25rem;">{{ $totalPending }}</div>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 0.5rem; flex-wrap: wrap;">
        <div style="font-size: 0.9rem; font-weight: 600; color: #1e293b;">
            Daftar Mahasiswa &amp; Status KHS ({{ $tahunAkademik ? $tahunAkademik->tahun.' '.ucfirst($tahunAkademik->semester) : '-' }})
        </div>
        @if($totalPending > 0 && (auth()->user()->isAdmin() || auth()->user()->isKaprodi()))
            <form action="{{ route('khs.approve-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui dan mempublikasikan seluruh KHS mahasiswa di prodi ini?');">
                @csrf
                <input type="hidden" name="program_studi_id" value="{{ $selectedProdiId }}">
                <input type="hidden" name="tahun_akademik_id" value="{{ $selectedTahunId }}">
                <button type="submit" class="btn btn-primary" style="background: #059669; border-color: #059669; font-size: 0.8rem; padding: 0.4rem 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Setujui Semua ({{ $totalPending }} Mahasiswa)
                </button>
            </form>
        @endif
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th style="width: 110px;">NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th style="width: 90px; text-align: center;">Kelas</th>
                    <th style="width: 70px; text-align: center;">SKS</th>
                    <th style="width: 75px; text-align: center;">IPS</th>
                    <th style="width: 150px; text-align: center;">Status KHS</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswas as $item)
                    @php
                        $mhs = $item['mahasiswa'];
                        $isApproved = $item['is_disetujui'];
                        $approval = $item['approval'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td style="font-family: monospace; font-weight: 600;">{{ $mhs->nim }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $mhs->nama }}</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">Angkatan {{ $mhs->angkatan }} &bull; {{ $item['kelas_count'] }} Mata Kuliah</div>
                        </td>
                        <td class="text-center">
                            @if(($mhs->jenis_kelas ?? 'reguler') === 'karyawan')
                                <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">Karyawan</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">Reguler</span>
                            @endif
                        </td>
                        <td class="text-center font-semibold">{{ $item['total_sks'] }}</td>
                        <td class="text-center font-bold" style="color: #1e40af; font-size: 0.9rem;">
                            {{ $item['ips'] }}
                        </td>
                        <td class="text-center">
                            @if($isApproved)
                                <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Disetujui
                                </span>
                                <div style="font-size: 0.68rem; color: #64748b; margin-top: 0.2rem;">
                                    {{ $approval->approved_at ? $approval->approved_at->format('d/m/Y H:i') : '' }}
                                </div>
                            @else
                                <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 0.35rem; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('khs.cetak', [$mhs->id, 'tahun_akademik_id' => $selectedTahunId]) }}" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;" title="Lihat Rincian KHS">
                                    Lihat KHS
                                </a>

                                @if(auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                                    @if(!$isApproved)
                                        <form action="{{ route('khs.approve', [$mhs->id, $selectedTahunId]) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: #10b981; color: #fff; font-size: 0.72rem; padding: 0.25rem 0.5rem; font-weight: 600;" title="Setujui dan Publikasikan KHS">
                                                Setujui
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('khs.unapprove', [$mhs->id, $selectedTahunId]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Batalkan persetujuan KHS mahasiswa ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.5rem; font-weight: 500;" title="Batalkan Persetujuan">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem; color: #64748b;">
        Tidak ada data mahasiswa ditemukan untuk program studi dan tahun akademik yang dipilih.
    </div>
@endif

@endsection
