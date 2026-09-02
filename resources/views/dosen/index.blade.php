@extends('layouts.app')

@section('content')

    <h1 class="page-header">
        Data Dosen
    </h1>
@unless(auth()->user()->isDirektur())
<div class="mb-4">
    <a href="{{ route('dosen.create') }}"
       class="btn btn-primary">

        Tambah Dosen

    </a>
</div>
@endunless

<x-alert type="success" :message="session('success')" />

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
        <label class="filter-label">Jabatan</label>
        <select name="jabatan" class="form-select filter-select">
            <option value="">Semua</option>
            <option value="dosen" {{ request('jabatan') == 'dosen' ? 'selected' : '' }}>Dosen</option>
            <option value="kaprodi" {{ request('jabatan') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
            <option value="direktur" {{ request('jabatan') == 'direktur' ? 'selected' : '' }}>Direktur</option>
        </select>
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Terapkan Filter</button>
        <a href="{{ route('dosen.index') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
    </div>
</form>

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>NIDN</th>
                <th>Program Studi</th>
                <th>Jabatan / Role</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($dosens as $index => $dosen)

            <tr>

                <td>
                    {{ $dosens->firstItem() + $index }}
                </td>

                <td>
                    {{ $dosen->user->name }}
                </td>

                <td>
                    {{ $dosen->user->email }}
                </td>

                <td>
                    {{ $dosen->nidn }}
                </td>

                <td>
                    {{ $dosen->programStudi->nama_prodi }}
                </td>

                <td>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                        @php
                            $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
                            $roles = $dosen->user ? $dosen->user->getRolesList() : ['dosen'];
                        @endphp
                        @foreach($roles as $r)
                            @php
                                $roleName = $roleMap->get($r) ?? ucfirst(str_replace('_', ' ', $r));
                            @endphp
                            @if($r === 'admin')
                                <span class="badge badge-diajukan">Admin</span>
                            @elseif($r === 'direktur' || str_starts_with($r, 'direktur'))
                                <span class="badge badge-disetujui">{{ $roleName }}</span>
                            @elseif($r === 'kaprodi' || str_starts_with($r, 'kaprodi'))
                                <span class="badge badge-diajukan" style="background: #e0e7ff; color: #4338ca;">{{ $roleName }}</span>
                            @else
                                <span class="badge badge-draft" style="background: #f1f5f9; color: #475569;">{{ $roleName }}</span>
                            @endif
                        @endforeach
                    </div>
                </td>

                <td>

                    <div class="btn-group">
                        <a href="{{ route('dosen.riwayat', $dosen->id) }}"
                        class="btn btn-sm btn-secondary">

                            Riwayat

                        </a>
                        @unless(auth()->user()->isDirektur())
                        <a href="{{ route('dosen.edit', $dosen->id) }}"
                           class="btn btn-sm btn-warning">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('dosen.destroy', $dosen->id) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus dosen ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                        @endunless

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7"
                    class="text-center text-sm" style="padding: 1.5rem; color: #6b7280;">

                    Data dosen belum tersedia.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $dosens->links() }}

</div>

@endsection
