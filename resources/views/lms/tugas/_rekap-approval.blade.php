@php
    $approval = $pengampu->assessment?->approval ?? null;
@endphp

<div style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

    @if($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_MENUNGGU)
        {{-- KONDISI: Sedang Menunggu Persetujuan Kaprodi --}}
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 34px; height: 34px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.88rem; font-weight: 700; color: #92400e;">Nilai Menunggu Persetujuan Kaprodi</div>
                <div style="font-size: 0.75rem; color: #b45309; margin-top: 0.1rem;">
                    Diajukan {{ $approval->diajukan_at?->format('d/m/Y H:i') }} oleh {{ $approval->pengaju?->name ?? 'Dosen Pengampu' }}
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span class="badge badge-diajukan">Menunggu</span>
            <button type="button" disabled title="Pengajuan nilai sedang dalam proses review oleh Kaprodi" style="padding: 0.45rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Sedang Diajukan
            </button>
        </div>

    @elseif($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_DISETUJUI)
        @php $kunciTerbuka = $approval->buka_kunci_at !== null; @endphp
        {{-- KONDISI: Sudah Disetujui Kaprodi (Terkunci/Terbuka Kunci) --}}
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 34px; height: 34px; border-radius: 8px; background: {{ $kunciTerbuka ? '#fef3c7' : '#d1fae5' }}; color: {{ $kunciTerbuka ? '#d97706' : '#059669' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.88rem; font-weight: 700; color: #065f46;">Nilai Telah Disetujui Kaprodi</div>
                <div style="font-size: 0.75rem; color: #059669; margin-top: 0.1rem;">
                    Disetujui {{ $approval->disetujui_at?->format('d/m/Y H:i') }} oleh {{ $approval->penyetuju?->name ?? 'Kaprodi' }} &middot;
                    @if($kunciTerbuka)
                        Kunci dibuka admin, nilai dapat diedit
                    @else
                        Nilai terkunci
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span class="badge badge-disetujui">Disetujui</span>
            @if($kunciTerbuka)
                <button type="button" disabled title="Kunci rekap nilai telah dibuka oleh admin. Dosen dapat mengedit kembali." style="padding: 0.45rem 1rem; border-radius: 8px; border: 1px solid #fde68a; background: #fffbeb; color: #b45309; font-size: 0.8rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    Kunci Dibuka (Admin)
                </button>
            @else
                <button type="button" disabled title="Nilai kelas telah disetujui Kaprodi dan statusnya final" style="padding: 0.45rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Nilai Terkunci
                </button>
            @endif
        </div>

    @else
        {{-- KONDISI: Belum Diajukan ATAU Dikembalikan / Direvisi --}}
        @if($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_DIREVISI)
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: #9f1239;">Nilai Dikembalikan oleh Kaprodi</div>
                    @if($approval->catatan_revisi)
                        <div style="font-size: 0.75rem; color: #be123c; margin-top: 0.1rem;"><strong>Catatan Revisi:</strong> {{ $approval->catatan_revisi }}</div>
                    @endif
                </div>
            </div>
        @else
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #FFF8E0; color: #A16207; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: #1e293b;">Persetujuan Nilai Perkuliahan</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.1rem;">Ajukan rekapitulasi nilai akhir kelas ini ke Kaprodi untuk pengesahan resmi.</div>
                </div>
            </div>
        @endif

        <div style="display: flex; align-items: center; gap: 0.6rem;">
            @if($belumDinilai > 0)
                {{-- PERSYARATAN BELUM LENGKAP: Tombol Abu-abu (Disabled) + Keterangan --}}
                <span style="font-size: 0.73rem; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #d97706;"></span>
                    {{ $belumDinilai }} mahasiswa belum dinilai
                </span>

                <button type="button" disabled title="Persyaratan belum terpenuhi: Lengkapi seluruh nilai mahasiswa sebelum mengajukan ke Kaprodi" style="padding: 0.45rem 1.15rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f1f5f9; color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    {{ $approval ? 'Ajukan Ulang ke Kaprodi' : 'Ajukan ke Kaprodi' }}
                </button>
            @else
                {{-- PERSYARATAN LENGKAP: Tombol Cerah Aktif --}}
                <span style="font-size: 0.73rem; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981;"></span>
                    Nilai lengkap
                </span>

                <x-confirm
                    action="{{ route('lms.nilai.ajukan', $pengampu->id) }}"
                    method="PATCH"
                    title="{{ $approval ? 'Ajukan Ulang Nilai' : 'Ajukan Nilai ke Kaprodi' }}"
                    message="Seluruh nilai mahasiswa kelas ini sudah lengkap. Ajukan nilai ke Kaprodi untuk disetujui?"
                    buttonText="{{ $approval ? 'Ajukan Ulang ke Kaprodi' : 'Ajukan ke Kaprodi' }}"
                    buttonClass="btn btn-primary btn-sm"
                    confirmText="Ya, Ajukan"
                    confirmClass="btn-success"
                />
            @endif
        </div>
    @endif

</div>