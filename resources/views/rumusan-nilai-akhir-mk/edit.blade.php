@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Rumusan Nilai Akhir MK
</h1>

@include('rumusan-nilai-akhir._form', [
    'action' => route('kurikulum.rumusan-nilai-akhir-mk.update', [$kurikulum->id, $item->id]),
    'method' => 'PUT',
    'submitLabel' => 'Update',
    'backUrl' => route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id),
])

@endsection
