@extends('layouts.app')

@section('content')

<h1 class="page-header mb-2">
    Riwayat Mengajar
</h1>

<p class="mb-4">
    {{ $dosen->user->name }}
</p>

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>

                <th>
                    Tahun Akademik
                </th>

                <th>
                    Semester
                </th>

                <th>
                    Mata Kuliah
                </th>

                <th>
                    Kelas
                </th>

                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($riwayat as $item)

                <tr>

                    <td>
                        {{ $item->tahunAkademik->tahun }}
                    </td>

                    <td>
                        {{ $item->semester_akademik }}
                    </td>

                    <td>
                        {{ $item->mataKuliah->kode }}
                        -
                        {{ $item->mataKuliah->nama }}
                    </td>

                    <td>
                        {{ $item->kelas ?? '-' }}
                    </td>

                    <td>
                        <a href="{{ route('mata-kuliah.rps.index', $item->mata_kuliah_id) }}"
                           class="btn btn-primary btn-sm"
                           style="margin-right: 0.25rem;">
                            RPS
                        </a>
                        <a href="{{ route('pengampu.lihat-kelas', $item->id) }}"
                           class="btn btn-secondary btn-sm">
                            Lihat Kelas
                        </a>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5"
                        class="text-center">

                        Belum ada riwayat mengajar.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    @if(auth()->user()->role === 'dosen')
        <a href="{{ route('dosen.self') }}"
           class="btn btn-secondary">

            Kembali

        </a>
    @else
        <a href="{{ route('dosen.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>
    @endif

</div>

@endsection
