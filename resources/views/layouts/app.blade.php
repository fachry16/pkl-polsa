<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-layout">

    @auth
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
        @yield('content')
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
