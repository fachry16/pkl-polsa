@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Rumusan Nilai Akhir MK
</h1>

@include('rumusan-nilai-akhir._form', [
    'action' => route('kurikulum.rumusan-nilai-akhir-mk.store', $kurikulum->id),
    'method' => 'POST',
    'submitLabel' => 'Simpan',
    'backUrl' => route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id),
])

@endsection
