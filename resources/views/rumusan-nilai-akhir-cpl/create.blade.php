@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Rumusan Nilai Akhir CPL
</h1>

@include('rumusan-nilai-akhir._form', [
    'action' => route('kurikulum.rumusan-nilai-akhir-cpl.store', $kurikulum->id),
    'method' => 'POST',
    'submitLabel' => 'Simpan',
    'backUrl' => route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id),
])

@endsection
