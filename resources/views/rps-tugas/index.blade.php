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

    function formatDatetimeLocal(dtString) {
        if (! dtString) return '';
        const d = new Date(dtString);
        if (isNaN(d.getTime())) return '';
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    window.openUpload = function (item) {
        const url = @js(route('rps.tugas.upload-ke-lms', ['rps' => $rps->id, 'tugas' => '__TUGAS__'])).replace('__TUGAS__', item.id);
        document.getElementById('upload-form').action = url;
        document.getElementById('upload-judul').value = item.nama_tugas || '';
        
        let ins = '';
        if (item.penugasan) ins += 'Penugasan: ' + item.penugasan + '\n\n';
        if (item.ruang_lingkup) ins += 'Ruang Lingkup: ' + item.ruang_lingkup + '\n';
        if (item.cara_pengerjaan) ins += 'Cara Pengerjaan: ' + item.cara_pengerjaan + '\n';
        if (item.luaran_tugas) ins += 'Luaran Tugas: ' + item.luaran_tugas;
        document.getElementById('upload-instruksi').value = ins.trim() || item.penugasan || '';

        document.getElementById('preview-topik').innerText = 'Minggu ' + (item.minggu_topik || '-');
        document.getElementById('preview-judul').innerText = item.nama_tugas || '-';
        document.getElementById('preview-subcpmk').innerText = item.sub_cpmk || '-';
        document.getElementById('preview-penugasan').innerText = item.penugasan || '-';
        document.getElementById('preview-ruanglingkup').innerText = item.ruang_lingkup || '-';
        document.getElementById('preview-luaran').innerText = item.luaran_tugas || '-';
        document.getElementById('preview-bataswaktu').innerText = item.batas_waktu || '-';

        const form = document.getElementById('upload-form');
        if (item.deadline) {
            form.querySelector('input[name="deadline"]').value = formatDatetimeLocal(item.deadline);
        } else {
            form.querySelector('input[name="deadline"]').value = '';
        }
        if (item.bobot_nilai !== null && item.bobot_nilai !== undefined) {
            form.querySelector('input[name="bobot_nilai"]').value = item.bobot_nilai;
        } else {
            form.querySelector('input[name="bobot_nilai"]').value = 100;
        }

        const fileNotice = document.getElementById('preview-file-soal-notice');
        if (fileNotice) {
            if (item.file_soal) {
                fileNotice.style.display = 'block';
                fileNotice.innerText = '📎 File Lampiran Soal terlampir dari RPS';
            } else {
                fileNotice.style.display = 'none';
            }
        }

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

    {{-- Live Preview Box RPS --}}
    <div style="margin: 1.25rem 1.5rem 0 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.85rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.5px;">Preview Rancangan Tugas RPS</span>
            <span id="preview-topik" style="font-size: 0.7rem; background: #dcfce7; color: #16a34a; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;"></span>
        </div>
        <div id="preview-judul" style="font-size: 0.88rem; font-weight: 700; color: #14532d;"></div>
        <div style="font-size: 0.75rem; color: #166534; margin-top: 0.3rem;">
            <strong>Sub-CPMK:</strong> <span id="preview-subcpmk"></span>
        </div>
        <div style="font-size: 0.75rem; color: #334155; margin-top: 0.3rem; line-height: 1.4;">
            <strong>Penugasan:</strong> <span id="preview-penugasan"></span>
        </div>
        <div style="font-size: 0.72rem; color: #475569; margin-top: 0.25rem;">
            <strong>Ruang Lingkup:</strong> <span id="preview-ruanglingkup"></span> &middot; <strong>Luaran:</strong> <span id="preview-luaran"></span>
        </div>
        <div style="font-size: 0.72rem; color: #16a34a; margin-top: 0.25rem; font-weight: 600;">
            Batas Waktu RPS: <span id="preview-bataswaktu"></span>
        </div>
        <div id="preview-file-soal-notice" style="display: none; font-size: 0.72rem; color: #0284c7; margin-top: 0.25rem; font-weight: 600;">
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