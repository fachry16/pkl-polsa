@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Pemenuhan CPL
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

<x-alert type="success" :message="session('success')" />

<form method="POST"
      action="{{ route('kurikulum.pemenuhan-cpl.store', $kurikulum->id) }}">

    @csrf

    <div class="table-container">

        <table class="data-table">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    @for($semester = 1; $semester <= 8; $semester++)

                        <th class="text-center">
                            Smt {{ $semester }}
                        </th>

                    @endfor

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                    <tr>

                        <td class="font-medium">
                            {{ $cpl->kode_cpl }}
                        </td>

                        @for($semester = 1; $semester <= 8; $semester++)

                            @php
                                $key = $cpl->id . '-' . $semester;
                            @endphp

                            <td class="text-center">

                                <input type="checkbox"
                                       name="mapping[{{ $key }}]"
                                       value="1"
                                       {{ isset($checked[$key]) ? 'checked' : '' }}>

                            </td>

                        @endfor

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @if(auth()->user()->role !== 'dosen')
    <div class="flex gap-2 mt-5">

        <button class="btn btn-success">

            Simpan

        </button>

    </div>
    @endif

</form>
<div class="mt-5">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>

</div>
@endsection