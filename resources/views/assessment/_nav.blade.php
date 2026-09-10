@php
    $current = $current ?? 'monitoring';
    $q = request()->query();
@endphp

<div style="display: flex; gap: 6px; margin-bottom: 1.25rem;">
    <a href="{{ route('assessment.index', $q) }}"
       style="font-size: 0.8rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 999px; text-decoration: none; border: 1px solid {{ $current === 'monitoring' ? '#4f46e5' : '#e2e8f0' }}; background: {{ $current === 'monitoring' ? '#4f46e5' : '#fff' }}; color: {{ $current === 'monitoring' ? '#fff' : '#475569' }};">
        Monitoring
    </a>
    <a href="{{ route('assessment.rekap', $q) }}"
       style="font-size: 0.8rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 999px; text-decoration: none; border: 1px solid {{ $current === 'rekap' ? '#4f46e5' : '#e2e8f0' }}; background: {{ $current === 'rekap' ? '#4f46e5' : '#fff' }}; color: {{ $current === 'rekap' ? '#fff' : '#475569' }};">
        Rekap
    </a>
    <a href="{{ route('assessment.evaluasi', $q) }}"
       style="font-size: 0.8rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 999px; text-decoration: none; border: 1px solid {{ $current === 'evaluasi' ? '#4f46e5' : '#e2e8f0' }}; background: {{ $current === 'evaluasi' ? '#4f46e5' : '#fff' }}; color: {{ $current === 'evaluasi' ? '#fff' : '#475569' }};">
        Evaluasi
    </a>
</div>
