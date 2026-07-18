@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Review RPS - {{ $rps->mataKuliah->nama }}
</h1>

<div class="card">

    <div class="mb-4">
        <span class="font-semibold">Status:</span>
        @if($rps->status == 'Diajukan')
            <span class="badge badge-diajukan ml-2">Diajukan</span>
        @elseif($rps->status == 'Revisi')
            <span class="badge badge-revisi ml-2">Revisi</span>
        @elseif($rps->status == 'Disetujui')
            <span class="badge badge-disetujui ml-2">Disetujui</span>
        @endif
    </div>

    <table class="detail-table mb-4">
        <tr>
            <td class="font-semibold">Kode RPS</td>
            <td>{{ $rps->kode_rps }}</td>
        </tr>
        <tr>
            <td class="font-semibold">Semester</td>
            <td>{{ $rps->semester }}</td>
        </tr>
        <tr>
            <td class="font-semibold">Dosen Pengampu</td>
            <td>{{ $rps->dosen_pengampu }}</td>
        </tr>
    </table>

    @if($rps->status == 'Revisi' && $rps->catatan_revisi)

    <div class="revision-box mb-4">
        <strong>Catatan Revisi:</strong>
        <p class="mt-1">{{ $rps->catatan_revisi }}</p>
    </div>

    <form action="{{ route('rps.ajukan', $rps) }}" method="POST">

        @csrf
        @method('PATCH')

        <button class="btn btn-primary">
            Ajukan Ulang
        </button>

    </form>

    @endif

    @if($rps->status == 'Diajukan')

    <div class="btn-group">

        <form action="{{ route('rps.setujui', $rps) }}" method="POST">

            @csrf
            @method('PATCH')

            <button class="btn btn-success">
                Setujui
            </button>

        </form>

        <button class="btn btn-warning"
                onclick="document.getElementById('revisi-form').classList.toggle('hidden')">
            Minta Revisi
        </button>

    </div>

    <form id="revisi-form"
          action="{{ route('rps.revisi', $rps) }}"
          method="POST"
          class="hidden mt-3">

        @csrf
        @method('PATCH')

        <label class="font-semibold">Catatan Revisi:</label>

        <textarea name="catatan_revisi"
                  class="form-textarea w-full"
                  rows="4"
                  required></textarea>

        <button class="btn btn-warning mt-2">
            Kirim Revisi
        </button>

    </form>

    @endif

    <div class="mt-6">
        <a href="{{ route('rps.pengajuan') }}"
           class="btn btn-secondary">
            Kembali ke Pengajuan
        </a>
    </div>

</div>

<script>
    function toggleRevisi() {
        document.getElementById('revisi-form').classList.toggle('hidden');
    }
</script>

@endsection
