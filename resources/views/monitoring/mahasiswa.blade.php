@extends('layouts.app')

@section('content')

<h1 class="page-header">Monitoring Mahasiswa</h1>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<form method="GET" class="filter-card">
    <div class="filter-group">
        <label class="filter-label">Program Studi</label>
        <select name="program_studi_id" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($programStudis as $prodi)
                <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                    {{ $prodi->nama_prodi }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Angkatan</label>
        <select name="angkatan" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach($angkatans as $thn)
                <option value="{{ $thn }}" {{ request('angkatan') == $thn ? 'selected' : '' }}>
                    {{ $thn }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Status</label>
        <select name="status" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach(['Aktif', 'Cuti', 'Lulus', 'DO'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Program / Kelas</label>
        <select name="jenis_kelas" class="form-select filter-select">
            <option value="">Semua</option>
            <option value="Reguler" {{ request('jenis_kelas') == 'Reguler' ? 'selected' : '' }}>Reguler (Kelas A)</option>
            <option value="Karyawan" {{ request('jenis_kelas') == 'Karyawan' ? 'selected' : '' }}>Karyawan (Kelas B)</option>
        </select>
    </div>

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

    <div class="filter-actions">
        <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Terapkan Filter</button>
        <a href="{{ route('monitoring.mahasiswa') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
    </div>
</form>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Program</th>
                <th>Angkatan</th>
                <th>Semester</th>
                <th>Tahun Akademik</th>
                <th>Status</th>
                <th>Akun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $index => $mahasiswa)
                @php
                    $semesterAktif = $mahasiswa->semesterMahasiswas->sortByDesc('id')->first();
                @endphp
                <tr>
                    <td>{{ $mahasiswas->firstItem() + $index }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    <td>{{ $mahasiswa->programStudi->nama_prodi }}</td>
                    <td>
                        @if(($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan')
                            <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Karyawan</span>
                        @else
                            <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Reguler</span>
                        @endif
                    </td>
                    <td>{{ $mahasiswa->angkatan }}</td>
                    <td>{{ $semesterAktif?->semester ?? '-' }}</td>
                    <td>
                        {{ $semesterAktif?->tahunAkademik?->tahun ?? '-' }}
                        {{ $semesterAktif?->tahunAkademik?->semester ?? '' }}
                    </td>
                    <td>
                        @if($mahasiswa->status === 'Aktif')
                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                        @elseif($mahasiswa->status === 'Cuti')
                            <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Cuti</span>
                        @elseif($mahasiswa->status === 'Lulus')
                            <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Lulus</span>
                        @else
                            <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">DO</span>
                        @endif
                    </td>
                    <td>
                        @if($mahasiswa->user)
                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                            <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.15rem;">{{ $mahasiswa->user->email }}</div>
                        @else
                            <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Belum</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 1.5rem; color: #6b7280;">
                        Data mahasiswa belum tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">
    {{ $mahasiswas->links() }}
</div>

@endsection
