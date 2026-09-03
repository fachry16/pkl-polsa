@extends('layouts.app')

@section('content')

    <h1 class="page-header">
        Data Mahasiswa
    </h1>

<div x-data="{ showImportModal: false }">
    @unless(auth()->user()->isDirektur())
    <div class="mb-4" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
            + Tambah Mahasiswa
        </a>
        <button type="button" @click="showImportModal = true" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Import Data Excel / CSV
        </button>
    </div>

    {{-- Import Modal Pop-up --}}
    <div x-show="showImportModal" x-cloak
         style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px);"
         @keydown.escape.window="showImportModal = false">
        
        <div @click.outside="showImportModal = false"
             style="background: #ffffff; border-radius: 14px; max-width: 540px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; border: 1px solid #e2e8f0;">
            
            {{-- Modal Header --}}
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Import Data Master Mahasiswa</h3>
                        <p style="margin: 0; font-size: 0.75rem; color: #64748b;">Upload file CSV/Excel sesuai format template baku.</p>
                    </div>
                </div>
                <button type="button" @click="showImportModal = false" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; padding: 0.25rem; border-radius: 6px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">&times;</button>
            </div>

            <form action="{{ route('mahasiswa.import') }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
                @csrf
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    {{-- Langkah 1: Download Template --}}
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1rem;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #166534;">Langkah 1: Unduh Format Template</div>
                                <div style="font-size: 0.75rem; color: #15803d; margin-top: 0.2rem;">
                                    Gunakan template resmi agar kolom data sesuai dan dapat diproses sistem.
                                </div>
                            </div>
                            <a href="{{ route('mahasiswa.template-import') }}" class="btn btn-sm" style="background: #16a34a; color: #fff; text-decoration: none; font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download Template
                            </a>
                        </div>
                    </div>

                    {{-- Panduan Kolom --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem;">
                        <div style="font-weight: 700; font-size: 0.8rem; color: #334155; margin-bottom: 0.35rem;">Struktur Kolom Template:</div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.74rem; color: #64748b; line-height: 1.6;">
                            <li><code>nim</code>: Nomor Induk Mahasiswa unik (juga sebagai password login awal).</li>
                            <li><code>nama</code>: Nama lengkap mahasiswa.</li>
                            <li><code>kode_prodi</code>: Kode prodi mahasiswa (contoh: <strong>TRPL</strong>, <strong>TI</strong>, <strong>AK</strong>, <strong>AB</strong>, <strong>BD</strong>).</li>
                            <li><code>angkatan</code>: Tahun angkatan 4 digit (contoh: <strong>2024</strong>).</li>
                            <li><code>semester</code>: Semester awal (angka 1-14, default: <strong>1</strong>).</li>
                            <li><code>status</code>: Opsional (default: <em>Aktif</em>).</li>
                            <li><code>jenis_kelas</code>: <strong>Reguler</strong> (Kelas A / Pagi) atau <strong>Karyawan</strong> (Kelas B / Sore-Malam). Default: <em>Reguler</em>.</li>
                        </ul>
                        <div style="margin-top: 0.5rem; font-size: 0.72rem; color: #059669; font-weight: 600;">
                            💡 Akun login mahasiswa otomatis dibuat dengan email: <code>[nim]@polsa.ac.id</code>
                        </div>
                    </div>

                    {{-- Langkah 2: Upload File --}}
                    <div>
                        <label class="form-label" style="font-weight: 600; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.4rem; display: block;">
                            Langkah 2: Pilih File yang Sudah Diisi <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="form-input" style="width: 100%; font-size: 0.82rem; padding: 0.45rem;">
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem;">Format: CSV (Maksimal 5 MB). Pastikan kolom header tidak diubah.</div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 0.6rem;">
                    <button type="button" @click="showImportModal = false" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.9rem;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.45rem 1rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Mulai Import Data
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endunless
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@if(session('import_warnings'))
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.8rem; color: #92400e;">
        <div style="font-weight: 700; margin-bottom: 0.35rem;">Catatan Baris yang Dilewati:</div>
        <ul style="margin: 0; padding-left: 1.2rem; line-height: 1.5;">
            @foreach(session('import_warnings') as $warn)
                <li>{{ $warn }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
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
                <th>Akun</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($mahasiswas as $index => $mahasiswa)

            @php
                $semesterAktif = $mahasiswa->semesterMahasiswas->sortByDesc('id')->first();
            @endphp

            <tr>

                <td>
                    {{ $mahasiswas->firstItem() + $index }}
                </td>

                <td>
                    {{ $mahasiswa->nim }}
                </td>

                <td>
                    {{ $mahasiswa->nama }}
                </td>

                <td>
                    {{ $mahasiswa->programStudi->nama_prodi }}
                </td>

                <td>
                    @if(($mahasiswa->jenis_kelas ?? 'Reguler') === 'Karyawan')
                        <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Karyawan</span>
                    @else
                        <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.72rem; font-weight: 700;">Reguler</span>
                    @endif
                </td>

                <td>
                    {{ $mahasiswa->angkatan }}
                </td>

                <td>
                    {{ $semesterAktif?->semester ?? '-' }}
                </td>

                <td>
                    {{ $semesterAktif?->tahunAkademik?->tahun ?? '-' }}
                    {{ $semesterAktif?->tahunAkademik?->semester ?? '' }}
                </td>

                <td>
                    @if($mahasiswa->user)
                        <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.15rem;">{{ $mahasiswa->user->email }}</div>
                    @else
                        <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 600;">Belum</span>
                    @endif
                </td>

                <td>

                    <div class="btn-group">
                        @unless(auth()->user()->isDirektur())
                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('mahasiswa.destroy', $mahasiswa->id) }}"
                            method="DELETE"
                            title="Hapus Mahasiswa"
                            message="Hapus Mahasiswa?"
                            sub-message="Data ini akan hilang selamanya."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endunless
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="9"
                    class="text-center">

                    Data mahasiswa belum tersedia.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-5">

    {{ $mahasiswas->withQueryString()->links() }}

</div>

@endsection


