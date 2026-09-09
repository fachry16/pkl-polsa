@extends('layouts.app')

@section('content')

<div x-data="{ showModal: false, activeTab: 'overview' }">

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
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
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

        <div class="card" style="padding: 1.25rem; border-left: 4px solid {{ $status['has_json'] ? '#2563eb' : '#f59e0b' }};">
            <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                Kredensial Service Account
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-top: 0.4rem;">
                {{ $status['has_json'] ? 'Kunci JSON Terpasang' : 'Belum Ada File JSON' }}
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

            <div style="margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="enabled" value="1" {{ $status['enabled'] ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #2563eb;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">Aktifkan Penyimpanan Cloud Google Drive</span>
                </label>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; margin-left: 1.8rem;">
                    Jika diaktifkan, berkas materi & kiriman mahasiswa diunggah langsung ke Google Drive kampus.
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label class="form-label" style="font-weight: 600; color: #334155;">Folder ID Google Drive Utama</label>
                    <input type="text" name="folder_id" class="form-control" value="{{ old('folder_id', $status['folder_id']) }}" placeholder="Contoh: 1A2b3C4d5E6f7G8h9I0jK">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                        Diambil dari URL folder Google Drive di browser (setelah <code>/folders/</code>).
                    </div>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 600; color: #334155;">Email Service Account</label>
                    <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $status['client_email']) }}" placeholder="name@project.iam.gserviceaccount.com">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                        Pastikan email ini diberikan hak akses <strong>Editor</strong> pada folder Google Drive kampus.
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; color: #334155;">Upload File Kredensial JSON (Service Account Key)</label>
                <input type="file" name="credentials_json" class="form-control" accept=".json">
                <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                    Unduh file JSON ini dari menu Service Account Keys di Google Cloud Console.
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <button type="submit" class="btn btn-primary" style="font-weight: 600;">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- POPUP MODAL PANDUAN -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; inset: 0; z-index: 1000; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; padding: 1rem;"
         x-cloak>

        <div @click.away="showModal = false"
             style="background: #ffffff; border-radius: 16px; max-width: 780px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

            <!-- Modal Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">Panduan Integrasi Google Drive</h3>
                        <div style="font-size: 0.78rem; color: #64748b;">Langkah mudah penautan penyimpanan cloud Eduva LMS</div>
                    </div>
                </div>

                <button type="button" @click="showModal = false" style="background: transparent; border: none; cursor: pointer; padding: 0.4rem; border-radius: 8px; color: #64748b;" title="Tutup">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Modal Tab Nav -->
            <div style="display: flex; border-bottom: 1px solid #e2e8f0; background: #ffffff; padding: 0 1.5rem; gap: 1rem; overflow-x: auto;">
                <button type="button" @click="activeTab = 'overview'" :style="activeTab === 'overview' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    1. Cara Kerja
                </button>
                <button type="button" @click="activeTab = 'gcp'" :style="activeTab === 'gcp' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    2. Service Account
                </button>
                <button type="button" @click="activeTab = 'drive'" :style="activeTab === 'drive' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    3. Folder &amp; Sharing
                </button>
                <button type="button" @click="activeTab = 'form'" :style="activeTab === 'form' ? 'border-bottom: 2px solid #2563eb; color: #2563eb; font-weight: 700;' : 'color: #64748b; font-weight: 500;'" style="padding: 0.8rem 0.25rem; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                    4. Simpan Kredensial
                </button>
            </div>

            <!-- Modal Body Content -->
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1; font-size: 0.88rem; color: #334155; line-height: 1.6;">

                <!-- TAB 1: CARA KERJA -->
                <div x-show="activeTab === 'overview'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Bagaimana Integrasi Ini Bekerja?</h4>
                    <p style="margin-bottom: 1rem;">
                        Sistem Eduva LMS menggunakan Google Drive kampus sebagai <strong>Storage Backend Cloud</strong>. Dosen dan mahasiswa tidak perlu membuat folder manual atau menghubungkan akun Google pribadi mereka.
                    </p>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Alur Pengunggahan &amp; Pratinjau File:</div>
                        <ul style="margin: 0; padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.4rem;">
                            <li>Pengguna mengunggah file biasa melalui form LMS (Upload Materi / Tugas).</li>
                            <li>Eduva LMS secara otomatis mentransfer file tersebut ke folder Google Drive kampus.</li>
                            <li>Sistem membuatkan hirarki folder per Semester &amp; Mata Kuliah secara otomatis.</li>
                            <li>Saat file diklik, browser akan **membuka tab baru** dengan tampilan pratinjau bawaan Google Drive (lengkap dengan opsi cetak &amp; download).</li>
                        </ul>
                    </div>
                </div>

                <!-- TAB 2: SERVICE ACCOUNT -->
                <div x-show="activeTab === 'gcp'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 1: Membuat Service Account &amp; Kunci JSON</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.6rem;">
                        <li>Buka konsol pengembang <a href="https://console.cloud.google.com" target="_blank" style="color: #2563eb; text-decoration: underline;">Google Cloud Console</a>.</li>
                        <li>Buat project baru (misal: <code>Eduva-LMS-Storage</code>).</li>
                        <li>Masuk ke menu <strong>APIs &amp; Services</strong> &rarr; <strong>Library</strong>, cari <strong>Google Drive API</strong> lalu klik <strong>Enable</strong>.</li>
                        <li>Masuk ke <strong>APIs &amp; Services</strong> &rarr; <strong>Credentials</strong> &rarr; klik <strong>Create Credentials</strong> &rarr; pilih <strong>Service Account</strong>.</li>
                        <li>Isi nama Service Account (misal: <code>eduva-drive-bot</code>) lalu klik <strong>Create and Continue</strong> &rarr; <strong>Done</strong>.</li>
                        <li>Klik email Service Account yang baru dibuat, masuk ke tab <strong>Keys</strong> &rarr; klik <strong>Add Key</strong> &rarr; <strong>Create New Key</strong>.</li>
                        <li>Pilih tipe <strong>JSON</strong> lalu klik <strong>Create</strong>. File kredensial JSON akan otomatis terunduh ke komputer Anda.</li>
                    </ol>
                </div>

                <!-- TAB 3: FOLDER & SHARING -->
                <div x-show="activeTab === 'drive'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 2: Menyiapkan Folder di Google Drive Kampus</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.6rem;">
                        <li>Buka Google Drive instansi/kampus Anda (<a href="https://drive.google.com" target="_blank" style="color: #2563eb; text-decoration: underline;">drive.google.com</a>).</li>
                        <li>Buat folder baru sebagai induk penyimpanan (misal: <code>Eduva_LMS_Storage</code>).</li>
                        <li>Klik kanan folder tersebut lalu pilih <strong>Bagikan / Share</strong>.</li>
                        <li>Salin <strong>Email Service Account</strong> (contoh: <code>eduva-drive-bot@project-id.iam.gserviceaccount.com</code>) lalu paste ke kotak bagikan.</li>
                        <li>Pastikan perannya diset sebagai <strong>Editor</strong>, uncheck "Send notification", lalu klik <strong>Share</strong>.</li>
                        <li>Buka folder tersebut, lalu perhatikan URL pada address bar browser:
                            <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 6px; font-family: monospace; font-size: 0.8rem; margin-top: 0.3rem;">
                                https://drive.google.com/drive/u/0/folders/<strong style="color: #059669;">1A2b3C4d5E6f7G8h9I0jK</strong>
                            </div>
                            Kode acak setelah <code>/folders/</code> tersebut adalah <strong>Folder ID Induk</strong> Anda.
                        </li>
                    </ol>
                </div>

                <!-- TAB 4: SIMPAN KREDENSIAL -->
                <div x-show="activeTab === 'form'">
                    <h4 style="font-weight: 700; color: #1e293b; margin-top: 0;">Langkah 3: Mengisi Pengaturan di Eduva LMS</h4>
                    <ol style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.6rem;">
                        <li>Kembali ke halaman form Pengaturan Google Drive di Eduva LMS ini.</li>
                        <li>Salin dan tempel <strong>Folder ID Induk</strong> dari Langkah 2.</li>
                        <li>Salin dan tempel <strong>Email Service Account</strong> dari Langkah 1.</li>
                        <li>Pilih dan unggah file <strong>JSON Key</strong> yang telah diunduh pada Langkah 1.</li>
                        <li>Centang kotak <strong>Aktifkan Penyimpanan Cloud Google Drive</strong>.</li>
                        <li>Klik tombol <strong>Simpan Pengaturan</strong>. Sistem Anda kini telah terhubung ke Google Drive!</li>
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
