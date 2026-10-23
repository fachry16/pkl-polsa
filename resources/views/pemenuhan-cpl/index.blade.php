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

<div x-data="{ q: '' }">

<input type="search" x-model="q" class="form-input filter-select" placeholder="Cari kode / deskripsi CPL" style="max-width: 420px; margin-bottom: 1rem;" aria-label="Cari CPL">

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

            @forelse($cpls as $cpl)

                <tr x-show="q === '' || $el.innerText.toLowerCase().includes(q.toLowerCase())">

                    <td class="font-medium">

                        <div>{{ $cpl->kode_cpl }}</div>

                        @if($cpl->deskripsi)
                        <div class="table-sub">{{ $cpl->deskripsi }}</div>
                        @endif

                    </td>

                    @for($semester = 1; $semester <= 8; $semester++)

                        <td>

                            @php $kodes = $matrix[$cpl->id][$semester] ?? []; @endphp

                            @if($kodes)

                                <div class="flex flex-wrap gap-2">

                                    @foreach($kodes as $kode)
                                        <span class="cpmk-chip">{{ $kode }}</span>
                                    @endforeach

                                </div>

                            @else

                                <span style="color: #cbd5e1;">—</span>

                            @endif

                        </td>

                    @endfor

                </tr>

            @empty

                <tr class="empty-row">
                    <td colspan="9">
                        Belum ada CPL.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

</div>

<div class="mt-5">
    <a href="{{ route('kurikulum.detail', $kurikulum->id) }}"
       class="btn btn-secondary">

            Kembali

    </a>

</div>

@endsection
