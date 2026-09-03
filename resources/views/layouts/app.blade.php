<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-layout"
     x-data="{
         isMobile: window.innerWidth < 768,
         sidebarOpen: window.innerWidth >= 768 ? (localStorage.getItem('sidebar_open') !== 'false') : false,
         toggleSidebar() {
             this.sidebarOpen = !this.sidebarOpen;
             if (!this.isMobile) {
                 localStorage.setItem('sidebar_open', this.sidebarOpen);
             }
         },
         init() {
             window.addEventListener('resize', () => {
                 this.isMobile = window.innerWidth < 768;
             });
         }
     }"
     @toggle-sidebar.window="toggleSidebar()"
     :class="{ 'sidebar-collapsed': !sidebarOpen, 'sidebar-open': sidebarOpen }">

    @auth
        {{-- Mobile Backdrop Overlay --}}
        <div x-show="sidebarOpen && isMobile"
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 90; backdrop-filter: blur(2px);">
        </div>
        @if(auth()->user()->isAdmin())
            @include('layouts.sidebar.admin')
        @elseif(auth()->user()->isDirektur())
            @include('layouts.sidebar.direktur')
        @elseif(auth()->user()->isKaprodi())
            @include('layouts.sidebar.kaprodi')
        @elseif(auth()->user()->isMahasiswa())
            @include('layouts.sidebar.mahasiswa')
        @else
            @include('layouts.sidebar.dosen')
        @endif
    @endauth

    <main class="main-content">
        @auth
            @include('layouts.partials.global-header')
        @endauth
        <div class="page-content-wrapper">
            @yield('content')
        </div>
    </main>

    <div class="toast-container">
        @if(session('toast_success'))
            <x-toast type="success" :message="session('toast_success')" />
        @endif
        @if(session('toast_error'))
            <x-toast type="error" :message="session('toast_error')" />
        @endif
        @if(session('toast_warning'))
            <x-toast type="warning" :message="session('toast_warning')" />
        @endif
        @if(session('toast_info'))
            <x-toast type="info" :message="session('toast_info')" />
        @endif
        @stack('toasts')
    </div>

    @stack('scripts')

</div>

</body>
</html>
