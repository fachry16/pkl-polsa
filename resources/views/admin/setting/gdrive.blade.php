@extends('layouts.app')

@section('content')

<div x-data="{ 
    showModal: false, 
    activeTab: 'oauth',
    authMode: '{{ old('auth_mode', $status['auth_mode'] ?? 'oauth') }}',
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
                Integrasi penyimpanan cloud terpusat untuk berkas materi, tugas, dan kiriman mahasiswa.
            </p>
        </div>

        <div>
            <button type="button" @click="showModal = true" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Panduan & Cara Menautkan GDrive
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
                    {{ $status['enabled'] ? 'Aktif (Connected)' : 'Nonaktif (Local Storage)' }}
                </span>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid {{ $status['has_oauth_connected'] ? '#10b981' : ($status['has_json'] ? '#A16207' : '#f59e0b') }};">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Metode Autentikasi
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-top: 0.4rem;">
                @if($status['has_oauth_connected'])
                    OAuth 2.0 Terhubung
                @elseif($status['has_json'])
                    Service Account
                @else
                    Belum Dikonfigurasi
                @endif
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem;">
                @if($status['has_oauth_connected'])
                    {{ $status['oauth_connected_email'] ?: 'Akun Google Kampus' }}
                @elseif($status['has_json'])
                    Kunci JSON Terpasang
                @else
                    Pilih OAuth atau Kunci JSON
                @endif
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid #D9A500;">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Folder Penyimpanan Utama
            </div>
            <div style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 0.4rem; font-family: monospace; word-break: break-all;">
                {{ $status['folder_id'] ?: 'Belum Diatur' }}
            </div>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 1.25rem;">
            Konfigurasi Akun Google Drive Kampus
        </h2>

        <form action="{{ route('admin.setting.gdrive.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Checkbox Aktifkan -->
            <div style="margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="enabled" value="1" {{ $status['enabled'] ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #A16207;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">Aktifkan Penyimpanan Cloud Google Drive</span>
                </label>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; margin-left: 1.8rem;">
                    Jika diaktifkan, berkas materi &amp; kiriman mahasiswa diunggah langsung ke Google Drive kampus.
                </div>
            </div>

            <!-- Folder ID Utama -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; color: #334155;">Folder ID Google Drive Utama</label>
                <input type="text" name="folder_id" class="form-control" value="{{ old('folder_id', $status['folder_id']) }}" placeholder="Contoh: 1A2b3C4d5E6f7G8h9I0jK">
                <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                    Folder induk di Drive Saya akun kampus (misal <code>eduva@polsa.ac.id</code>). Diambil dari URL browser setelah <code>/folders/</code>.
                </div>
            </div>

            <!-- Pilihan Metode Autentikasi -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; display: block;">
                    Metode Autentikasi
                </label>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    <!-- Option OAuth -->
                    <label :style="authMode === 'oauth' ? 'border-color: #A16207; background: #FFF8E0;' : 'border-color: #e2e8f0; background: #ffffff;'"
                           style="border: 2px solid; border-radius: 12px; padding: 1rem; cursor: pointer; display: flex; gap: 0.75rem; align-items: flex-start; transition: all 0.2s;">
                        <input type="radio" name="auth_mode" value="oauth" x-model="authMode" style="margin-top: 0.2rem; accent-color: #A16207;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; display: flex; align-items: center; gap: 0.4rem;">
                                OAuth 2.0 (Akun Kampus)
                                <span style="background: #FFF3C4; color: #A16207; font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 9999px;">Disarankan</span>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                                Login langsung dengan email kampus (misal: <code>eduva@polsa.ac.id</code>). Bebas kuota 0 MB Service Account dan tanpa perlu izin admin kampus.
                            </div>
                        </div>
                    </label>

                    <!-- Option Service Account -->
                    <label :style="authMode === 'service_account' ? 'border-color: #A16207; background: #FFF8E0;' : 'border-color: #e2e8f0; background: #ffffff;'"
                           style="border: 2px solid; border-radius: 12px; padding: 1rem; cursor: pointer; display: flex; gap: 0.75rem; align-items: flex-start; transition: all 0.2s;">
                        <input type="radio" name="auth_mode" value="service_account" x-model="authMode" style="margin-top: 0.2rem; accent-color: #A16207;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                                Service Account (Kunci JSON)
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                                Menggunakan robot Service Account Google Cloud. Memerlukan fitur <em>Drive Bersama</em> (Shared Drive) atau Google Workspace Admin Console.
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- BAGIAN 1: FORM OAUTH 2.0 -->
            <div x-show="authMode === 'oauth'" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A0D40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Kredensial OAuth 2.0 Web Client (Google Cloud Console)
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #334155;">Client ID</label>
                        <input type="text" name="oauth_client_id" class="form-control" value="{{ old('oauth_client_id', $status['oauth_client_id']) }}" placeholder="Contoh: 123456789-abc.apps.googleusercontent.com">
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                            Didapat dari menu APIs &amp; Services &rarr; Credentials di Google Cloud.
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
                        Authorized Redirect URI (Masukkan ini ke Google Cloud Console):
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="text" readonly value="{{ $redirectUri }}" style="flex: 1; font-family: monospace; font-size: 0.82rem; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.4rem 0.6rem; border-radius: 6px; color: #334155;">
                        <button type="button" @click="copyRedirectUri()" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; white-space: nowrap;">
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
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <div style="font-size: 0.82rem; color: #065f46; font-weight: 700;">
                                        Akun Google Kampus Berhasil Terhubung:
                                    </div>
                                    <div style="font-size: 0.95rem; font-weight: 800; color: #064e3b; font-family: monospace;">
                                        {{ $status['oauth_connected_email'] ?: 'eduva@polsa.ac.id' }}
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

            <!-- BAGIAN 2: FORM SERVICE ACCOUNT -->
            <div x-show="authMode === 'service_account'" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A0D40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    Kredensial Service Account (Google Cloud)
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #334155;">Email Service Account</label>
                        <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $status['client_email']) }}" placeholder="name@project.iam.gserviceaccount.com">
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                            Email robot GCP yang diberi izin Editor pada Drive Bersama.
                        </div>
                    </div>

                    <div>
                        <label class="form-label" style="font-weight: 600; color: #334155;">Email Delegasi Impersonate (Opsional)</label>
                        <input type="email" name="impersonate_email" class="form-control" value="{{ old('impersonate_email', $status['impersonate_email'] ?? '') }}" placeholder="admin@domain-kampus.ac.id">
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                            Diisi jika menggunakan Domain-Wide Delegation Google Workspace.
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 600; color: #334155;">Upload File Kredensial JSON (Service Account Key)</label>
                    <input type="file" name="credentials_json" class="form-control" accept=".json">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                        Unduh file JSON ini dari menu Service Account Keys di Google Cloud Console.
                    </div>
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
         :style="{ display: showModal ? 'flex' : 'none' }"
         style="position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(2px); align-items: center; justify-content: center; padding: 1rem; display: none;">

        <div @click.outside="showModal = false"
             style="background: #ffffff; border-radius: 16px; max-width: 820px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); border: 1px solid #e2e8f0;">

            <!-- Modal Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #FFF8E0; color: #A16207; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">Panduan Integrasi Google Drive Kampus</h3>
                        <div style="font-size: 0.78rem; color: #64748b;">Langkah mudah menghubungkan email kampus eduva@polsa.ac.id ke Eduva LMS</div>
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
                <button type="button" @click="activeTab = 'oauth'" :style="activeTab === 'oauth' ? 'border-bottom: 2px solid #D9A500; color: #A16207; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    1. OAuth Akun Kampus (Utama)
                </button>
                <button type="button" @click="activeTab = 'folder'" :style="activeTab === 'folder' ? 'border-bottom: 2px solid #D9A500; color: #A16207; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    2. Folder Drive Saya
                </button>
                <button type="button" @click="activeTab = 'service_account'" :style="activeTab === 'service_account' ? 'border-bottom: 2px solid #D9A500; color: #A16207; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    3. Alternatif Service Account
                </button>
            </div>

            <!-- Modal Body Content -->
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1; font-size: 0.88rem; color: #334155; line-height: 1.6;">

                <!-- TAB 1: PANDUAN OAUTH -->
                <div x-show="activeTab === 'oauth'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 1: Menyiapkan OAuth 2.0 Web Client di Google Cloud</h4>
                    <p style="margin-bottom: 1rem;">
                        Metode ini memungkinkan Eduva LMS mengunggah berkas langsung ke <strong>Drive Saya</strong> milik akun <code>eduva@polsa.ac.id</code> menggunakan kuota resmi kampus Anda (bebas dari error kuota 0 MB Service Account).
                    </p>

                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.65rem;">
                        <li>Buka konsol Google: <a href="https://console.cloud.google.com" target="_blank" style="color: #A16207; text-decoration: underline; font-weight: 600;">console.cloud.google.com</a> dan pastikan project aktif sudah terpilih.</li>
                        <li>Pastikan <strong>Google Drive API</strong> telah diaktifkan di menu <strong>APIs &amp; Services</strong> &rarr; <strong>Library</strong>.</li>
                        <li>Buka menu <strong>APIs &amp; Services</strong> &rarr; <strong>OAuth consent screen</strong>:
                            <ul style="padding-left: 1.2rem; margin-top: 0.25rem;">
                                <li>Pilih User Type: <strong>Internal</strong> (jika diizinkan Google Workspace polsa.ac.id) atau <strong>External</strong>.</li>
                                <li>Isi App name (misal: <code>Eduva LMS Storage</code>) dan email support (<code>eduva@polsa.ac.id</code>).</li>
                                <li>Di bagian Scopes, tambahkan <code>.../auth/drive</code> dan <code>.../auth/userinfo.email</code>.</li>
                                <li>Jika memilih External dan status masih Testing, tambahkan <code>eduva@polsa.ac.id</code> di menu <strong>Test Users</strong>.</li>
                            </ul>
                        </li>
                        <li>Masuk ke menu <strong>APIs &amp; Services</strong> &rarr; <strong>Credentials</strong>:
                            <ul style="padding-left: 1.2rem; margin-top: 0.25rem;">
                                <li>Klik <strong>Create Credentials</strong> &rarr; pilih <strong>OAuth client ID</strong>.</li>
                                <li>Pilih Application type: <strong>Web application</strong>.</li>
                                <li>Beri nama client (contoh: <code>Eduva Web Storage</code>).</li>
                                <li>Di bagian <strong>Authorized redirect URIs</strong>, klik <strong>Add URI</strong> dan tempel URL berikut:<br>
                                    <code style="background: #e2e8f0; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.82rem; color: #0f172a;">{{ $redirectUri }}</code>
                                </li>
                                <li>Klik <strong>Create</strong>. Salin <strong>Client ID</strong> dan <strong>Client Secret</strong> yang muncul.</li>
                            </ul>
                        </li>
                        <li>Kembali ke form Pengaturan Eduva ini, tempelkan <strong>Client ID</strong> dan <strong>Client Secret</strong>, lalu klik <strong>Simpan Pengaturan</strong>.</li>
                        <li>Setelah tersimpan, klik tombol biru <strong>Hubungkan Akun Kampus Sekarang</strong>. Login dengan <code>eduva@polsa.ac.id</code> dan klik <strong>Izinkan (Allow)</strong>. Selesai!</li>
                    </ol>
                </div>

                <!-- TAB 2: FOLDER DRIVE SAYA -->
                <div x-show="activeTab === 'folder'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 2: Menyiapkan Folder di Drive Saya (eduva@polsa.ac.id)</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.65rem;">
                        <li>Buka <a href="https://drive.google.com" target="_blank" style="color: #A16207; text-decoration: underline;">drive.google.com</a> dengan akun kampus <code>eduva@polsa.ac.id</code>.</li>
                        <li>Di menu <strong>Drive Saya (My Drive)</strong>, klik <strong>+ Baru (+ New)</strong> &rarr; pilih <strong>Folder baru</strong> (beri nama misal: <code>Eduva_LMS_Storage</code>).</li>
                        <li>Buka folder tersebut. Perhatikan address bar browser Anda:
                            <div style="background: #f1f5f9; padding: 0.6rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.8rem; margin-top: 0.35rem; color: #1e293b; border: 1px solid #cbd5e1;">
                                drive.google.com/drive/folders/<strong style="color: #A16207;">1A2b3C4d5E6f7G8h9I0jK</strong>
                            </div>
                        </li>
                        <li>Salin deretan karakter setelah <code>/folders/</code> tersebut (itu adalah <strong>Folder ID</strong>).</li>
                        <li>Tempelkan ke kolom <strong>Folder ID Google Drive Utama</strong> pada form pengaturan Eduva ini.</li>
                    </ol>
                </div>

                <!-- TAB 3: ALTERNATIF SERVICE ACCOUNT -->
                <div x-show="activeTab === 'service_account'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Metode Service Account (Kunci JSON)</h4>
                    <p style="margin-bottom: 1rem;">
                        Metode Service Account hanya disarankan jika organisasi kampus Anda menyediakan menu <strong>Drive Bersama (Shared Drives)</strong> atau jika Anda memiliki akses super-admin Google Workspace.
                    </p>
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem; font-size: 0.84rem; color: #92400e;">
                        <strong>Peringatan Kuota 0 MB:</strong> Jika akun kampus Anda tidak memiliki menu Drive Bersama dan Anda membuat folder di "Drive Saya", Google akan menolak unggahan Service Account. Untuk kasus ini, selalu gunakan <strong>Metode OAuth 2.0 Akun Kampus</strong> pada Tab 1.
                    </div>
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
