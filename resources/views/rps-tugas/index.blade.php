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
                    {{ $item->nama_tugas }}
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

    const perus = @js($pertemuans->map(fn($p) => ['id' => $p->id, 'minggu' => (int) $p->minggu]));

    function parseMinggu(mingguTopik) {
        if (! mingguTopik) return null;
        const m = String(mingguTopik).match(/\d+/);
        return m ? parseInt(m[0], 10) : null;
    }

    function autoPertemuan(mingguTopik) {
        const minggu = parseMinggu(mingguTopik);
        if (! minggu) return '';
        const found = perus.find(p => p.minggu === minggu);
        return found ? found.id : '';
    }

    window.openUpload = function (item) {
        const url = @js(route('rps.tugas.upload-ke-lms', ['rps' => $rps->id, 'tugas' => '__TUGAS__'])).replace('__TUGAS__', item.id);
        document.getElementById('upload-form').action = url;
        document.getElementById('upload-judul').value = item.nama_tugas || '';
        document.getElementById('upload-instruksi').value = item.penugasan || '';
        document.getElementById('upload-pertemuan').value = autoPertemuan(item.minggu_topik);
        document.getElementById('upload-file').value = '';
        dispatchEvent(new CustomEvent('open-modal', { detail: 'upload-tugas-modal' }));
    };

});
</script>

<x-modal name="upload-tugas-modal" maxWidth="2xl">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Upload Tugas ke LMS</h3>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">
            Tugas akan dibuat sebagai LmsTugas pada kelas dan pertemuan yang dipilih.
        </div>
    </div>

    <form id="upload-form"
          method="POST"
          enctype="multipart/form-data"
          style="padding: 1.25rem 1.5rem;">

        @csrf

        <div class="form-group">
            <label class="form-label">Judul Tugas <span style="color:#dc2626;">*</span></label>
            <input type="text" id="upload-judul" name="judul" class="form-input w-full" required>
        </div>

        <div class="form-group">
            <label class="form-label">Instruksi <span style="color:#dc2626;">*</span></label>
            <textarea id="upload-instruksi" name="instruksi" class="form-textarea w-full" style="min-height: 90px;" required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Kelas (LMS) <span style="color:#dc2626;">*</span></label>
            <select name="pengampu_id" class="form-input w-full" required>
                <option value="">-- Pilih kelas --</option>
                @foreach($pengampuKelas as $kelas)
                    <option value="{{ $kelas->id }}">
                        Kelas {{ $kelas->kelas }} · {{ $kelas->tahunAkademik?->tahun ?? '' }}
                        ({{ $kelas->label_semester }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">RPS Pertemuan <span style="color:#dc2626;">*</span></label>
            <select id="upload-pertemuan" name="rps_pertemuan_id" class="form-input w-full" required>
                <option value="">-- Pilih pertemuan --</option>
                @foreach($pertemuans as $pertemuan)
                    <option value="{{ $pertemuan->id }}">
                        Minggu {{ $pertemuan->minggu }} - {{ Str::limit($pertemuan->sub_cpmk, 60) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Deadline <span style="color:#dc2626;">*</span></label>
            <input type="datetime-local" name="deadline" class="form-input w-full" required>
        </div>

        <div class="form-group">
            <label class="form-label">Bobot (Relatif Antar Tugas) <span style="color:#dc2626;">*</span></label>
            <input type="number" name="bobot_nilai" class="form-input w-full" min="0" max="100" value="100" required>
        </div>

        <div class="form-group">
            <label class="form-label">Batas Upload File Jawaban (MB)</label>
            <input type="number" name="batas_upload_mb" class="form-input w-full" min="1" max="50" placeholder="Kosongkan = 50 MB">
        </div>

        <div class="form-group">
            <label class="form-label">File Soal (Lampiran)</label>
            <input type="file" id="upload-file" name="file" class="form-input w-full">
            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">PDF, DOC, XLS, PPT, gambar, ZIP, dll (maks 50 MB).</div>
        </div>

        <div class="btn-group" style="margin-top: 1rem; justify-content: flex-end;">
            <button type="button"
                    class="btn btn-secondary"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'upload-tugas-modal' }))">
                Batal
            </button>
            <button type="submit" class="btn btn-success">
                Upload ke LMS
            </button>
        </div>

    </form>
</x-modal>

@endsection