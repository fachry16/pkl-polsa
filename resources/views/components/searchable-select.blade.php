@props([
    'name',
    'options',
    'value' => null,
    'placeholder' => 'Pilih data',
    'searchPlaceholder' => null,
    'allowEmpty' => false,
    'emptyLabel' => 'Semua',
    'autoSubmit' => false,
    'required' => false,
    'inputClass' => 'form-input',
    'id' => null,
])

@php
    $uid = $id ?? 'ss-'.\Illuminate\Support\Str::random(8);

    $items = collect($options)->map(function ($option) {
        $value = is_array($option) ? ($option['value'] ?? $option['id'] ?? '') : $option;
        $label = is_array($option) ? ($option['label'] ?? $value) : $option;
        $extra = is_array($option) ? ($option['search'] ?? '') : '';

        return [
            'value' => (string) $value,
            'label' => (string) $label,
            'search' => mb_strtolower(trim($label.' '.$extra)),
        ];
    })->values();

    if ($allowEmpty) {
        $items->prepend(['value' => '', 'label' => $emptyLabel, 'search' => mb_strtolower($emptyLabel)]);
    }

    $settings = ['allowEmpty' => $allowEmpty, 'autoSubmit' => $autoSubmit, 'required' => $required];
@endphp

<div x-data="searchableSelect({{ Js::from($items) }}, {{ Js::from($value) }}, {{ Js::from($settings) }})"
     @click.outside="close()"
     class="search-select">
    <input type="hidden" name="{{ $name }}" :value="selected ? selected.value : ''">

    <input type="text"
           id="{{ $uid }}"
           x-model="query"
           @focus="onFocus($event)"
           @input="onInput()"
           @keydown.arrow-down.prevent="move(1)"
           @keydown.arrow-up.prevent="move(-1)"
           @keydown.enter.prevent="chooseActive()"
           @keydown.escape="close()"
           @keydown.tab="close()"
           class="{{ $inputClass }} search-select-input"
           placeholder="{{ $searchPlaceholder ?? $placeholder }}"
           autocomplete="off"
           role="combobox"
           aria-autocomplete="list"
           aria-controls="{{ $uid }}-menu"
           :aria-expanded="open.toString()"
           :aria-activedescendant="open && filtered[active] ? '{{ $uid }}-opt-' + active : null"
           @if($required) aria-required="true" @endif>

    <button type="button"
            x-show="selected"
            x-cloak
            class="search-select-clear"
            aria-label="Hapus pilihan"
            @click="clear()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>

    <div x-show="open" x-cloak id="{{ $uid }}-menu" class="search-select-menu" role="listbox">
        <template x-for="(item, index) in filtered" :key="item.value">
            <button type="button"
                    class="search-select-option"
                    :class="{ 'is-active': index === active }"
                    :id="'{{ $uid }}-opt-' + index"
                    @mouseenter="active = index"
                    @click="choose(item)"
                    role="option"
                    :aria-selected="(selected && selected.value === item.value).toString()">
                <span x-text="item.label"></span>
            </button>
        </template>
        <div x-show="filtered.length === 0" class="search-select-empty">Data tidak ditemukan.</div>
    </div>
</div>
