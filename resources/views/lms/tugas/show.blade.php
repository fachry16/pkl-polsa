@extends('layouts.lms')

@section('title', $tugas->judul ?? 'Tugas')

@section('content')
<div class="page-container">
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

    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem; color: #1e293b;">
        Pengumpulan Mahasiswa
        <span style="font-weight: 400; color: #64748b; font-size: 0.85rem;">({{ $mahasiswas->count() }} mahasiswa)</span>
    </h3>

    <div class="space-y-3">
        @forelse($mahasiswas as $mahasiswa)
            @php $submission = $submissions->get($mahasiswa->id); @endphp
            <div class="card p-0 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="user-avatar-sm flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                            {{ strtoupper(substr($mahasiswa->nama ?? 'M', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="font-semibold text-sm">{{ $mahasiswa->nama }}</div>
                                <span class="text-xs text-gray-400">NIM: {{ $mahasiswa->nim }}</span>
                            </div>

                            <!-- Status & Waktu Kumpul -->
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                @if($submission)
                                    @if($submission->isTerlambat())
                                        <span class="badge badge-danger">Terlambat</span>
                                    @else
                                        <span class="badge badge-success">Tepat Waktu</span>
                                    @endif
                                    <span class="text-xs text-gray-500">
                                        Dikumpulkan: {{ $submission->dikumpulkan_pada->format('d M Y H:i') }}
                                    </span>
                                @else
                                    <span class="badge badge-draft">Belum Dikumpulkan</span>
                                @endif
                            </div>

                            <!-- File & Catatan -->
                            @if($submission)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg space-y-2">
                                    @if($submission->file_jawaban)
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">File Jawaban:</span>
                                            <x-file-link :file="$submission->file_jawaban" compact :href="route('lms.file', ['submission', $submission->id])" />
                                        </div>
                                    @endif

                                    @if($submission->catatan_mahasiswa)
                                        <div class="flex items-start gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" class="flex-shrink-0 mt-0.5">
                                                <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                                            </svg>
                                            <div class="flex-1">
                                                <span class="text-xs font-semibold text-gray-700">Pesan/Catatan Mahasiswa:</span>
                                                <div class="text-xs text-gray-600 mt-0.5">{{ $submission->catatan_mahasiswa }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($submission->nilai !== null)
                                        <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                                            <span class="text-xs font-semibold text-gray-700">Nilai:</span>
                                            <span class="font-bold text-sm" style="color: {{ $submission->nilai >= 60 ? '#059669' : '#dc2626' }};">{{ $submission->nilai }}</span>
                                            @if($submission->catatan_dosen)
                                                <span class="text-xs text-gray-500">- {{ $submission->catatan_dosen }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Form Penilaian Inline -->
                            @if($submission)
                                <form action="{{ route('lms.submission.nilai', $submission->id) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2 p-3 bg-blue-50 rounded-lg">
                                    @csrf @method('PATCH')
                                    <div class="flex-1 min-w-0" style="min-width: 100px;">
                                        <label class="text-xs font-semibold text-gray-700 block mb-1">Nilai (0-100)</label>
                                        <input type="number" name="nilai" class="form-input" min="0" max="100" step="0.01" value="{{ old('nilai', $submission->nilai) }}" placeholder="0-100" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;">
                                    </div>
                                    <div class="flex-1 min-w-0" style="min-width: 150px;">
                                        <label class="text-xs font-semibold text-gray-700 block mb-1">Catatan</label>
                                        <input type="text" name="catatan_dosen" class="form-input" value="{{ old('catatan_dosen', $submission->catatan_dosen) }}" placeholder="Catatan untuk mahasiswa" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">Simpan Nilai</button>
                                </form>
                            @else
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg text-xs text-gray-500 text-center">
                                    Mahasiswa belum mengumpulkan tugas.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card text-center py-8 text-gray-500">Belum ada mahasiswa di kelas ini.</div>
        @endforelse
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $mahasiswas->links() }}
    </div>
</div>
@endsection
