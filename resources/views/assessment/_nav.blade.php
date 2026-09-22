@php
    $current = $current ?? 'monitoring';
    $q = request()->query();
@endphp

<div style="display: flex; gap: 6px; margin-bottom: 1.25rem;">
    <a href="{{ route('assessment.index', $q) }}"
       style="font-size: 0.8rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 999px; text-decoration: none; border: 1px solid {{ $current === 'monitoring' ? '#A16207' : '#e2e8f0' }}; background: {{ $current === 'monitoring' ? '#A16207' : '#fff' }}; color: {{ $current === 'monitoring' ? '#fff' : '#475569' }};">
        Monitoring
    </a>
    <a href="{{ route('assessment.rekap', $q) }}"
       style="font-size: 0.8rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 999px; text-decoration: none; border: 1px solid {{ $current === 'rekap' ? '#A16207' : '#e2e8f0' }}; background: {{ $current === 'rekap' ? '#A16207' : '#fff' }}; color: {{ $current === 'rekap' ? '#fff' : '#475569' }};">
        Rekap
    </a>
</div>
