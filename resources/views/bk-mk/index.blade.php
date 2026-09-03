@extends('layouts.app')
@section('content')
<h1 class="page-header">
    Matriks BK - MK
</h1>

<form method="POST" action="{{ route('kurikulum.bk-mk.update', $kurikulum->id) }}">
    @csrf
    @method('PUT')
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama MK</th>
                    @foreach($bahanKajians as $bahanKajian)
                    <th>
                        <div>{{ $bahanKajian->kode_bk }}</div>
                        @if($bahanKajian->nama_bk)
                        <div class="table-sub">{{ $bahanKajian->nama_bk }}</div>
                        @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($mataKuliahs as $matakuliah)
                <tr>
                    <td>{{ $matakuliah->kode }}</td>
                    <td>{{ $matakuliah->nama }}</td>
                    @foreach($bahanKajians as $bahanKajian)
                    <td class="text-center">
                        <input type="checkbox" name="mataKuliah[{{ $matakuliah->id }}][]" value="{{ $bahanKajian->id }}" {{ $matakuliah->bahanKajians->contains($bahanKajian->id) ? 'checked' : '' }}>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(auth()->user()->role !== 'dosen')
    <button type="submit" class="btn btn-primary mt-5">Simpan Matriks</button>
    @endif
</form>
<div class="mt-4">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection