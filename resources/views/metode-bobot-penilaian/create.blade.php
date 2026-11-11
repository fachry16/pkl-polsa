@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Metode dan Bobot Penilaian
</h1>

@include('metode-bobot-penilaian._form', [
    'action' => route('kurikulum.metode-bobot-penilaian.store', $kurikulum->id),
    'method' => 'POST',
    'submitLabel' => 'Simpan',
    'backUrl' => route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id),
])

@endsection
