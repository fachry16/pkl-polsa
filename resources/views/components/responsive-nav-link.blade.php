@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full nav-link nav-link-active'
            : 'block w-full nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
