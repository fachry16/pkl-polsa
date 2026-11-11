@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Rumusan Nilai Akhir CPL
</h1>

@include('rumusan-nilai-akhir._form', [
    'action' => route('kurikulum.rumusan-nilai-akhir-cpl.update', [$kurikulum->id, $item->id]),
    'method' => 'PUT',
    'submitLabel' => 'Update',
    'backUrl' => route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id),
])

@endsection
