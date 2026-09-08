@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Rancangan Tugas dan Latihan
</h1>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.tugas.create', $rps->id) }}" class="btn btn-primary">Tambah Tugas</a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>Minggu Ke / Topik</th>
                <th>Nama Tugas</th>
                <th>Sub-CPMK</th>
                <th>Penugasan</th>
                <th>Ruang Lingkup</th>
                <th>Cara Pengerjaan</th>
                <th>Batas Waktu</th>
                <th>Luaran Tugas</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

        @forelse($tugas as $item)

            <tr>

                <td class="text-center font-bold">
                    {{ $item->minggu_topik }}
                </td>

                <td class="text-sm">
                    <div style="font-weight: 600;">{{ $item->nama_tugas }}</div>
                    <span style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 0.1rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">
                        {{ $item->kategori_komponen ?? 'tugas' }}
                    </span>
                </td>

                <td class="text-sm">
                    {{ $item->sub_cpmk ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->penugasan ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->ruang_lingkup ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->cara_pengerjaan ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->batas_waktu ?? '-' }}
                </td>

                <td class="text-sm">
                    {{ $item->luaran_tugas ?? '-' }}
                </td>

                <td class="text-center" style="white-space: nowrap;">

                    <div class="btn-group">

                        <a href="{{ route('rps.tugas.edit', [$rps->id, $item->id]) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <button type="button"
                                class="btn btn-success btn-sm"
                                onclick="openUpload(@js($item))">

                            Upload

                        </button>

                        <x-confirm
                            action="{{ route('rps.tugas.destroy', [$rps->id, $item->id]) }}"
                            method="DELETE"
                            title="Hapus Tugas"
                            message="Hapus rancangan tugas ini?"
                            sub-message="Data tugas ini akan ikut terhapus."
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="9"
                    class="text-center">

                    Belum ada data rancangan tugas.

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
        {{ $tugas->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    window.openUpload = function (item) {
        const url = @js(route('rps.tugas.upload-ke-lms', ['rps' => $rps->id, 'tugas' => '__TUGAS__'])).replace('__TUGAS__', item.id);
        document.getElementById('upload-form').action = url;

        document.getElementById('preview-topik').innerText = 'Minggu ' + (item.minggu_topik || '-');
        document.getElementById('preview-judul').innerText = item.nama_tugas || '-';
        document.getElementById('preview-subcpmk').innerText = item.sub_cpmk || '-';
        document.getElementById('preview-penugasan').innerText = item.penugasan || '-';
        document.getElementById('preview-ruanglingkup').innerText = item.ruang_lingkup || '-';
        document.getElementById('preview-luaran').innerText = item.luaran_tugas || '-';

        if (item.deadline) {
            const d = new Date(item.deadline);
            document.getElementById('preview-deadline').innerText = !isNaN(d.getTime()) ? d.toLocaleString('id-ID') : item.deadline;
        } else {
            document.getElementById('preview-deadline').innerText = 'Belum diatur';
        }
        document.getElementById('preview-bobot').innerText = (item.bobot_nilai !== null && item.bobot_nilai !== undefined) ? item.bobot_nilai : '100';

        const fileNotice = document.getElementById('preview-file-soal-notice');
        if (fileNotice) {
            if (item.file_soal) {
                fileNotice.style.display = 'block';
                fileNotice.innerText = '📎 File Lampiran Soal terlampir dari RPS';
            } else {
                fileNotice.style.display = 'none';
            }
        }

        dispatchEvent(new CustomEvent('open-modal', { detail: 'upload-tugas-modal' }));
    };

});
</script>

<x-modal name="upload-tugas-modal" maxWidth="2xl">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Konfirmasi Upload Tugas ke LMS</h3>
        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
            Rancangan tugas RPS ini akan diunggah ke seluruh kelas LMS mata kuliah ini sebagai <strong>Draf</strong>.
        </div>
    </div>

    {{-- Live Preview Box RPS --}}
    <div style="margin: 1.25rem 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.5px;">Preview Rancangan Tugas RPS</span>
            <span id="preview-topik" style="font-size: 0.7rem; background: #dcfce7; color: #16a34a; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;"></span>
        </div>
        <div id="preview-judul" style="font-size: 0.95rem; font-weight: 700; color: #14532d; margin-bottom: 0.4rem;"></div>
        <div style="font-size: 0.78rem; color: #166534; margin-top: 0.3rem;">
            <strong>Sub-CPMK:</strong> <span id="preview-subcpmk"></span>
        </div>
        <div style="font-size: 0.78rem; color: #334155; margin-top: 0.3rem; line-height: 1.4;">
            <strong>Penugasan:</strong> <span id="preview-penugasan"></span>
        </div>
        <div style="font-size: 0.75rem; color: #475569; margin-top: 0.3rem;">
            <strong>Ruang Lingkup:</strong> <span id="preview-ruanglingkup"></span> &middot; <strong>Luaran:</strong> <span id="preview-luaran"></span>
        </div>
        <div style="font-size: 0.75rem; color: #16a34a; margin-top: 0.4rem; font-weight: 600;">
            Deadline: <span id="preview-deadline"></span> &middot; Bobot Nilai: <span id="preview-bobot"></span>
        </div>
        <div id="preview-file-soal-notice" style="display: none; font-size: 0.75rem; color: #0284c7; margin-top: 0.3rem; font-weight: 600;">
        </div>
    </div>

    <form id="upload-form" method="POST" style="padding: 0 1.5rem 1.25rem 1.5rem;">
        @csrf
        <div style="font-size: 0.78rem; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
            💡 <strong>Catatan:</strong> Setelah dikonfirmasi, tugas akan masuk ke daftar tugas LMS dosen dengan status <em>Draf</em>. Dosen dapat menekan tombol <strong>"Tugaskan"</strong> pada kelas LMS masing-masing untuk mempublikasikannya kepada mahasiswa.
        </div>

        <div class="btn-group" style="justify-content: flex-end;">
            <button type="button"
                    class="btn btn-secondary"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'upload-tugas-modal' }))">
                Batal
            </button>
            <button type="submit" class="btn btn-success">
                Konfirmasi &amp; Upload ke LMS
            </button>
        </div>
    </form>
</x-modal>

@endsection