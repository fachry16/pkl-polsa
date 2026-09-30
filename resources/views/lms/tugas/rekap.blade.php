@extends('layouts.app')

@section('content')

<div class="page-header">
    Rekap Nilai Perkuliahan &amp; LMS
    <span style="font-size: 0.85rem; font-weight: 400; color: #64748b; display: block; margin-top: 0.2rem;">
        {{ $pengampu->mataKuliah->kode ?? '' }} - {{ $pengampu->mataKuliah->nama ?? '' }} &middot; Kelas {{ $pengampu->kelas ?? '-' }}
    </span>
</div>

<div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('lms.tugas.index', $pengampu->id) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas</a>
        @if(!$terkunci)
            <form action="{{ route('lms.tugas.sync', $pengampu->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Hitung Ulang Nilai</button>
            </form>
        @endif
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('lms.tugas.rekap.export', array_filter(['pengampu' => $pengampu->id, 'format' => 'print'])) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Export Print
        </a>
        <a href="{{ route('lms.tugas.rekap.export', ['pengampu' => $pengampu->id, 'format' => 'pdf']) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
        </a>
        <a href="{{ route('lms.tugas.rekap.export', ['pengampu' => $pengampu->id, 'format' => 'excel']) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line></svg>
            Excel
        </a>
    </div>
</div>

@include('lms.tugas._rekap-approval', ['belumDinilai' => $belumDinilai])

@if(Auth::user()->isAdmin())
    @php $approvalAdmin = $pengampu->assessment?->approval ?? null; @endphp
    @if($approvalAdmin && $approvalAdmin->status === \App\Models\AssessmentApproval::STATUS_DISETUJUI)
        <div style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #FFF8E0; color: #A16207; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: #1e293b;">Kunci Rekap Nilai (Admin)</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.1rem;">
                        @if($approvalAdmin->buka_kunci_at !== null)
                            Terbuka sejak {{ $approvalAdmin->buka_kunci_at?->format('d/m/Y H:i') }} oleh {{ $approvalAdmin->pembukaKunci?->name ?? 'Admin' }} &middot; Dosen dapat mengedit
                        @else
                            Terkunci &middot; Dosen tidak dapat mengedit nilai
                        @endif
                    </div>
                </div>
            </div>
            <form action="{{ route('lms.nilai.kunci', $pengampu->id) }}" method="POST" style="margin: 0;">
                @csrf
                @method('PATCH')
                @if($approvalAdmin->buka_kunci_at !== null)
                    <button type="submit" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Kunci Kembali
                    </button>
                @else
                    <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Buka Kunci
                    </button>
                @endif
            </form>
        </div>
    @endif
@endif

@if(!$terkunci)
<form action="{{ route('lms.tugas.komponen', $pengampu->id) }}" method="POST">
    @csrf
@endif
<div class="table-container" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                @foreach($tugasList as $tugas)
                    <th style="text-align: center; font-size: 0.7rem;">{{ Str::limit($tugas->judul, 15) }}</th>
                @endforeach
                <th style="text-align: center; font-weight: 700;">Nilai Tugas</th>
                @foreach($bobot as $komponen => $persen)
                    @if($komponen !== 'tugas' && ($persen > 0 || in_array($komponen, ['absensi', 'keaktifan', 'etika'])))
                        <th style="text-align: center; font-size: 0.7rem;">{{ ucfirst($komponen) }}<br><small style="color:#94a3b8;">({{ $persen }}%)</small>@if($komponen === 'absensi')<br><small style="color:#94a3b8;">auto kehadiran</small>@endif</th>
                    @endif
                @endforeach
                <th style="text-align: center; font-weight: 700; background: #f8fafc;">Nilai Angka</th>
                <th style="text-align: center; font-weight: 700; background: #f8fafc;">Nilai Huruf (NA)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mahasiswa)
                @php
                    $nilaiTugas = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'tugas')?->nilai;
                    $nilaiAkhir = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', 'akhir')?->nilai;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    @foreach($tugasList as $tugas)
                        @php
                            $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                            $nilaiSub = $submission?->nilai;
                        @endphp
                        <td style="text-align: center;">
                            @if($nilaiSub !== null)
                                <span style="font-weight: 600; color: {{ $nilaiSub >= 60 ? '#059669' : '#dc2626' }};">{{ $nilaiSub }}</span>
                            @elseif($submission)
                                <span style="color: #d97706; font-size: 0.75rem;">Blm Dinilai</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: 700;">
                        {{ $nilaiTugas !== null ? number_format($nilaiTugas, 2) : '-' }}
                    </td>
                    @foreach($bobot as $komponen => $persen)
                        @if($komponen !== 'tugas' && ($persen > 0 || in_array($komponen, ['absensi', 'keaktifan', 'etika'])))
                            @php
                                if ($komponen === 'absensi') {
                                    $nilaiKomponen = app(App\Services\PenilaianService::class)->hitungAbsensi($pengampu, $mahasiswa);
                                } else {
                                    $nilaiKomponen = $nilaiByMhs->get($mahasiswa->id)?->firstWhere('komponen', $komponen)?->nilai;
                                }
                            @endphp
                            <td style="text-align: center;">
                                @if($komponen === 'absensi')
                                    <input type="number" value="{{ $nilaiKomponen !== null ? $nilaiKomponen : '' }}" min="0" max="100" step="0.01" readonly tabindex="-1" title="Otomatis dari data kehadiran presensi kelas" style="width: 65px; text-align: center; padding: 0.2rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.8rem; background: #f8fafc; color: #64748b;">
                                @elseif(!$terkunci)
                                    <input type="number" name="nilai[{{ $mahasiswa->id }}][{{ $komponen }}]" value="{{ $nilaiKomponen !== null ? $nilaiKomponen : '' }}" min="0" max="100" step="0.01" placeholder="-" style="width: 65px; text-align: center; padding: 0.2rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.8rem;">
                                @else
                                    {{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2) : '-' }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                    <td style="text-align: center; font-weight: 700; background: #f8fafc;">
                        {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}
                    </td>
                    <td style="text-align: center; background: #f8fafc;">
                        @if($nilaiAkhir !== null)
                            @php
                                $huruf = konversiNilaiHurufPolsa($nilaiAkhir);
                                $badgeStyle = match($huruf) {
                                    'A', 'A-' => 'background: #ecfdf5; color: #059669; border-color: #a7f3d0;',
                                    'B+', 'B' => 'background: #FFF8E0; color: #B8860B; border-color: #FFE88F;',
                                    'B-', 'C+', 'C' => 'background: #fefce8; color: #a16207; border-color: #fde047;',
                                    'C-', 'D' => 'background: #fff7ed; color: #c2410c; border-color: #fdba74;',
                                    default => 'background: #fef2f2; color: #b91c1c; border-color: #fecaca;',
                                };
                            @endphp
                            <span style="{{ $badgeStyle }} border-width: 1px; border-style: solid; padding: 0.2rem 0.55rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; display: inline-block;">
                                {{ $huruf }}
                            </span>
                        @else
                            <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + $tugasList->count() + collect($bobot)->except('tugas')->filter(fn ($p, $k) => $p > 0 || in_array($k, ['absensi', 'keaktifan', 'etika']))->count() }}" class="text-center" style="padding: 2rem; color: #94a3b8;">Belum ada mahasiswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(!$terkunci)
