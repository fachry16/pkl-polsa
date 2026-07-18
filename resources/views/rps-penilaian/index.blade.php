@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Penilaian RPS
</h1>

<div class="mb-5 btn-group">

    @if($penilaian)

        <a href="{{ route('rps.penilaian.edit', $rps) }}"
           class="btn btn-warning">

            Edit

        </a>

    @else

        <a href="{{ route('rps.penilaian.create', $rps) }}"
           class="btn btn-primary">

            Tambah Penilaian

        </a>

    @endif

</div>

<x-alert type="success" :message="session('success')" />

@if($penilaian)

<div class="card table-container">

    <table class="detail-table">

        <tr>
            <td>Tugas</td>
            <td>{{ $penilaian->tugas }}%</td>
        </tr>

        <tr>
            <td>Quiz</td>
            <td>{{ $penilaian->quiz }}%</td>
        </tr>

        <tr>
            <td>UTS</td>
            <td>{{ $penilaian->uts }}%</td>
        </tr>

        <tr>
            <td>UAS</td>
            <td>{{ $penilaian->uas }}%</td>
        </tr>

        <tr>
            <td>Praktikum</td>
            <td>{{ $penilaian->praktikum }}%</td>
        </tr>

        <tr>
            <td>Project</td>
            <td>{{ $penilaian->project }}%</td>
        </tr>

        <tr class="font-bold">
            <td>Total</td>
            <td>
                {{ $penilaian->tugas +
                   $penilaian->quiz +
                   $penilaian->uts +
                   $penilaian->uas +
                   $penilaian->praktikum +
                   $penilaian->project }}%
            </td>
        </tr>

    </table>

</div>

@else

<div class="alert alert-warning">

    Penilaian belum dibuat.

</div>

@endif
<div class="mt-4">
    <a href="{{ route('mata-kuliah.rps.index', $rps->mataKuliah) }}"
       class="btn btn-secondary">

        Kembali

    </a>
</div>
@endsection
