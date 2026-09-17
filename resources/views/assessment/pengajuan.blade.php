@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-5">
    <h1 class="page-header">
        Pengajuan Nilai Kelas
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
                <th>Kelas</th>
                <th>Dosen</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($approvals as $approval)

            @php
                $assessment = $approval->assessment;
                $pengampu = $assessment?->pengampu;
            @endphp

            <tr>

                <td>
                    @if($pengampu)
                        <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'rekap_nilai']) }}">
                            {{ $pengampu->mataKuliah?->nama }}
                        </a>
                    @else
                        -
                    @endif
                </td>

                <td>
                    {{ $pengampu?->mataKuliah?->kode }}
                </td>

                <td>
                    {{ $pengampu?->kelas }} &middot; {{ $pengampu?->label_semester }}
                </td>

                <td>
                    {{ $pengampu?->dosen?->user?->name ?? '-' }}
                </td>

                <td>

                    @if($approval->status == 'menunggu')
                        <span class="badge badge-diajukan">
                            Menunggu
                        </span>

                    @elseif($approval->status == 'direvisi')
                        <span class="badge badge-revisi">
                            Direvisi
                        </span>

                    @elseif($approval->status == 'disetujui')
                        <span class="badge badge-disetujui">
                            Disetujui
                        </span>

                        @if($approval->penyetuju)
                            <div class="text-xs mt-1">
                                oleh {{ $approval->penyetuju->name }}
                                @if($approval->disetujui_at)
                                    , {{ $approval->disetujui_at->format('d/m/Y') }}
                                @endif
                            </div>
                        @endif

                    @else
                        <span class="badge">
                            {{ $approval->status }}
                        </span>
                    @endif

                </td>

                <td>

                    @if($approval->status == 'menunggu')

                        <div class="btn-group">

                            <x-confirm
                                action="{{ route('assessment.setujui', $assessment) }}"
                                method="PATCH"
                                title="Setujui Nilai"
                                message="Setujui nilai kelas ini?"
                                sub-message="Nilai yang disetujui akan dianggap final dan tidak bisa diajukan kembali."
                                buttonText="Setujui"
                                buttonClass="btn btn-success btn-sm"
                                confirmText="Ya, Setujui"
                                confirmClass="btn-success"
                            />

                            <button class="btn btn-warning btn-sm"
                                    onclick="toggleRevisi({{ $approval->id }})">
                                Minta Revisi
                            </button>

                        </div>

                        <form id="revisi-form-{{ $approval->id }}"
                              action="{{ route('assessment.revisi', $assessment) }}"
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
                                        onclick="toggleRevisi({{ $approval->id }})">
                                    Batal
                                </button>

                            </div>

                        </form>

                    @elseif($approval->status == 'direvisi')

                        <div class="revision-box">
                            @if($approval->catatan_revisi)
                                <strong>Catatan revisi:</strong>
                                <p class="mt-1">{{ $approval->catatan_revisi }}</p>
                            @endif
                        </div>

                    @elseif($approval->status == 'disetujui')

                        <span class="badge badge-disetujui">Selesai</span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>
                <td colspan="6" class="text-center">
                    Belum ada pengajuan nilai.
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