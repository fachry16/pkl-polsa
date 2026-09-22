@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Pertemuan RPS
</h1>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.pertemuan.create', $rps->id) }}" class="btn btn-primary">Tambah Pertemuan</a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>Minggu</th>
                <th>CPMK Induk</th>
                <th>Sub CPMK</th>
                <th>Materi Pembelajaran</th>
                <th>Materi (File)</th>
                <th>Metode Daring</th>
                <th>Metode Luring</th>
                <th>Pengalaman Belajar</th>
                <th>Indikator</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($pertemuans as $pertemuan)

            <tr>

                <td class="text-center font-bold">
                    {{ $pertemuan->minggu }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->cpmk_induk ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->sub_cpmk }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->materi }}
                </td>

                <td class="text-sm">
                    @if($pertemuan->file_materi)
                        <a href="{{ route('rps.pertemuan.file', [$rps->id, $pertemuan->id]) }}" target="_blank"
                           style="color: #16a34a; text-decoration: underline; font-weight: 600;">
                            Unduh Materi
                        </a>
                    @else
                        <span style="color: #94a3b8;">-</span>
                    @endif
                </td>

                <td class="text-sm">
                    {{ $pertemuan->metode_daring ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->metode_luring ?? $pertemuan->metode ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $pertemuan->pengalaman_belajar ?? '-' }}
                </td>
                <td class="text-sm">
                    {{ $pertemuan->indikator ?? '-' }}
                </td>

                <td class="text-center text-sm">
                    {{ $pertemuan->bobot ? $pertemuan->bobot . '%' : '-' }}
                </td>

                <td class="text-center" style="white-space: nowrap;">

                    <div class="btn-group">

                        <a href="{{ route('rps.pertemuan.edit', [$rps->id, $pertemuan->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <button type="button"
                                class="btn btn-success btn-sm"
                                onclick="openUploadMateri(@js($pertemuan))">

                            Upload Materi

                        </button>

                        <x-confirm
                            action="{{ route('rps.pertemuan.destroy', [$rps->id, $pertemuan->id]) }}"
                            method="DELETE"
                            title="Hapus Pertemuan"
                            message="Hapus pertemuan ini?"
                            sub-message="Data penilaian dan absensi terkait akan ikut terhapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="11"
                    class="text-center">

                    Belum ada data pertemuan.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-5 flex justify-between items-center">
    <a href="{{ route('mata-kuliah.rps.index', $rps->mata_kuliah_id) }}"
       class="btn btn-secondary">
        Kembali
    </a>

    <div class="pagination">
        {{ $pertemuans->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    window.openUploadMateri = function (pertemuan) {
        var route = @js(route('rps.pertemuan.upload-materi', ['rps' => $rps->id, 'pertemuan' => '__P__'])).replace('__P__', pertemuan.id);
        document.getElementById('upload-materi-form').action = route;

        document.getElementById('preview-minggu').innerText = 'Minggu ' + (pertemuan.minggu || '-');
        document.getElementById('preview-materi').innerText = pertemuan.materi || '-';

        var fileNotice = document.getElementById('preview-file-materi-notice');
        if (fileNotice) {
            if (pertemuan.file_materi) {
                fileNotice.style.display = 'block';
                fileNotice.innerText = 'File materi akan diganti.';
            } else {
                fileNotice.style.display = 'none';
            }
        }

        dispatchEvent(new CustomEvent('open-modal', { detail: 'upload-materi-modal' }));
    };

});
</script>

<x-modal name="upload-materi-modal" maxWidth="xl">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Upload Materi Pertemuan</h3>
        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
            Upload file materi pembelajaran untuk pertemuan ini.
        </div>
    </div>

    <div style="margin: 1.25rem 1.5rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Pertemuan</span>
            <span id="preview-minggu" style="font-size: 0.7rem; background: #dbeafe; color: #1d4ed8; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;"></span>
        </div>
        <div id="preview-materi" style="font-size: 0.88rem; font-weight: 600; color: #1e3a5f;"></div>
        <div id="preview-file-materi-notice" style="display: none; font-size: 0.75rem; color: #b91c1c; margin-top: 0.3rem; font-weight: 600;"></div>
    </div>

    <div style="margin: 0 1.5rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span style="font-size: 0.78rem; color: #166534; line-height: 1.5;">File materi akan otomatis tersinkron sebagai materi di seluruh kelas LMS mata kuliah ini.</span>
    </div>

    <form id="upload-materi-form" method="POST" enctype="multipart/form-data" style="padding: 0 1.5rem 1.25rem 1.5rem;">
        @csrf

        <div class="form-group">

            <label class="form-label">File Materi</label>

            <input type="file"
                   name="file"
                   class="form-input w-full"
                   required>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Format PDF, DOC, XLS, PPT, ZIP, gambar (maks 50 MB)</div>

        </div>

        <div class="btn-group" style="justify-content: flex-end;">
            <button type="button"
                    class="btn btn-secondary"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'upload-materi-modal' }))">
                Batal
            </button>
            <button type="submit" class="btn btn-success">
                Simpan Materi
            </button>
        </div>
    </form>
</x-modal>

@endsection