@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Sub-CPMK
</h1>

<x-alert type="success" :message="session('success')" />

<div class="card">

    <form action="{{ route('rps.sub-cpmk.store', $rps) }}" method="POST">

        @csrf

        <div class="form-group">

            <label class="form-label" for="sub_cpmk_baru">Tambah Sub-CPMK</label>

            <textarea id="sub_cpmk_baru"
                      name="sub_cpmk"
                      class="form-textarea w-full"
                      rows="3"
                      placeholder="Tulis satu Sub-CPMK."
                      required>{{ old('sub_cpmk') }}</textarea>

            @error('sub_cpmk')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <div class="btn-group">

            <button class="btn btn-primary">
                Tambah
            </button>

            <a href="{{ route('mata-kuliah.rps.index', $rps->mata_kuliah_id) }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </div>

    </form>

</div>

<div class="table-container mt-5">

    <table class="data-table data-table-stack">

        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Sub-CPMK</th>
                <th style="width: 180px;">Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($subCpmks as $index => $subCpmk)

            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $subCpmk }}</td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('rps.sub-cpmk.index', ['rps' => $rps, 'edit' => $index]) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <x-confirm
                            action="{{ route('rps.sub-cpmk.destroy', [$rps, $index]) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus Sub-CPMK ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                    </div>
                </td>
            </tr>

            @if((string) request('edit') === (string) $index)

            <tr>
                <td colspan="3">
                    <form action="{{ route('rps.sub-cpmk.update', [$rps, $index]) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <textarea name="sub_cpmk"
                                  class="form-textarea w-full"
                                  rows="3"
                                  required>{{ old('sub_cpmk', $subCpmk) }}</textarea>

                        @error('sub_cpmk')
                            <span class="form-error">{{ $message }}</span>
                        @enderror

                        <div class="btn-group mt-2">
                            <button class="btn btn-primary btn-sm">
                                Simpan
                            </button>

                            <a href="{{ route('rps.sub-cpmk.index', $rps) }}"
                               class="btn btn-secondary btn-sm">
                                Batal
                            </a>
                        </div>

                    </form>
                </td>
            </tr>

            @endif

            @empty

            <tr class="empty-row">
                <td colspan="3">
                    Belum ada Sub-CPMK.
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection
