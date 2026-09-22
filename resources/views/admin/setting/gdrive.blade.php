@extends('layouts.app')

@section('content')

<div x-data="{ 
    showModal: false, 
    activeTab: 'oauth_gcp',
    copied: false,
    copyRedirectUri() {
        navigator.clipboard.writeText('{{ $redirectUri }}');
        this.copied = true;
        setTimeout(() => { this.copied = false; }, 2500);
    }
}">

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 class="page-header" style="margin-bottom: 0.25rem;">
                Pengaturan Google Drive
            </h1>
            <p class="page-subtitle" style="color: #64748b; margin: 0;">
                Integrasi penyimpanan cloud terpusat akun kampus via OAuth 2.0 untuk berkas materi, tugas, dan kiriman mahasiswa.
            </p>
        </div>

        <div>
            <button type="button" @click="showModal = true" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Panduan Menautkan Akun Kampus
            </button>
        </div>
    </div>

    <!-- Status Card Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-left: 4px solid {{ $status['enabled'] ? '#10b981' : '#64748b' }};">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Status Integrasi
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.4rem;">
                <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $status['enabled'] ? '#10b981' : '#94a3b8' }}; display: inline-block;"></span>
                <span style="font-size: 1.15rem; font-weight: 800; color: #1e293b;">
                    {{ $status['enabled'] ? 'Aktif (Cloud Storage)' : 'Nonaktif (Local Storage)' }}
                </span>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                {{ $status['enabled'] ? 'Unggahan tersimpan di Google Drive' : 'Unggahan tersimpan di server lokal' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid {{ $status['has_oauth_connected'] ? '#10b981' : '#f59e0b' }};">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Tautan Akun Google
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                @if($status['has_oauth_connected'])
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>Terhubung</span>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>Belum Ditautkan</span>
                @endif
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem; word-break: break-all;">
                {{ $status['has_oauth_connected'] ? ($status['oauth_connected_email'] ?: 'Akun Google Kampus') : 'Perlu login via OAuth 2.0' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid #6366f1;">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Folder Penyimpanan Utama
            </div>
            <div style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 0.4rem; font-family: monospace; word-break: break-all;">
                {{ $status['folder_id'] ?: 'Belum Dikonfigurasi' }}
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                Folder induk di Drive Saya akun kampus
            </div>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
            <div>
                <h2 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">
                    Konfigurasi Akun Google Drive (OAuth 2.0)
                </h2>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem;">
                    Gunakan kredensial OAuth 2.0 Google Cloud agar kuota penyimpanan menggunakan kapasitas akun kampus langsung.
                </div>
            </div>
            <span style="font-size: 0.75rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 0.25rem 0.6rem; border-radius: 9999px; font-weight: 600;">
                Anti-Reset &amp; Auto-Backup Aktif
            </span>
        </div>

        <form action="{{ route('admin.setting.gdrive.update') }}" method="POST">
            @csrf

            <!-- Checkbox Aktifkan -->
            <div style="margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="enabled" value="1" {{ $status['enabled'] ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #2563eb;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">Aktifkan Penyimpanan Cloud Google Drive</span>
                </label>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; margin-left: 1.8rem;">
                    Jika dicentang, seluruh materi perkuliahan dan kiriman tugas mahasiswa akan otomatis diunggah ke Google Drive.
                </div>
            </div>

            <!-- Folder ID Utama -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; color: #334155;">Folder ID Google Drive Utama</label>
                <input type="text" name="folder_id" class="form-control" value="{{ old('folder_id', $status['folder_id']) }}" placeholder="Contoh: 1A2b3C4d5E6f7G8h9I0jK">
                <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                    ID Folder induk di Drive Saya akun kampus (misal <code>eduva@polsa.ac.id</code>). Diambil dari URL browser setelah <code>/folders/</code>.
                </div>
            </div>

            <!-- Bagian Kredensial OAuth 2.0 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Kredensial OAuth 2.0 Web Client (Google Cloud Console)
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #334155;">Client ID</label>
                        <input type="text" name="oauth_client_id" class="form-control" value="{{ old('oauth_client_id', $status['oauth_client_id']) }}" placeholder="Contoh: 123456789-xxx.apps.googleusercontent.com">
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                            Didapat dari menu Google Cloud: APIs &amp; Services &rarr; Credentials.
                        </div>
                    </div>

                    <div>
                        <label class="form-label" style="font-weight: 600; color: #334155;">Client Secret</label>
                        <input type="password" name="oauth_client_secret" class="form-control" value="{{ old('oauth_client_secret', $status['oauth_client_secret']) }}" placeholder="Contoh: GOCSPX-xxxxxxxxxxxxxx">
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                            Kunci rahasia client dari Google Cloud Console.
                        </div>
                    </div>
                </div>

                <!-- Redirect URI Information -->
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
                        Authorized Redirect URI (Wajib dicantumkan di Google Cloud Console):
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <input type="text" readonly value="{{ $redirectUri }}" style="flex: 1; min-width: 250px; font-family: monospace; font-size: 0.82rem; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.4rem 0.6rem; border-radius: 6px; color: #334155;">
                        <button type="button" @click="copyRedirectUri()" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span x-show="!copied">Salin URI</span>
                            <span x-show="copied" style="color: #10b981; font-weight: 700;">Tersalin!</span>
                        </button>
                    </div>
                </div>

                <!-- Status Hubungan Akun Google -->
                <div style="padding: 1rem; border-radius: 8px; border: 1px solid; {{ $status['has_oauth_connected'] ? 'background: #ecfdf5; border-color: #a7f3d0;' : 'background: #fffbeb; border-color: #fde68a;' }}">
                    @if($status['has_oauth_connected'])
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <div style="font-size: 0.82rem; color: #065f46; font-weight: 700;">
                                        Akun Google Kampus Berhasil Terhubung:
                                    </div>
                                    <div style="font-size: 0.95rem; font-weight: 800; color: #064e3b; font-family: monospace;">
                                        {{ $status['oauth_connected_email'] ?: 'Akun Terautentikasi' }}
                                    </div>
                                </div>
                            </div>

                            <button type="submit" formaction="{{ route('admin.setting.gdrive.oauth.disconnect') }}" class="btn btn-danger" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;" onclick="return confirm('Apakah Anda yakin ingin memutuskan koneksi akun Google ini?')">
                                Putuskan Hubungan
                            </button>
                        </div>
                    @else
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <div style="font-weight: 700; color: #92400e; font-size: 0.88rem;">
                                    Akun Google Kampus Belum Ditautkan
                                </div>
                                <div style="font-size: 0.8rem; color: #78350f; margin-top: 0.2rem;">
                                    1) Isi Client ID &amp; Secret di atas &rarr; 2) Klik <strong>Simpan Pengaturan</strong> &rarr; 3) Klik <strong>Hubungkan Akun Kampus</strong>.
                                </div>
                            </div>

                            @if(!empty($status['oauth_client_id']) && !empty($status['oauth_client_secret']))
                                <a href="{{ route('admin.setting.gdrive.oauth.connect') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 700;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                        <polyline points="10 17 15 12 10 7"></polyline>
                                        <line x1="15" y1="12" x2="3" y2="12"></line>
                                    </svg>
                                    Hubungkan Akun Kampus Sekarang
                                </a>
                            @else
                                <button type="button" disabled class="btn btn-secondary" style="opacity: 0.6; cursor: not-allowed; font-size: 0.85rem;">
                                    Simpan Client ID &amp; Secret Terlebih Dahulu
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; padding-top: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="font-weight: 600;">
                    Simpan Pengaturan
                </button>
                <button type="submit" formaction="{{ route('admin.setting.gdrive.test') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Tes Koneksi GDrive
                </button>
            </div>
        </form>
    </div>

    <!-- POPUP MODAL PANDUAN -->
    <div x-show="showModal"
         x-cloak
         @keydown.escape.window="showModal = false"
         @click.self="showModal = false"
         style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(2px);">

        <div @click.outside="showModal = false"
             style="background: #ffffff; border-radius: 16px; max-width: 820px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); border: 1px solid #e2e8f0;">

            <!-- Modal Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">Panduan Integrasi Google Drive (OAuth 2.0)</h3>
                        <div style="font-size: 0.78rem; color: #64748b;">Panduan resmi menautkan akun Google kampus ke Eduva LMS</div>
                    </div>
                </div>

                <button type="button" @click="showModal = false" style="background: #f1f5f9; border: 1px solid #e2e8f0; cursor: pointer; padding: 0.4rem 0.6rem; border-radius: 8px; color: #475569; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.35rem;" title="Tutup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Tutup
                </button>
            </div>

            <!-- Modal Tab Nav -->
            <div style="display: flex; border-bottom: 1px solid #e2e8f0; background: #ffffff; padding: 0 1.5rem; gap: 1rem; overflow-x: auto;">
                <button type="button" @click="activeTab = 'oauth_gcp'" :style="activeTab === 'oauth_gcp' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    1. OAuth &amp; Consent Screen
                </button>
                <button type="button" @click="activeTab = 'credentials'" :style="activeTab === 'credentials' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    2. Buat OAuth Client ID
                </button>
                <button type="button" @click="activeTab = 'folder'" :style="activeTab === 'folder' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    3. Siapkan Folder di Drive Saya
                </button>
            </div>

            <!-- Modal Body Content -->
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1; font-size: 0.88rem; color: #334155; line-height: 1.6;">

                <!-- TAB 1: OAUTH & CONSENT SCREEN -->
                <div x-show="activeTab === 'oauth_gcp'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 1: Setup Proyek Google Cloud &amp; Layar Persetujuan (OAuth Consent Screen)</h4>
                    <p style="margin-bottom: 1rem;">
                        Dengan metode OAuth 2.0, Eduva LMS mengunggah file langsung ke <strong>Drive Saya</strong> akun kampus (misal <code>eduva@polsa.ac.id</code>). Kapasitas penyimpanan otomatis mengikuti kuota resmi akun kampus Anda.
                    </p>

                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.65rem;">
                        <li>Buka Google Cloud Console: <a href="https://console.cloud.google.com" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">console.cloud.google.com</a> dan login menggunakan akun Google Anda.</li>
                        <li>Buat project baru atau pilih project yang sudah ada (contoh nama: <code>Eduva Storage</code>).</li>
                        <li>Masuk ke menu navigasi &rarr; <strong>APIs &amp; Services</strong> &rarr; <strong>Library</strong>. Cari <strong>Google Drive API</strong>, lalu klik <strong>Enable</strong>.</li>
                        <li>Masuk ke menu <strong>APIs &amp; Services</strong> &rarr; <strong>OAuth consent screen</strong>:
                            <ul style="padding-left: 1.2rem; margin-top: 0.25rem;">
                                <li>Pilih User Type: <strong>Internal</strong> (jika memakai Google Workspace kampus) atau <strong>External</strong> (jika memakai akun Gmail umum). Klik <em>Create</em>.</li>
                                <li>Isi <strong>App name</strong> (misal: <code>Eduva LMS Storage</code>) dan <strong>User support email</strong> (email Anda/kampus).</li>
                                <li>Di bagian Developer contact information, isi alamat email Anda. Klik <em>Save and Continue</em>.</li>
                                <li>Di langkah <strong>Scopes</strong>, klik <em>Add or Remove Scopes</em>, centang scope:
                                    <br><code>.../auth/drive</code> dan <code>.../auth/userinfo.email</code>.
                                    <br>Klik <em>Update</em> &rarr; <em>Save and Continue</em>.</li>
                                <li>Jika memilih tipe <strong>External</strong>, pada langkah <strong>Test users</strong>, klik <em>Add Users</em> dan masukkan email akun kampus yang akan ditautkan (misal: <code>eduva@polsa.ac.id</code>).</li>
                            </ul>
                        </li>
                    </ol>
                </div>

                <!-- TAB 2: BUAT OAUTH CLIENT ID -->
                <div x-show="activeTab === 'credentials'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 2: Membuat Client ID &amp; Mendaftarkan Authorized Redirect URI</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.65rem;">
                        <li>Masuk ke menu <strong>APIs &amp; Services</strong> &rarr; <strong>Credentials</strong> di Google Cloud Console.</li>
                        <li>Klik tombol <strong>+ Create Credentials</strong> di bagian atas &rarr; pilih <strong>OAuth client ID</strong>.</li>
                        <li>Pilih <strong>Application type</strong>: <strong>Web application</strong>.</li>
                        <li>Beri nama client (contoh: <code>Eduva Web Storage Client</code>).</li>
                        <li>Pada bagian <strong>Authorized redirect URIs</strong>, klik <strong>+ Add URI</strong> dan masukkan persis URL redirect di bawah:
                            <div style="background: #f1f5f9; padding: 0.6rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.82rem; margin-top: 0.35rem; color: #0f172a; border: 1px solid #cbd5e1; word-break: break-all;">
                                {{ $redirectUri }}
                            </div>
                        </li>
                        <li>Klik <strong>Create</strong>. Dialog pop-up akan menampilkan <strong>Client ID</strong> dan <strong>Client Secret</strong>.</li>
                        <li>Salin kedua nilai tersebut dan tempelkan ke kolom form <strong>Client ID</strong> dan <strong>Client Secret</strong> di halaman Eduva ini.</li>
                        <li>Klik tombol <strong>Simpan Pengaturan</strong> di bawah form.</li>
                        <li>Setelah tersimpan, klik tombol biru <strong>Hubungkan Akun Kampus Sekarang</strong>. Lakukan persetujuan izin akses Google Drive untuk Eduva.</li>
                    </ol>
                </div>

                <!-- TAB 3: FOLDER DRIVE SAYA -->
                <div x-show="activeTab === 'folder'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 3: Menyiapkan Folder di Drive Saya (eduva@polsa.ac.id)</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.65rem;">
                        <li>Buka <a href="https://drive.google.com" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">drive.google.com</a> dengan akun kampus yang sudah ditautkan.</li>
                        <li>Di menu <strong>Drive Saya (My Drive)</strong>, klik <strong>+ Baru (+ New)</strong> &rarr; pilih <strong>Folder baru</strong> (beri nama misal: <code>Eduva_LMS_Storage</code>).</li>
                        <li>Buka folder baru tersebut. Perhatikan address bar di browser Anda:
                            <div style="background: #f1f5f9; padding: 0.6rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.82rem; margin-top: 0.35rem; color: #1e293b; border: 1px solid #cbd5e1;">
                                drive.google.com/drive/folders/<strong style="color: #2563eb;">1A2b3C4d5E6f7G8h9I0jK</strong>
                            </div>
                        </li>
                        <li>Salin deretan karakter setelah <code>/folders/</code> tersebut (itu adalah <strong>Folder ID</strong>).</li>
                        <li>Tempelkan ke kolom <strong>Folder ID Google Drive Utama</strong> pada form pengaturan Eduva ini, lalu klik <strong>Simpan Pengaturan</strong>.</li>
                        <li>Klik <strong>Tes Koneksi GDrive</strong> untuk memverifikasi kesiapan integrasi. Selesai!</li>
                    </ol>
                </div>

            </div>

            <!-- Modal Footer -->
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end;">
                <button type="button" @click="showModal = false" class="btn btn-secondary" style="font-weight: 600;">
                    Tutup Panduan
                </button>
            </div>

        </div>

    </div>

</div>

@endsection
