@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ $tugas->judul }}
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
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
                            <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap;">
                                @if($submission->file_jawaban)
                                    <x-file-link :file="$submission->file_jawaban" compact :href="route('lms.file', ['submission', $submission->id])" />
                                @endif
                                @if($submission->link_jawaban)
                                    <a href="{{ $submission->link_jawaban }}" target="_blank" class="btn btn-secondary btn-xs" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; padding: 0.15rem 0.4rem;" title="{{ $submission->link_jawaban }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        Link
                                    </a>
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
                        <div style="display: flex; gap: 0.35rem; align-items: center;">
                            @if($submission)
                                <button type="button" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;" onclick="document.getElementById('nilai-form-{{ $submission->id }}').classList.toggle('hidden')">Nilai</button>
                            @endif
                            @php
                                $mhsKomentars = $komentarsPribadi->get($mahasiswa->id, collect());
                            @endphp
                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 0.25rem;" onclick="document.getElementById('komentar-pribadi-{{ $mahasiswa->id }}').classList.toggle('hidden')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                <span>Pesan</span>
                                @if($mhsKomentars->count() > 0)
                                    <span style="background: #2563eb; color: #fff; border-radius: 999px; padding: 0 0.35rem; font-size: 0.65rem; font-weight: 700;">{{ $mhsKomentars->count() }}</span>
                                @endif
                            </button>
                        </div>
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
                <tr id="komentar-pribadi-{{ $mahasiswa->id }}" class="hidden">
                    <td colspan="9" style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <div style="max-width: 650px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b; display: flex; align-items: center; gap: 0.4rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    Komentar Pribadi: {{ $mahasiswa->nama }} ({{ $mahasiswa->nim }})
                                </span>
                                <button type="button" class="btn btn-link btn-xs" style="color: #64748b; padding: 0; text-decoration: none;" onclick="document.getElementById('komentar-pribadi-{{ $mahasiswa->id }}').classList.add('hidden')">&times; Tutup</button>
                            </div>

                            {{-- Thread Komentar Pribadi --}}
                            <div style="display: flex; flex-direction: column; gap: 0.6rem; max-height: 250px; overflow-y: auto; margin-bottom: 0.75rem; padding-right: 0.5rem;">
                                @forelse($mhsKomentars as $komentar)
                                    <div style="padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.8rem; {{ $komentar->user?->isDosen() ? 'background: #eff6ff; border: 1px solid #dbeafe; margin-left: 1.5rem;' : 'background: #ffffff; border: 1px solid #e2e8f0; margin-right: 1.5rem;' }}">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.2rem;">
                                            <span style="font-weight: 600; font-size: 0.75rem; color: {{ $komentar->user?->isDosen() ? '#2563eb' : '#1e293b' }};">
                                                {{ $komentar->user?->name }} {{ $komentar->user?->isDosen() ? '(Dosen)' : '' }}
                                            </span>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-size: 0.65rem; color: #94a3b8;">{{ $komentar->created_at->diffForHumans() }}</span>
                                                <form action="{{ route('lms.topik.komentar.destroy', [$pengampu->id, $komentar->id]) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?');" style="margin: 0;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" style="background: none; border: none; padding: 0; color: #ef4444; font-size: 0.75rem; cursor: pointer;" title="Hapus komentar">&times;</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div style="color: #334155; white-space: pre-wrap;">{{ $komentar->pesan }}</div>
                                    </div>
                                @empty
                                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.5rem 0;">Belum ada percakapan pribadi dengan mahasiswa ini.</p>
                                @endforelse
                            </div>

                            {{-- Form Kirim Komentar Pribadi --}}
                            <form action="{{ route('lms.topik.komentar.store', $pengampu->id) }}" method="POST" style="margin: 0; display: flex; gap: 0.5rem;">
                                @csrf
                                <input type="hidden" name="tipe_topik" value="tugas">
                                <input type="hidden" name="topik_id" value="{{ $tugas->id }}">
                                <input type="hidden" name="is_private" value="1">
                                <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                                <input type="text" name="pesan" class="form-input" placeholder="Tulis komentar pribadi untuk {{ $mahasiswa->nama }}..." required style="flex: 1; font-size: 0.8rem; padding: 0.35rem 0.6rem;">
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.35rem 0.75rem;">Kirim</button>
                            </form>
                        </div>
                    </td>
                </tr>
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

{{-- Komentar Kelas (Class Comments) --}}
<div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 1.5rem;">
    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Komentar Kelas ({{ $komentarsKelas->count() }})
    </h3>

    {{-- Form Tambah Komentar --}}
    <form action="{{ route('lms.topik.komentar.store', $pengampu->id) }}" method="POST" style="margin-bottom: 1.5rem;">
        @csrf
        <input type="hidden" name="tipe_topik" value="tugas">
        <input type="hidden" name="topik_id" value="{{ $tugas->id }}">
        <input type="hidden" name="is_private" value="0">
        <div style="display: flex; gap: 0.5rem;">
            <input type="text" name="pesan" class="form-input" placeholder="Tambahkan komentar kelas..." required style="flex: 1; font-size: 0.85rem;">
            <button type="submit" class="btn btn-primary btn-sm">Kirim</button>
        </div>
    </form>

    {{-- List Komentar --}}
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        @forelse($komentarsKelas as $komentar)
            <div style="display: flex; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #f8fafc;">
                <div style="width: 2rem; height: 2rem; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                    {{ substr($komentar->user->name ?? '?', 0, 2) }}
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ $komentar->user->name ?? '-' }}</span>
                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ $komentar->created_at->diffForHumans() }}</span>
                        @if($komentar->user?->isDosen())
                            <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.05rem 0.35rem; font-size: 0.6rem; font-weight: 600;">Dosen</span>
                        @endif
                    </div>
                    <div style="font-size: 0.85rem; color: #334155; margin-top: 0.2rem;">{{ $komentar->pesan }}</div>
                    <div style="margin-top: 0.25rem;">
                        <form action="{{ route('lms.topik.komentar.destroy', [$pengampu->id, $komentar->id]) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?');" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link btn-xs" style="color: #ef4444; padding: 0; font-size: 0.7rem; text-decoration: none;">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; text-align: center; padding: 1rem 0;">Belum ada komentar kelas.</p>
        @endforelse
    </div>
</div>

<style>
.hidden { display: none !important; }
</style>

@endsection
