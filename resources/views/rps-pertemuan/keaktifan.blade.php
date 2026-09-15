@extends('layouts.app')

@section('content')

<div class="page-header">
    Keaktifan Materi Pertemuan
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $rps->mataKuliah?->kode ?? '-' }} - {{ $rps->mataKuliah?->nama ?? '-' }} &middot; Minggu Ke-{{ $pertemuan->minggu }}
    </span>
</div>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.pertemuan.index', $rps->id) }}" class="btn btn-secondary">Kembali ke Pertemuan</a>
</div>

{{-- Info Pertemuan --}}
<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Pertemuan</span>
        <span style="font-size: 0.7rem; background: #dbeafe; color: #1d4ed8; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;">Minggu {{ $pertemuan->minggu }}</span>
    </div>
    <div style="font-size: 0.82rem; color: #334155; line-height: 1.6;">
        <div><strong>Sub CPMK:</strong> {{ $pertemuan->sub_cpmk }}</div>
        <div style="margin-top: 0.3rem;"><strong>Materi:</strong> {{ $pertemuan->materi }}</div>
        <div style="margin-top: 0.3rem;">
            <strong>File Materi RPS:</strong>
            @if($pertemuan->file_materi)
                <a href="{{ Storage::url($pertemuan->file_materi) }}" target="_blank" style="color: #16a34a; text-decoration: underline;">Unduh Materi</a>
            @else
                <span style="color: #94a3b8;">Belum diunggah</span>
            @endif
        </div>
    </div>
</div>

@forelse($kelasData as $kelas)

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">

        {{-- Header Kelas --}}
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div>
                <span style="font-weight: 700; color: #1e293b;">Kelas {{ $kelas['pengampu']->kelas ?? '-' }}</span>
                <span style="font-size: 0.78rem; color: #64748b; margin-left: 0.5rem;">
                    {{ $kelas['pengampu']->nama_dosen }}
                </span>
                <span style="font-size: 0.75rem; color: #94a3b8; margin-left: 0.5rem;">
                    {{ $kelas['pengampu']->label_semester }}
                </span>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <span style="font-size: 0.72rem; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 600;">
                    Total {{ $kelas['total'] }} Mahasiswa
                </span>
                <span style="font-size: 0.72rem; background: #dcfce7; border: 1px solid #bbf7d0; color: #15803d; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 600;">
                    Sudah Lihat {{ $kelas['sudah'] }}
                </span>
                <span style="font-size: 0.72rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 600;">
                    Belum Lihat {{ $kelas['total'] - $kelas['sudah'] }}
                </span>
            </div>
        </div>

        {{-- Tabel Mahasiswa --}}
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th style="text-align: center;">Status</th>
                        <th>Dibaca Pada</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($kelas['rows'] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['mahasiswa']->nim }}</td>
                        <td>{{ $row['mahasiswa']->nama }}</td>
                        <td style="text-align: center;">
                            @if($row['dibaca_pada'])
                                <span style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Sudah Lihat</span>
                            @else
                                <span style="display: inline-block; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Belum</span>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem; color: #64748b;">
                            {{ $row['dibaca_pada'] ? $row['dibaca_pada']->format('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@empty

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; color: #64748b;">
        <div style="font-weight: 600; color: #1e293b; margin-bottom: 0.3rem;">Belum ada data keaktifan</div>
        <div style="font-size: 0.85rem;">Materi LMS belum diunggah atau belum tertaut ke pertemuan ini. Pastikan dosen mengunggah materi di kelas LMS dan menautkannya ke pertemuan RPS minggu {{ $pertemuan->minggu }}.</div>
    </div>

@endforelse

@endsection