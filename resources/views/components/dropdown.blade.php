@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'left-0',
    'top' => 'bottom-full',
    default => 'right-0',
};
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            class="dropdown-menu {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="dropdown-menu-content {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
