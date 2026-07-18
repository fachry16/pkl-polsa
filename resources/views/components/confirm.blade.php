@props([
    'action',
    'method' => 'POST',
    'message' => 'Yakin ingin melanjutkan?',
    'subMessage' => '',
    'buttonText' => 'Hapus',
    'buttonClass' => 'btn btn-danger btn-sm',
    'confirmText' => 'Ya, Hapus',
    'confirmClass' => 'btn-danger',
    'title' => 'Konfirmasi',
])

@php
    $isDanger = $confirmClass === 'btn-danger';
    $isSuccess = $confirmClass === 'btn-success';
    $variant = $isDanger ? 'danger' : ($isSuccess ? 'success' : 'default');
@endphp

<div x-data="{ open: false }" class="inline">
    <button type="button"
            @click="open = true"
            class="{{ $buttonClass }}">
        {{ $buttonText }}
    </button>

    <div x-show="open"
         x-transition:enter.duration.100ms
         x-transition:leave.duration.100ms
         class="confirm-overlay"
         @click.self="open = false"
         style="display: none;">
        <div class="confirm-modal confirm-{{ $variant }}" @click.stop>
            <div class="confirm-body">
                <div class="confirm-icon-wrap">
                    @if($isDanger)
                    <div class="confirm-icon confirm-icon-danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="13"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    @elseif($isSuccess)
                    <div class="confirm-icon confirm-icon-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    @else
                    <div class="confirm-icon confirm-icon-warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <h3 class="confirm-title">{{ $title }}</h3>
                <p class="confirm-message">{{ $message }}</p>
                @if($subMessage)
                <p class="confirm-sub-message">{{ $subMessage }}</p>
                @endif
            </div>
            <div class="confirm-footer">
                <button type="button" @click="open = false" class="btn-confirm btn-confirm-cancel">Batal</button>
                <form action="{{ $action }}" method="POST" class="inline">
                    @csrf
                    @method($method)
                    {{ $slot }}
                    <button type="submit" class="btn-confirm btn-confirm-{{ $variant }}">
                        @if($isDanger)
                        <svg class="btn-confirm-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        @elseif($isSuccess)
                        <svg class="btn-confirm-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                        {{ $confirmText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
