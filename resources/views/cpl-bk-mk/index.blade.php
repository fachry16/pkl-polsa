@extends('layouts.app')

@section('content')

<h1 class="page-header">
    CPL - BK - MK
</h1>

<p class="mb-5">
    {{ $kurikulum->nama_kurikulum }}
    -
    {{ $kurikulum->programStudi->nama_prodi }}
</p>

<x-alert type="success" :message="session('success')" />

{{-- Pilih Mata Kuliah --}}
<form method="GET"
      action="{{ route('kurikulum.cpl-bk-mk.index', $kurikulum->id) }}"
      class="card mb-5">

    <div class="form-group">

        <label class="form-label">
            Pilih Mata Kuliah
        </label>

        <div class="flex gap-3 mt-2">

            <select name="mata_kuliah_id"
                    class="form-select">

                <option value="">
                    -- Pilih Mata Kuliah --
                </option>

                @foreach($mataKuliahs as $mk)

                    <option value="{{ $mk->id }}"
                        {{ $mataKuliahId == $mk->id ? 'selected' : '' }}>

                        {{ $mk->kode }}
                        -
                        {{ $mk->nama }}

                    </option>

                @endforeach

            </select>

            <button class="btn btn-primary">
                Tampilkan
            </button>

        </div>

    </div>

</form>

@if($mataKuliahId)

<form method="POST"
      action="{{ route('kurikulum.cpl-bk-mk.store', $kurikulum->id) }}">

    @csrf

    <input type="hidden"
           name="mata_kuliah_id"
           value="{{ $mataKuliahId }}">

    <div class="table-container">

        <table class="data-table">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    @foreach($bahanKajians as $bk)

                        <th>

                            {{ $bk->kode_bk }}

                        </th>

                    @endforeach

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                    <tr>

                        <td class="font-medium">

                            {{ $cpl->kode_cpl }}

                        </td>

                        @foreach($bahanKajians as $bk)

                            @php

                                $key = $cpl->id . '-' . $bk->id;

                            @endphp

                            <td class="text-center">

                                <input type="checkbox"
                                       name="mapping[{{ $key }}]"
                                       value="1"

                                       {{ isset($checked[$key]) ? 'checked' : '' }}>

                            </td>

                        @endforeach

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

@endif
<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
</div>

@endsection