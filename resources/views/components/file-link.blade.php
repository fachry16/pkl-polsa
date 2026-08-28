@props(['file', 'compact' => false, 'href' => null])

@php
    $nama = basename($file);
    $ekstensi = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
    $url = $href ?? Storage::url($file);
@endphp

@if($file)
    <a href="{{ $url }}" target="_blank" rel="noopener"
       class="btn btn-secondary btn-sm"
       style="padding: 0.25rem 0.6rem; font-size: 0.75rem; {{ $compact ? 'padding: 0.2rem 0.5rem; font-size: 0.7rem;' : '' }}">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; vertical-align: -2px; margin-right: 0.25rem;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
        {{ $nama }}
        @if($ekstensi)
            <span style="background: #eef2ff; color: #4f46e5; border-radius: 4px; padding: 0.05rem 0.35rem; font-size: 0.6rem; font-weight: 700; margin-left: 0.35rem; text-transform: uppercase;">{{ $ekstensi }}</span>
        @endif
    </a>
@endif
