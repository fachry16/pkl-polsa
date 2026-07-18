@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-5">
    <h1 class="page-header">
        Pengajuan RPS
    </h1>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="table-container">

    <table class="data-table">

        <thead>
            <tr>
                <th>Mata Kuliah</th>
                <th>Kode</th>
                <th>Semester</th>
                <th>Dosen</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($rpss as $rps)

            <tr>

                <td>
                    <a href="{{ route('mata-kuliah.rps.index', $rps->mataKuliah) }}">
                        {{ $rps->mataKuliah->nama }}
                    </a>
                </td>

                <td>
                    {{ $rps->mataKuliah->kode }}
                </td>

                <td>
                    {{ $rps->semester }}
                </td>

                <td>
                    {{ $rps->dosen_pengampu }}
                </td>

                <td>

                    @if($rps->status == 'Diajukan')
                        <span class="badge badge-diajukan">
                            Diajukan
                        </span>

                    @elseif($rps->status == 'Revisi')
                        <span class="badge badge-revisi">
                            Revisi
                        </span>

                    @elseif($rps->status == 'Disetujui')
                        <span class="badge badge-disetujui">
                            Disetujui
                        </span>

                        @if($rps->disetujuiOleh)
                            <div class="text-xs mt-1">
                                oleh {{ $rps->disetujuiOleh->name }}
                                @if($rps->tanggal_disetujui)
                                    , {{ $rps->tanggal_disetujui->format('d/m/Y') }}
                                @endif
                            </div>
                        @endif

                    @else
                        <span class="badge">
                            {{ $rps->status }}
                        </span>
                    @endif

                </td>

                <td>

                    @if($rps->status == 'Diajukan')

                        <div class="btn-group">

                            <x-confirm
                                action="{{ route('rps.setujui', $rps) }}"
                                method="PATCH"
                                title="Setujui RPS"
                                message="Setujui RPS ini?"
                                sub-message="RPS yang disetujui akan langsung berlaku dan tidak bisa diedit."
                                buttonText="Setujui"
                                buttonClass="btn btn-success btn-sm"
                                confirmText="Ya, Setujui"
                                confirmClass="btn-success"
                            />

                            <button class="btn btn-warning btn-sm"
                                    onclick="toggleRevisi({{ $rps->id }})">
                                Minta Revisi
                            </button>

                        </div>

                        <form id="revisi-form-{{ $rps->id }}"
                              action="{{ route('rps.revisi', $rps) }}"
                              method="POST"
                              class="hidden mt-2">

                            @csrf
                            @method('PATCH')

                            <textarea name="catatan_revisi"
                                      class="form-textarea w-full"
                                      rows="3"
                                      placeholder="Catatan revisi..."
                                      required></textarea>

                            <div class="btn-group mt-1">

                                <button class="btn btn-warning btn-sm">
                                    Kirim Revisi
                                </button>

                                <button type="button"
                                        class="btn btn-secondary btn-sm"
                                        onclick="toggleRevisi({{ $rps->id }})">
                                    Batal
                                </button>

                            </div>

                        </form>

                    @elseif($rps->status == 'Revisi')

                        <div class="revision-box">
                            @if($rps->catatan_revisi)
                                <strong>Catatan revisi:</strong>
                                <p class="mt-1">{{ $rps->catatan_revisi }}</p>
                            @endif
                        </div>

                    @elseif($rps->status == 'Disetujui')

                        <span class="badge badge-disetujui">Selesai</span>

                        <a href="{{ route('rps.ekstrak-pdf', $rps) }}"
                           class="btn btn-primary btn-sm mt-1">
                            Ekstrak PDF
                        </a>

                    @endif

                </td>

            </tr>

            @empty

            <tr>
                <td colspan="6" class="text-center">
                    Belum ada pengajuan RPS.
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@push('scripts')
<script>
    function toggleRevisi(id) {
        const form = document.getElementById('revisi-form-' + id);
        form.classList.toggle('hidden');
    }
</script>
@endpush

@endsection
