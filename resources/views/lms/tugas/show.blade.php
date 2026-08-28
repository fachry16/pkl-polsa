@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ $tugas->judul }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
</div>


<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1.5rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem 2.5rem;">
        <div>
            <div style="font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Deadline</div>
            <div style="font-size: 0.9rem; font-weight: 500; color: {{ $tugas->deadline->isPast() ? '#dc2626' : '#059669' }};">{{ $tugas->deadline->format('d M Y H:i') }}</div>
        </div>
        <div>
            <div style="font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Bobot Nilai</div>
            <div style="font-size: 0.9rem; font-weight: 500; color: #1e293b;">{{ $tugas->bobot_nilai }}</div>
        </div>
        <div>
            <div style="font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Maks. Upload</div>
            <div style="font-size: 0.9rem; font-weight: 500; color: #1e293b;">{{ $tugas->batas_upload_mb ?? 50 }} MB</div>
        </div>
        <div>
            <div style="font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em;">Status</div>
            <div>
                @if($tugas->deadline->isPast())
                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; font-weight: 600;">Tutup</span>
                @else
                    <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; font-weight: 600;">Aktif</span>
                @endif
            </div>
        </div>
    </div>
    @if($tugas->instruksi)
        <div style="margin-top: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px; font-size: 0.85rem; color: #475569; line-height: 1.7;">
            {!! linkify($tugas->instruksi) !!}
        </div>
    @endif
    <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
        @if($tugas->file_lampiran)
            <x-file-link :file="$tugas->file_lampiran" :href="route('lms.file', ['tugas', $tugas->id])" />
        @endif
    </div>
</div>

<div class="table-container">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 0.95rem; font-weight: 600;">
            Pengumpulan Mahasiswa
            <span style="font-weight: 400; color: #64748b; font-size: 0.85rem;">({{ $mahasiswas->count() }} mahasiswa)</span>
        </h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Waktu Kumpul</th>
                <th>File Jawaban</th>
                <th>Nilai</th>
                <th>Catatan Dosen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mahasiswa)
                @php $submission = $submissions->get($mahasiswa->id); @endphp
                <tr>
                    <td>{{ $mahasiswas->firstItem() + $loop->index }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    <td>
                        @if($submission)
                            @if($submission->isTerlambat())
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Terlambat</span>
                            @else
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.7rem; font-weight: 600;">Tepat Waktu</span>
                            @endif
                        @else
                            <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.15rem 0.6rem; font-size: 0.7rem; font-weight: 600;">Belum</span>
                        @endif
                    </td>
                    <td style="font-size: 0.8rem;">
                        {{ $submission ? $submission->dikumpulkan_pada->format('d M Y H:i') : '-' }}
                    </td>
                    <td>
                        @if($submission)
                            <div style="display: flex; gap: 0.3rem;">
                                @if($submission->file_jawaban)
                                    <x-file-link :file="$submission->file_jawaban" compact :href="route('lms.file', ['submission', $submission->id])" />
                                @endif
                                @if($submission->catatan_mahasiswa)
                                    <span style="font-size: 0.7rem; color: #64748b; cursor: help;" title="{{ $submission->catatan_mahasiswa }}">Catatan</span>
                                @endif
                            </div>
                        @else
                            <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($submission && $submission->nilai !== null)
                            <span style="font-weight: 600; color: {{ $submission->nilai >= 60 ? '#059669' : '#dc2626' }};">{{ $submission->nilai }}</span>
                        @elseif($submission)
                            <span style="color: #d97706; font-size: 0.8rem;">Belum Dinilai</span>
                        @else
                            <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                        @endif
                    </td>
                    <td style="font-size: 0.8rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $submission->catatan_dosen ?? '-' }}
                    </td>
                    <td>
                        @if($submission)
                            <button type="button" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;" onclick="document.getElementById('nilai-form-{{ $submission->id }}').classList.toggle('hidden')">Nilai</button>
                        @else
                            <span style="color: #94a3b8; font-size: 0.7rem;">-</span>
                        @endif
                    </td>
                </tr>
                @if($submission)
                    <tr id="nilai-form-{{ $submission->id }}" class="hidden">
                        <td colspan="9" style="padding: 0.75rem 1rem; background: #f8fafc;">
                            <form action="{{ route('lms.submission.nilai', $submission->id) }}" method="POST" style="display: flex; gap: 0.75rem; align-items: flex-end; margin: 0;">
                                @csrf @method('PATCH')
                                <div style="flex: 0 0 100px;">
                                    <label style="font-size: 0.7rem; font-weight: 600; color: #64748b; display: block; margin-bottom: 0.2rem;">Nilai</label>
                                    <input type="number" name="nilai" class="form-input" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;" min="0" max="100" step="0.01" value="{{ old('nilai', $submission->nilai) }}" placeholder="0-100">
                                </div>
                                <div style="flex: 1;">
                                    <label style="font-size: 0.7rem; font-weight: 600; color: #64748b; display: block; margin-bottom: 0.2rem;">Catatan</label>
                                    <input type="text" name="catatan_dosen" class="form-input" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;" value="{{ old('catatan_dosen', $submission->catatan_dosen) }}" placeholder="Catatan untuk mahasiswa">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm">Simpan Nilai</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 0.75rem 1rem; border-top: 1px solid #e2e8f0;">
        {{ $mahasiswas->links() }}
    </div>
</div>

<style>
.hidden { display: none !important; }
</style>

@endsection