<div style="margin-top: 1rem; text-align: right;">
    <button type="submit" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.5rem 1.25rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan Component &amp; Kirim ke Asesmen OBE
    </button>
</div>
</form>
@endif

@if($cpmkConfig->isNotEmpty() && !$terkunci)
<div style="margin-top: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">Pemetaan Instrumen → CPMK</h3>
    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">Konfigurasi komponen penilaian yang berkontribusi ke tiap CPMK. Jika belum dikonfigurasi, sync akan menggunakan nilai akhir secara proporsional.</p>

    @foreach($cpmkConfig as $cpmkId => $meta)
        <div style="padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
            <div style="font-size: 0.82rem; font-weight: 600; color: #374151;">{{ $meta['cpmk']->kode_cpmk }} — {{ $meta['cpmk']->deskripsi }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.3rem;">Bobot: {{ $meta['bobot'] }} | CPL: {{ $meta['cpl']->kode_cpl }}</div>
            @php $mapped = $instrumenCpmk->where('cpmk_id', $cpmkId); @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center;">
                @foreach($mapped as $map)
                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.5rem; background: #FFF3C4; border: 1px solid #FFE88F; border-radius: 6px; font-size: 0.75rem; color: #A16207;">
                        {{ ucfirst($map->komponen) }} ({{ $map->bobot_kontribusi }}%)
                        <form action="{{ route('lms.tugas.instrumen-cpmk.hapus', [$pengampu->id, $map->id]) }}" method="POST" style="margin:0; display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; cursor:pointer; color:#dc2626; font-size:0.7rem; padding:0;">x</button>
                        </form>
                    </span>
                @endforeach
                <form action="{{ route('lms.tugas.instrumen-cpmk', $pengampu->id) }}" method="POST" style="display: inline-flex; gap: 0.3rem; align-items: center;">
                    @csrf
                    <input type="hidden" name="cpmk_id" value="{{ $cpmkId }}">
                    <select name="komponen" required style="font-size: 0.75rem; padding: 0.2rem 0.4rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                        <option value="">Komponen</option>
                        @foreach(['tugas','quiz','uts','uas','praktikum','project','absensi','keaktifan','etika'] as $k)
                            <option value="{{ $k }}">{{ ucfirst($k) }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="bobot_kontribusi" value="100" min="0" max="100" required style="width: 50px; font-size: 0.75rem; padding: 0.2rem 0.4rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <button type="submit" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; background: #FEC200; color: #0A0D40; border: none; border-radius: 4px; cursor: pointer;">+</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif

<div style="margin-top: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Rekap &amp; Export Asesmen OBE Kelas</h3>
            <p style="font-size: 0.8rem; color: #64748b; margin: 0.2rem 0 0 0;">
                Capaian CPMK, nilai mata kuliah, dan capaian CPL untuk kelas ini.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('assessment.rekap', ['pengampu_id' => $pengampu->id]) }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Rekap Asesmen OBE
            </a>
            <a href="{{ route('assessment.export', ['pengampu_id' => $pengampu->id, 'format' => 'print']) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print
            </a>
            <a href="{{ route('assessment.export', ['pengampu_id' => $pengampu->id, 'format' => 'pdf']) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                PDF
            </a>
            <a href="{{ route('assessment.export', ['pengampu_id' => $pengampu->id, 'format' => 'excel']) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                Excel
            </a>
        </div>
    </div>
</div>

@endsection
