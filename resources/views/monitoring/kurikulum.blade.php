@extends('layouts.app')

@section('content')

<h1 class="page-header">Monitoring Kurikulum</h1>

<p style="font-size: 0.85rem; color: #64748b; margin: 0 0 1rem;">
    Daftar seluruh kurikulum di setiap program studi. Mode monitoring read-only tanpa ubah data.
</p>

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
        <label class="filter-label">Status</label>
        <select name="status" class="form-select filter-select">
            <option value="">Semua</option>
            @foreach(['Aktif', 'Draft', 'Arsip'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Terapkan Filter</button>
        <a href="{{ route('monitoring.kurikulum') }}" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Reset</a>
    </div>
</form>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Program Studi</th>
                <th>Nama Kurikulum</th>
                <th>Tahun Berlaku</th>
                <th>Beban Studi</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kurikulums as $index => $kurikulum)
                <tr>
                    <td>{{ $kurikulums->firstItem() + $index }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $kurikulum->programStudi->nama_prodi ?? '-' }}</td>
                    <td>{{ $kurikulum->nama_kurikulum }}</td>
                    <td>{{ $kurikulum->tahun_berlaku }}</td>
                    <td>{{ $kurikulum->beban_studi ?? '-' }}</td>
                    <td style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $kurikulum->deskripsi ?? '-' }}</td>
                    <td>
                        @if($kurikulum->status == 'Aktif')
                            <span class="badge badge-disetujui">Aktif</span>
                        @elseif($kurikulum->status == 'Draft')
                            <span class="badge badge-draft">Draft</span>
                        @else
                            <span class="badge badge-diajukan">Arsip</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 1.5rem; color: #6b7280;">
                        Data kurikulum belum tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $kurikulums->links() }}
</div>

@endsection