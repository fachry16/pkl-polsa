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
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: radial-gradient(circle at top, #FFF8DC, #F8F9FA 55%);
            color: #0f172a;
            line-height: 1.5;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        .gp-card {
            margin: auto;
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 32px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }

        .gp-header {
            padding: 2.25rem 2rem 1.5rem 2rem;
            text-align: center;
        }

        .gp-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #FFF3C4;
            color: #A16207;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: inset 0 0 0 1px rgba(161, 98, 7, 0.12);
        }

        .gp-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #0f172a;
            margin-bottom: 0.4rem;
            line-height: 1.3;
        }

        .gp-desc {
            font-size: 0.85rem;
            color: #64748b;
            max-width: 34ch;
            margin: 0 auto;
        }

        .gp-user {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin: 0 2rem 1.5rem 2rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
            gap: 0.75rem;
        }

        .gp-user-identity {
            min-width: 0;
        }

        .gp-user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #0f172a;
            overflow-wrap: anywhere;
        }

        .gp-user-email {
            font-size: 0.75rem;
            color: #64748b;
            overflow-wrap: anywhere;
        }

        .gp-user-role {
            align-self: start;
            font-size: 0.7rem;
            letter-spacing: 0.03em;
            background: #FFF3C4;
            color: #A16207;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .gp-body {
            padding: 0 2rem 2rem 2rem;
        }

        .gp-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
        }

        .gp-alert ul {
            padding-left: 1.2rem;
            font-size: 0.8rem;
            color: #b91c1c;
        }

        .gp-group {
            margin-bottom: 1rem;
        }

        .gp-group:last-of-type {
            margin-bottom: 1.5rem;
        }

        .gp-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .gp-hint {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 0.35rem;
        }

        .gp-input {
            width: 100%;
            min-height: 44px;
            padding: 0.6rem 0.85rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            color: #0f172a;
            background: #ffffff;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .gp-input:focus {
            border-color: #A16207;
            box-shadow: 0 0 0 3px rgba(254, 194, 0, 0.12);
        }

        .gp-submit {
            width: 100%;
            min-height: 44px;
            padding: 0.7rem 1rem;
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
            transition: background 0.2s, transform 0.1s;
        }

        .gp-submit:hover {
            background: #D9A500;
        }

        .gp-submit:active {
            transform: translateY(1px);
        }

        .gp-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            background: #f8fafc;
        }

        .gp-logout {
            background: none;
            border: none;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.4rem;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }

        .gp-logout:hover {
            color: #dc2626;
            background: #fef2f2;
        }

        .gp-logout:focus-visible,
        .gp-submit:focus-visible {
            outline: 2px solid #A16207;
            outline-offset: 2px;
        }

        @media (max-width: 520px) {
            body {
                padding: 0.75rem;
            }

            .gp-card {
                margin: 0 auto;
                border-radius: 14px;
            }

            .gp-header {
                padding: 1.5rem 1.15rem 1.15rem 1.15rem;
            }

            .gp-icon {
                width: 46px;
                height: 46px;
                border-radius: 13px;
                margin-bottom: 0.75rem;
            }

            .gp-title {
                font-size: 1.15rem;
            }

            .gp-desc {
                font-size: 0.8rem;
            }

            .gp-user {
                margin: 0 1.15rem 1.25rem 1.15rem;
                padding: 0.7rem 0.85rem;
                gap: 0.5rem;
            }

            .gp-body {
                padding: 0 1.15rem 1.5rem 1.15rem;
            }

            .gp-input {
                min-height: 48px;
                font-size: 16px;
            }

            .gp-submit {
                min-height: 48px;
            }

            .gp-footer {
                padding: 0.9rem 1.15rem;
            }
        }
    </style>
</head>
<body>

    <div class="gp-card">
        <div class="gp-header">
            <div class="gp-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 class="gp-title">Perbarui Password Akun Anda</h1>
            <p class="gp-desc">Demi keamanan akun Anda, silakan ganti password bawaan/default sistem sebelum melanjutkan penggunaan aplikasi.</p>
        </div>

        <div class="gp-user">
            <div class="gp-user-identity">
                <div class="gp-user-name">{{ $user->name }}</div>
                <div class="gp-user-email">{{ $user->email }}</div>
            </div>
            <span class="gp-user-role">{{ $user->role }}</span>
        </div>

        <div class="gp-body">
            @if ($errors->any())
                <div class="gp-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.ganti-wajib.update') }}">
                @csrf

                <div class="gp-group">
                    <label for="password" class="gp-label">Password Baru <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password" name="password" class="gp-input" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    <div class="gp-hint">Gunakan kombinasi huruf, angka, atau simbol.</div>
                </div>

                <div class="gp-group">
                    <label for="password_confirmation" class="gp-label">Konfirmasi Password Baru <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="gp-input" required autocomplete="new-password" placeholder="Ulangi password baru">
                </div>

                <button type="submit" class="gp-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Simpan Password &amp; Lanjutkan
                </button>
            </form>
        </div>

        <div class="gp-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="gp-logout">
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
