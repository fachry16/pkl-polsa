@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Matriks CPL - PL
</h1>

<form method="POST"
      action="{{ route('kurikulum.cpl-pl.update', $kurikulum->id) }}">

    @csrf
    @method('PUT')

    <div class="table-container">

        <table class="data-table">

            <thead>

                <tr>

                    <th>
                        Kode CPL
                    </th>

                    @foreach($profilLulusans as $pl)

                    <th>

                        {{ $pl->kode_pl }}

                    </th>

                    @endforeach

                </tr>

            </thead>

            <tbody>

                @foreach($cpls as $cpl)

                <tr>

                    <td>

                        {{ $cpl->kode_cpl }}

                    </td>

                    @foreach($profilLulusans as $pl)

                    <td class="text-center">

                        <input type="checkbox"
                               name="cpl[{{ $cpl->id }}][]"
                               value="{{ $pl->id }}"
                               {{ $cpl->profilLulusans->contains($pl->id) ? 'checked' : '' }}>

                    </td>

                    @endforeach

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @if(auth()->user()->role !== 'dosen')
    <button type="submit"
            class="btn btn-primary mt-5">

        Simpan Matriks

    </button>
    @endif

</form>
    <div class="mt-4">
        <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
           class="btn btn-secondary">

            Kembali

        </a>
    </div>
@endsection