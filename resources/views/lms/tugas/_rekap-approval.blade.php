@php
    $approval = $pengampu->assessment?->approval ?? null;
@endphp

@if(!Auth::user()->isAdmin())
<div style="margin-bottom: 1rem; padding: 0.85rem 1.1rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">

    @if($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_MENUNGGU)
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #b45309;">
            <span style="width: 26px; height: 26px; border-radius: 8px; background: #fef3c7; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </span>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #92400e;">Nilai Menunggu Persetujuan Kaprodi</div>
                <div style="font-size: 0.75rem; color: #b45309;">Diajukan {{ $approval->diajukan_at?->format('d/m/Y H:i') }} oleh {{ $approval->pengaju?->name ?? 'Dosen' }}</div>
            </div>
        </div>
        <span class="badge badge-diajukan">Menunggu</span>

    @elseif($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_DISETUJUI)
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #059669;">
            <span style="width: 26px; height: 26px; border-radius: 8px; background: #d1fae5; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #065f46;">Nilai Disetujui Kaprodi</div>
                <div style="font-size: 0.75rem; color: #059669;">Disetujui {{ $approval->disetujui_at?->format('d/m/Y H:i') }} oleh {{ $approval->penyetuju?->name ?? 'Kaprodi' }}</div>
            </div>
        </div>
        <span class="badge badge-disetujui">Disetujui</span>

    @else
        @if($approval && $approval->status === \App\Models\AssessmentApproval::STATUS_DIREVISI)
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #e11d48;">
                <span style="width: 26px; height: 26px; border-radius: 8px; background: #ffe4e6; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #9f1239;">Nilai Dikembalikan Kaprodi</div>
                    @if($approval->catatan_revisi)
                        <div style="font-size: 0.75rem; color: #be123c;">Catatan: {{ $approval->catatan_revisi }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if($belumDinilai > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button type="button" disabled style="padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f1f5f9; color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: not-allowed;">
                    Ajukan ke Kaprodi
                </button>
                <span style="font-size: 0.75rem; color: #d97706; font-weight: 600;">{{ $belumDinilai }} mahasiswa belum dinilai</span>
            </div>
        @else
            <x-confirm
                action="{{ route('lms.nilai.ajukan', $pengampu->id) }}"
                method="PATCH"
                title="{{ $approval ? 'Ajukan Ulang Nilai' : 'Ajukan Nilai ke Kaprodi' }}"
                message="Seluruh nilai mahasiswa kelas ini sudah lengkap. Ajukan nilai ke Kaprodi untuk disetujui?"
                buttonText="Ajukan ke Kaprodi"
                buttonClass="btn btn-primary"
                confirmText="Ya, Ajukan"
                confirmClass="btn-success"
            />
        @endif
    @endif

</div>
@endif