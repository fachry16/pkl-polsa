@extends('layouts.app')

@section('content')

<h1 class="page-header mb-2">
    Riwayat Mengajar
</h1>

<p class="mb-4">
    {{ $dosen->user->name }}
</p>

<div class="table-container">

    @php $isSelf = auth()->user()->dosen?->id === $dosen->id; @endphp

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

                <th>Status RPS</th>

                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($riwayat as $item)

                @php $rps = $item->mataKuliah->rps; @endphp

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

                        @if($rps)

                            @if($rps->status == 'Draft')
                                <span class="badge badge-draft">Draft</span>
                            @elseif($rps->status == 'Diajukan')
                                <span class="badge badge-diajukan">Diajukan</span>
                            @elseif($rps->status == 'Revisi')
                                <span class="badge badge-revisi">Revisi</span>
                            @elseif($rps->status == 'Disetujui')
                                <span class="badge badge-disetujui">Disetujui</span>
                            @endif

                        @else

                            <span class="badge badge-draft">Belum Ada</span>

                        @endif

                    </td>

                    <td class="aksi-cell">

                        @if($rps)

                            <a href="{{ route('mata-kuliah.rps.index', $item->mata_kuliah_id) }}"
                               class="btn btn-primary btn-sm">
                                Lihat RPS
                            </a>

                            @if($isSelf && in_array($rps->status, ['Draft', 'Revisi']))

                            <form action="{{ route('rps.ajukan', $rps) }}"
                                  method="POST"
                                  style="display: inline;">

                                @csrf
                                @method('PATCH')

                                <button class="btn btn-success btn-sm">
                                    {{ $rps->status == 'Revisi' ? 'Ajukan Ulang' : 'Ajukan' }}
                                </button>

                            </form>

                            @endif

                            @if($rps->status == 'Disetujui')

                            <a href="{{ route('rps.ekstrak-pdf', $rps) }}"
                               class="btn btn-secondary btn-sm">
                                Ekstrak PDF
                            </a>

                            @endif

                        @else

                            @if($isSelf)

                            <a href="{{ route('mata-kuliah.rps.create', $item->mata_kuliah_id) }}"
                               class="btn btn-primary btn-sm">
                                Buat RPS
                            </a>

                            @endif

                        @endif

                        <a href="{{ route('pengampu.lihat-kelas', $item->id) }}"
                           class="btn btn-secondary btn-sm">
                            Lihat Kelas
                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6"
                        class="text-center">

                        Belum ada riwayat mengajar.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<style>
.aksi-cell {
    white-space: nowrap;
}
.aksi-cell .btn {
    margin-right: 0.25rem;
}
.aksi-cell .btn:last-child {
    margin-right: 0;
}
</style>

@if(!request()->routeIs('dosen.self.riwayat') && !$isSelf)
<div class="mt-4">
    <a href="{{ route('dosen.index') }}"
       class="btn btn-secondary">
        Kembali
    </a>
</div>
@endif

@endsection
