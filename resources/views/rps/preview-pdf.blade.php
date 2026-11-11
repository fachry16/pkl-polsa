@extends('layouts.app')

@section('content')

<h1 class="page-header">
    RPS - {{ $rps->mataKuliah->nama }}
</h1>

<p class="page-subtitle">
    {{ $rps->mataKuliah->kode }} — {{ $rps->mataKuliah->kurikulum->nama_kurikulum ?? '-' }}
</p>

<div class="mb-4">
    <a href="{{ route('rps.ekstrak-pdf', ['rps' => $rps, 'download' => 1]) }}"
       class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:6px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download PDF
    </a>

    <a href="{{ route('mata-kuliah.rps.index', $rps->mataKuliah) }}"
       class="btn btn-secondary">
        Kembali
    </a>
</div>

<div class="rps-document">

    @include('rps._cover')

    @include('rps._document')

</div>

@endsection
