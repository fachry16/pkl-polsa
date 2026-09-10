@extends('layouts.app')

@section('content')

<h1 class="page-header">Evaluasi Kurikulum</h1>

<p class="page-subtitle" style="color: #64748b;">
    Daftar catatan evaluasi kurikulum berdasarkan filter yang dipilih.
</p>

@include('assessment._nav', ['current' => 'evaluasi'])

@if(session('success'))
<div class="alert alert-success mb-3">
    {{ session('success') }}
</div>
@endif

<x-alert type="error" :message="session('error')" />

@include('assessment._filters', ['filters' => $filters, 'drop' => $drop])

@if($filters['kurikulum_id'])
    <div class="mb-5 btn-group">
        <a href="{{ route('kurikulum.evaluasi-kurikulum.create', [$filters['kurikulum_id'], 'redirect_to' => 'assessment']) }}" class="btn btn-primary">Tambah Evaluasi</a>
    </div>
@endif

@if($evaluasis->count())

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kurikulum</th>
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
                        <div style="font-weight: 600; color: #1e293b; font-size: 0.82rem;">{{ $evaluasi->kurikulum->nama_kurikulum }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $evaluasi->kurikulum->programStudi->nama_prodi ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #1e293b;">{{ $evaluasi->judul }}</div>
                        @if($evaluasi->catatan)
                            <div style="font-size: 0.78rem; color: #64748b; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Str::limit($evaluasi->catatan, 80) }}</div>
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
                            <a href="{{ route('kurikulum.evaluasi-kurikulum.edit', [$evaluasi->kurikulum_id, $evaluasi->id, 'redirect_to' => 'assessment']) }}" class="btn btn-warning btn-sm">Edit</a>
                            <x-confirm
                                action="{{ route('kurikulum.evaluasi-kurikulum.destroy', [$evaluasi->kurikulum_id, $evaluasi->id]) }}"
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

<div class="card" style="padding: 2rem; text-align: center; color: #64748b;">
    @if($filters['kurikulum_id'])
        Belum ada evaluasi kurikulum untuk kurikulum ini.
    @else
        Pilih kurikulum pada filter di atas untuk melihat daftar evaluasi, atau
        <a href="{{ route('assessment.index') }}" style="color: #4f46e5;">kembali ke Monitoring</a>.
    @endif
</div>

@endif

@endsection
