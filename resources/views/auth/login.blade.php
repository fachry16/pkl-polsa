<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .login-card {
        width: 100%;
        max-width: 820px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(79,70,229,0.1);
        overflow: hidden;
        display: flex;
    }

    .login-brand {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        position: relative;
        overflow: hidden;
    }

    .login-brand::before {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: rgba(79,70,229,0.04);
        top: -100px;
        right: -100px;
    }

    .login-brand::after {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: rgba(79,70,229,0.03);
        bottom: -80px;
        left: -80px;
    }

    .login-brand .brand-icon {
        width: 72px;
        height: 72px;
        background: #fff;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(79,70,229,0.12);
        position: relative;
        z-index: 1;
    }

    .login-brand h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        margin-top: 1.25rem;
        position: relative;
        z-index: 1;
    }

    .login-brand p {
        font-size: 0.85rem;
        color: #64748b;
        text-align: center;
        max-width: 320px;
        margin-top: 0.4rem;
        position: relative;
        z-index: 1;
    }

    .login-form-wrap {
        flex: 0 0 380px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2.5rem 2rem;
    }

    .login-form-wrap .form-header {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .login-form-wrap .form-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4f46e5;
    }

    .login-form-wrap .form-group { margin-bottom: 1.15rem; }

    .login-form-wrap .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.3rem;
        font-size: 0.85rem;
        color: #374151;
    }

    .login-form-wrap .form-input {
        padding: 0.6rem 0.75rem;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.88rem;
        background: #f8fafc;
        transition: all 0.15s;
        font-family: inherit;
        color: #1e293b;
    }

    .login-form-wrap .form-input:focus {
        outline: none;
        border-color: #4f46e5;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.08);
    }

    .login-form-wrap .form-error {
        color: #dc2626;
        font-size: 0.78rem;
        margin-top: 0.2rem;
    }

    @media (max-width: 768px) {
        body { padding: 1rem; }
        .login-card { flex-direction: column; max-width: 420px; border-radius: 12px; }
        .login-brand { padding: 2rem 1.5rem; min-height: 200px; }
        .login-form-wrap { flex: 1; padding: 1.75rem 1.25rem; }
    }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon">
                <svg width="34" height="34" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 4L26 10V18L16 24L6 18V10L16 4Z" fill="#eef2ff" stroke="#4f46e5" stroke-width="1.5"/>
                    <path d="M16 24L26 18V22L16 28L6 22V18L16 24Z" fill="#e0e7ff" stroke="#4f46e5" stroke-width="1.5"/>
                    <path d="M16 14L21 11V15L16 18L11 15V11L16 14Z" fill="#4f46e5" opacity="0.3"/>
                </svg>
            </div>
            <h2>PIKOBE Polsa</h2>
            <p>Sistem Informasi Kurikulum &amp; Perkuliahan Digital (LMS) — Politeknik Sawunggalih Aji</p>
        </div>

        <div class="login-form-wrap">
            <div class="form-header">
                <h1>Login</h1>
            </div>

            <x-alert type="success" :message="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh@email.com">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="remember" style="accent-color: #4f46e5;">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem;">
                    @if (Route::has('password.request'))
                        <a style="font-size: 0.85rem; color: #4f46e5; font-weight: 500; text-decoration: none;" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif

                    <button type="submit" style="background: #4f46e5; color: #fff; font-weight: 600; padding: 0.65rem 1.75rem; font-size: 0.9rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s; font-family: inherit;"
                            onmouseover="this.style.background='#4338ca'"
                            onmouseout="this.style.background='#4f46e5'">
                        Masuk
                    </button>
                </div>
        </form>

            <div style="margin-top: 1.25rem; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                <a href="{{ url('/') }}" style="font-size: 0.82rem; color: #94a3b8; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
