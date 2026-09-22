<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wajib Ganti Password - {{ config('app.name', 'Eduva') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/eduva/eduva-logo.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .auth-header {
            padding: 2rem 2rem 1.25rem 2rem;
            text-align: center;
        }

        .auth-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #FFF3C4;
            color: #A16207;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .auth-desc {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.5;
        }

        .user-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            margin: 0 2rem 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .auth-body {
            padding: 0 2rem 2rem 2rem;
        }

        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .form-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            border-color: #A16207;
            box-shadow: 0 0 0 3px rgba(254, 194, 0, 0.12);
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: #FEC200;
            color: #0A0D40;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #D9A500;
        }

        .auth-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            background: #f8fafc;
        }

        .btn-logout {
            background: none;
            border: none;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-logout:hover {
            color: #dc2626;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 class="auth-title">Perbarui Password Akun Anda</h1>
            <p class="auth-desc">Demi keamanan akun Anda, silakan ganti password bawaan/default sistem sebelum melanjutkan penggunaan aplikasi.</p>
        </div>

        <div class="user-pill">
            <div>
                <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">{{ $user->name }}</div>
                <div style="font-size: 0.72rem; color: #64748b;">{{ $user->email }}</div>
            </div>
            <span style="font-size: 0.7rem; background: #FFF3C4; color: #A16207; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600; text-transform: uppercase;">
                {{ $user->role }}
            </span>
        </div>

        <div class="auth-body">
            @if ($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem;">
                    <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.8rem; color: #b91c1c;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.ganti-wajib.update') }}">
                @csrf

                <div class="form-group">
                    <label for="password" class="form-label">Password Baru <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password" name="password" class="form-input" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Gunakan kombinasi huruf, angka, atau simbol.</div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="Ulangi password baru">
                </div>

                <button type="submit" class="btn-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Simpan Password &amp; Lanjutkan
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>

</body>
</html>
