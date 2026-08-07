@extends('layouts.app')

@section('content')

<h1 class="page-header">
    CPL - CPMK
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

<x-alert type="success" :message="session('success')" />

<form method="POST"
      action="{{ route('kurikulum.cpl-cpmk-mk.store', $kurikulum->id) }}">

    @csrf

    <div class="table-container">

        <table class="data-table">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    <th>
                        Kode CPMK
                    </th>

                    <th>
                        Deskripsi CPMK
                    </th>

                    <th>
                        Semester
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                    @foreach($cpmks as $cpmk)

                        <tr>

                            <td class="font-medium">
                                {{ $cpl->kode_cpl }}
                            </td>

                            <td>
                                {{ $cpmk->kode_cpmk }}
                            </td>

                            <td>
                                {{ $cpmk->deskripsi }}
                            </td>

                            <td>

                                <div class="flex flex-wrap gap-2">

                                    @for($semester = 1; $semester <= 8; $semester++)

                                        @php
                                            $key = $cpl->id . '-' . $cpmk->id . '-' . $semester;
                                        @endphp

                                        <label class="flex items-center gap-2">

                                            <input type="checkbox"
                                                   name="mapping[{{ $key }}]"
                                                   value="1"
                                                   {{ isset($checked[$key]) ? 'checked' : '' }}>

                                            <span>
                                                {{ $semester }}
                                            </span>

                                        </label>

                                    @endfor

                                </div>

                            </td>

                        </tr>

                    @endforeach

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