@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Struktur Kurikulum
</h1>

<div class="mb-4">

    <strong>Total SKS :</strong>

    {{ $kurikulum->total_sks }} SKS

</div>

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>

                <th>Kode MK</th>
                <th>Nama MK</th>
                <th>SKS</th>

                @for($i=1;$i<=8;$i++)

                    <th>
                        S{{ $i }}
                    </th>

                @endfor

            </tr>

        </thead>

        <tbody>

            @foreach($mataKuliahs as $mk)

            <tr>

                <td>
                    {{ $mk->kode }}
                </td>

                <td>
                    {{ $mk->nama }}
                </td>

                <td class="text-center">
                    {{ $mk->sks_teori + $mk->sks_praktikum }}
                </td>

                @for($i=1;$i<=8;$i++)

                    <td class="text-center">

                        @if($mk->semester == $i)

                            ✓

                        @endif

                    </td>

                @endfor

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div class="mt-5">

    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

        Kembali

    </a>

</div>

@endsection
