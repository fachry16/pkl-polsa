@extends('layouts.app')

@section('content')

<h1 class="page-header">Evaluasi Kurikulum</h1>

<p class="page-subtitle" style="color: #64748b;">
    {{ $kurikulum->nama_kurikulum }} &mdash; {{ $kurikulum->programStudi->nama_prodi }}
</p>

<div class="mb-5 btn-group">
    <a href="{{ route('kurikulum.evaluasi-kurikulum.create', $kurikulum->id) }}" class="btn btn-primary">Tambah Evaluasi</a>
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
</div>

<x-alert type="success" :message="session('success')" />

@if($evaluasis->count())

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tahun Akademik</th>
                <th>Dibuat Oleh</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluasis as $evaluasi)
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1e293b;">{{ $evaluasi->judul }}</div>
                        @if($evaluasi->catatan)
                            <div style="font-size: 0.78rem; color: #64748b; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Str::limit($evaluasi->catatan, 80) }}</div>
                        @endif
                    </td>
                    <td class="text-sm">
                        {{ $evaluasi->tahunAkademik->tahun ?? '—' }}
                        @if($evaluasi->tahunAkademik)
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ ucfirst($evaluasi->tahunAkademik->semester) }}</div>
                        @endif
                    </td>
                    <td class="text-sm">{{ $evaluasi->creator->name ?? '—' }}</td>
                    <td class="text-sm">{{ $evaluasi->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('kurikulum.evaluasi-kurikulum.edit', [$kurikulum->id, $evaluasi->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                            <x-confirm
                                action="{{ route('kurikulum.evaluasi-kurikulum.destroy', [$kurikulum->id, $evaluasi->id]) }}"
                                method="DELETE"
                                title="Hapus Evaluasi"
                                message="Hapus evaluasi kurikulum ini?"
                                buttonText="Hapus"
                                confirmText="Ya, Hapus"
                            />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@else

<div class="card">
    <p class="mb-4">Belum ada evaluasi kurikulum.</p>
    <a href="{{ route('kurikulum.evaluasi-kurikulum.create', $kurikulum->id) }}" class="btn btn-primary">Tambah Evaluasi</a>
</div>

@endif

@endsection
