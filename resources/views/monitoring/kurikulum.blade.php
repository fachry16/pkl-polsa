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
        <label class="filter-label" for="monitoring-prodi">Program Studi</label>
        <x-searchable-select
            id="monitoring-prodi"
            name="program_studi_id"
            :options="$programStudis->map(fn ($prodi) => ['value' => $prodi->id, 'label' => $prodi->nama_prodi])"
            :value="request('program_studi_id')"
            placeholder="Semua program studi"
            input-class="form-select filter-select search-select-input"
            allow-empty
            auto-submit />
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
        <table class="data-table data-table-stack">
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
                        <td data-label="No">{{ $kurikulums->firstItem() + $index }}</td>
                        <td data-label="Program Studi" style="font-weight: 600; color: #0f172a;">{{ $kurikulum->programStudi->nama_prodi ?? '-' }}</td>
                        <td data-label="Nama Kurikulum">{{ $kurikulum->nama_kurikulum }}</td>
                        <td data-label="Tahun Berlaku">{{ $kurikulum->tahun_berlaku }}</td>
                        <td data-label="Beban Studi">{{ $kurikulum->beban_studi ?? '-' }}</td>
                        <td data-label="Deskripsi">{{ $kurikulum->deskripsi ?? '-' }}</td>
                        <td data-label="Status">
                            @if($kurikulum->status == 'Aktif')
                                <span class="badge badge-disetujui">Aktif</span>
                            @elseif($kurikulum->status == 'Draft')
                                <span class="badge badge-draft">Draft</span>
                            @else
                                <span class="badge badge-diajukan">Arsip</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" data-label="Informasi" class="text-center" style="padding: 1.5rem; color: #6b7280;">
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