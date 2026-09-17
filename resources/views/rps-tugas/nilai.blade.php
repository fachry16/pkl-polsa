@extends('layouts.app')

@section('content')

<div class="page-header">
    Nilai Tugas &amp; Latihan
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $rps->mataKuliah?->kode ?? '-' }} - {{ $rps->mataKuliah?->nama ?? '-' }}
    </span>
</div>

<div class="mb-5 btn-group">
    <a href="{{ route('rps.tugas.index', $rps->id) }}" class="btn btn-secondary">Kembali ke Tugas &amp; Latihan</a>
</div>

{{-- Info Tugas --}}
<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Tugas</span>
        <span style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600; text-transform: uppercase;">{{ $tugas->kategori_komponen ?? 'tugas' }}</span>
    </div>
    <div style="font-size: 0.82rem; color: #334155; line-height: 1.6;">
        <div><strong>Nama:</strong> {{ $tugas->nama_tugas }}</div>
        <div style="margin-top: 0.3rem;"><strong>Minggu/Topik:</strong> {{ $tugas->minggu_topik ?? '-' }} &middot; <strong>Sub-CPMK:</strong> {{ $tugas->sub_cpmk ?? '-' }}</div>
        <div style="margin-top: 0.3rem;"><strong>Batas Waktu:</strong> {{ $tugas->batas_waktu ?? '-' }} &middot; <strong>Deadline LMS:</strong> {{ $tugas->deadline?->format('d M Y, H:i') ?? 'Belum diatur' }}</div>
        <div style="margin-top: 0.3rem;"><strong>Bobot Nilai:</strong> {{ $tugas->bobot_nilai ?? 100 }}</div>
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
                    Mengumpulkan {{ $kelas['mengumpulkan'] }}
                </span>
                <span style="font-size: 0.72rem; background: #fef9c3; border: 1px solid #fde047; color: #854d0e; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 600;">
                    Dinilai {{ $kelas['dinilai'] }}
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
                        <th style="text-align: center;">Status Pengumpulan</th>
                        <th style="text-align: center;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($kelas['rows'] as $row)
                    @php
                        $submission = $row['submission'];
                        $terlambat = $submission && $kelas['lmsTugas']->deadline && $submission->dikumpulkan_pada->gt($kelas['lmsTugas']->deadline);
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['mahasiswa']->nim }}</td>
                        <td>{{ $row['mahasiswa']->nama }}</td>
                        <td style="text-align: center;">
                            @if($submission && $terlambat)
                                <span style="display: inline-block; background: #fef9c3; color: #854d0e; border: 1px solid #fde047; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Terlambat</span>
                            @elseif($submission)
                                <span style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Mengumpulkan</span>
                            @else
                                <span style="display: inline-block; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Belum</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($submission?->nilai !== null)
                                <span style="font-weight: 600; color: {{ $submission->nilai >= 60 ? '#059669' : '#dc2626' }};">{{ $submission->nilai }}</span>
                            @elseif($submission)
                                <span style="color: #d97706; font-size: 0.78rem;">Belum Dinilai</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@empty

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; color: #64748b;">
        <div style="font-weight: 600; color: #1e293b; margin-bottom: 0.3rem;">Belum ada data nilai</div>
        <div style="font-size: 0.85rem;">Belum ada kelas dengan pengumpulan tugas. Tugas ini perlu diunggah ke kelas LMS dan ditugaskan agar mahasiswa dapat mengumpulkannya.</div>
    </div>

@endforelse

@endsection