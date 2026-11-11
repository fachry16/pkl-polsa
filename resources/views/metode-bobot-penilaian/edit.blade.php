@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Edit Metode dan Bobot Penilaian
</h1>

@include('metode-bobot-penilaian._form', [
    'action' => route('kurikulum.metode-bobot-penilaian.update', [$kurikulum->id, $item->id]),
    'method' => 'PUT',
    'submitLabel' => 'Update',
    'backUrl' => route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id),
])

@endsection
