# PIKOBE Polsa — Konteks Project untuk Brainstorming LMS

## 📋 Informasi Project

- **Nama**: PIKOBE (Politeknik Sawunggalih Aji Information System)
- **Domain**: Manajemen Kurikulum & RPS (Rencana Pembelajaran Semester)
- **Kampus**: Politeknik Sawunggalih Aji (Polsa)
- **Framework**: Laravel 12, PHP 8.2+
- **Frontend**: Blade + Alpine.js + Tailwind v3 + Vite
- **Database**: SQLite (dev), MySQL via Laragon (production), SQLite `:memory:` (testing)
- **PDF**: barryvdh/laravel-dompdf ^3.1
- **Auth**: Laravel Breeze ^2.4
- **Testing**: PHPUnit ^11.5.50
- **Code Style**: Laravel Pint ^1.24

---

## 🗂️ Struktur Direktori

```
app/
  Http/
    Controllers/
    Middleware/
  Models/
  Providers/
  View/Components/
bootstrap/
config/
database/
  factories/
  migrations/    (32 file)
  seeders/
public/
resources/
  views/
    auth/
    layouts/
    sidebar/
    rps/, cpl/, cpmk/, kurikulum/, ...
  css/
  js/
routes/
  web.php        (557 baris)
  auth.php
  console.php
storage/
tests/
  Unit/    (1 file)
  Feature/ (8 file)
```

---

## 🧩 Model & Database

### 1. User
| Field | Type |
|-------|------|
| name | string |
| email | string (unique) |
| password | hashed |
| role | enum: admin, dosen, direktur |

**Relasi**: `hasOne(Dosen)`
**Helpers**: `isAdmin()`, `isDirektur()`, `isDosen()`, `isKaprodi()`

### 2. Dosen
| Field | Type |
|-------|------|
| user_id | FK |
| program_studi_id | FK |
| nidn | string (unique) |
| jabatan | string (dosen/kaprodi) |

**Relasi**: `belongsTo(User)`, `belongsTo(ProgramStudi)`, `hasMany(Pengampu)`

### 3. Mahasiswa
| Field | Type |
|-------|------|
| program_studi_id | FK |
| nim | string |
| nama | string |
| angkatan | year |
| status | string |

**Relasi**: `belongsTo(ProgramStudi)`, `hasMany(SemesterMahasiswa)`, `belongsToMany(TahunAkademik)`, `belongsToMany(Pengampu)`

### 4. ProgramStudi
| Field | Type |
|-------|------|
| kode_prodi | string |
| nama_prodi | string |
| jenjang | string (D3/D4) |
| akreditasi | string |

**Seed**: TI(D3), AB(D3), BD(D4), TRPL(D4), AK(D3)
**Relasi**: `hasMany(Dosen)`, `hasMany(Mahasiswa)`, `hasMany(Kurikulum)`

### 5. TahunAkademik
| Field | Type |
|-------|------|
| tahun | year |
| semester | enum: ganjil/genap |
| is_active | boolean |

**Relasi**: `hasMany(SemesterMahasiswa)`, `hasMany(Pengampu)`, `hasMany(MahasiswaTahunAkademik)`

### 6. Kurikulum
| Field | Type |
|-------|------|
| program_studi_id | FK |
| nama_kurikulum | string |
| tahun_berlaku | year |
| beban_studi | string |
| deskripsi | text |
| status | string (Draft/Aktif/Arsip) |

**Relasi**: `belongsTo(ProgramStudi)`, `hasMany(ProfilLulusan)`, `hasMany(Cpl)`, `hasMany(MataKuliah)`, `hasMany(BahanKajian)`, `hasMany(Cpmk)`
**Accessor**: `getTotalSksAttribute()`

### 7. MataKuliah
| Field | Type |
|-------|------|
| kurikulum_id | FK |
| kode | string |
| nama | string |
| sks_teori | integer |
| sks_praktikum | integer |
| semester | integer |
| jenis | string |

**Relasi**: `belongsTo(Kurikulum)`, `belongsToMany(BahanKajian)`, `belongsToMany(Cpmk)`, `belongsToMany(Cpl)`, `hasMany(Pengampu)`, `hasOne(Rps)`
**Accessor**: `getTotalSksAttribute()`

### 8. ProfilLulusan (PL)
| Field | Type |
|-------|------|
| kurikulum_id | FK |
| kode_pl | string |
| nama_pl | string |
| profesi | string |

**Relasi**: `belongsTo(Kurikulum)`, `belongsToMany(Cpl)` via `profil_lulusan_cpl`

### 9. Cpl (Capaian Pembelajaran Lulusan)
| Field | Type |
|-------|------|
| kurikulum_id | FK |
| kode_cpl | string |
| deskripsi | text |

**Relasi**: `belongsTo(Kurikulum)`, `belongsToMany(ProfilLulusan)`, `belongsToMany(BahanKajian)`, `belongsToMany(Cpmk)`, `belongsToMany(MataKuliah)`

### 10. Cpmk (Capaian Pembelajaran Mata Kuliah)
| Field | Type |
|-------|------|
| kurikulum_id | FK |
| kode_cpmk | string |
| deskripsi | text |

**Relasi**: `belongsTo(Kurikulum)`, `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)`

### 11. BahanKajian (BK)
| Field | Type |
|-------|------|
| kurikulum_id | FK |
| kode_bk | string |
| nama_bk | string |
| referensi | text |

**Relasi**: `belongsTo(Kurikulum)`, `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)`

### 12. Rps (Rencana Pembelajaran Semester)
| Field | Type |
|-------|------|
| mata_kuliah_id | FK (unique) |
| kode_rps | string nullable |
| semester | integer |
| dosen_pengampu | string |
| deskripsi_mata_kuliah | text nullable |
| status | string (Draft/Diajukan/Revisi/Disetujui) |
| disetujui_oleh | FK ke users nullable |
| tanggal_disetujui | datetime nullable |
| catatan_revisi | text nullable |

**Relasi**: `belongsTo(MataKuliah)`, `hasMany(RpsPertemuan)`, `hasMany(RpsPenilaian)`, `hasOne(RpsPenilaian)`, `belongsTo(User, disetujui_oleh)`

### 13. RpsPertemuan
| Field | Type |
|-------|------|
| rps_id | FK |
| minggu | integer |
| sub_cpmk | string |
| materi | text |
| metode | string |
| pengalaman_belajar | text |
| indikator | text |
| bobot | string |

### 14. RpsPenilaian
| Field | Type |
|-------|------|
| rps_id | FK |
| tugas, quiz, uts, uas, praktikum, project | string nullable |

### 15. Pengampu
| Field | Type |
|-------|------|
| krs_id | FK nullable |
| dosen_id | FK |
| mata_kuliah_id | FK |
| tahun_akademik_id | FK |
| semester_akademik | string |
| kelas | string |

**Relasi**: `belongsTo(Krs)`, `belongsTo(Dosen)`, `belongsTo(MataKuliah)`, `belongsTo(TahunAkademik)`, `belongsToMany(Mahasiswa)` via `pengampu_mahasiswa`

### 16. Krs (Kartu Rencana Studi)
| Field | Type |
|-------|------|
| program_studi_id | FK |
| mata_kuliah_id | FK |
| dosen_id | FK |
| tahun_akademik_id | FK |
| kelas | string(10) |

**Relasi**: `belongsTo(ProgramStudi)`, `belongsTo(MataKuliah)`, `belongsTo(Dosen)`, `belongsTo(TahunAkademik)`, `belongsToMany(Mahasiswa)` via `krs_mahasiswa`, `hasOne(Pengampu)`
**Unique**: (mata_kuliah_id, tahun_akademik_id, dosen_id, kelas)

### 17. SemesterMahasiswa
| Field | Type |
|-------|------|
| mahasiswa_id | FK |
| tahun_akademik_id | FK |
| semester | integer |
| status | string |

### 18. MahasiswaTahunAkademik
| Field | Type |
|-------|------|
| mahasiswa_id | FK |
| tahun_akademik_id | FK |
| semester | integer |
| status | string |

---

## 🔐 Auth & Role System

### Middleware (bootstrap/app.php)
```php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'Kaprodi' => \App\Http\Middleware\KaprodiMiddleware::class,
]);
```

### RoleMiddleware
```php
public function handle(Request $request, Closure $next, ...$roles)
{
    if (!auth()->check()) abort(403);
    if (!in_array(auth()->user()->role, $roles)) abort(403);
    return $next($request);
}
```

### KaprodiMiddleware (custom logic)
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) abort(403);
    if (auth()->user()->role === 'admin' || auth()->user()->isKaprodi()) {
        return $next($request);
    }
    abort(403);
}
```

### User Model — Role Helpers
```php
public function isAdmin()    { return $this->role === 'admin'; }
public function isDirektur() { return $this->role === 'direktur'; }
public function isDosen()    { return $this->role === 'dosen'; }
public function isKaprodi()  {
    return $this->role === 'dosen'
        && strtolower($this->dosen?->jabatan ?? '') === 'kaprodi';
}
```

### Role-based Sidebar (layouts/app.blade.php)
```blade
@auth
    @if(auth()->user()->role == 'admin')
        @include('layouts.sidebar.admin')
    @elseif(auth()->user()->role == 'direktur')
        @include('layouts.sidebar.direktur')
    @elseif(auth()->user()->dosen &&
             strtolower(auth()->user()->dosen->jabatan) == 'kaprodi')
        @include('layouts.sidebar.kaprodi')
    @else
        @include('layouts.sidebar.dosen')
    @endif
@endauth
```

---

## 🧭 Route Map

### Public
| URI | Middleware |
|-----|-----------|
| `/` | web |
| `/dashboard` | auth, verified |

### Profile (auth)
| Method | URI | Name |
|--------|-----|------|
| GET/PATCH/DELETE | `/profile` | profile.* |

### Dosen Self (auth)
| Method | URI | Name |
|--------|-----|------|
| GET | `/dosen/self` | dosen.self |
| GET | `/dosen/self/riwayat` | dosen.self.riwayat |

### Shared Read-Only (Admin + Direktur) `role:admin,direktur`
- `tahun-akademik.index`, `program-studi.index`, `dosen.index`, `mahasiswa.index`
- `pengampu.index`, `krs.index`, `krs.show`
- `tahun-akademik.mahasiswa.index`

### Admin Only `role:admin`
- Full CRUD: `tahun-akademik` (except index), `program-studi` (except index), `dosen` (except index), `mahasiswa` (except index), `pengampu` (except index/show/edit/update), `users`
- Actions: TA toggle activate, Mahasiswa TA manage, KRS create/store/destroy, Pengampu manage students

### All Authenticated (auth)
- `kurikulum.*` (CRUD + index + detail + aktifkan + indexByProgramStudi)
- `kurikulum.cpl.*`, `kurikulum.cpmk.*`, `kurikulum.bahan-kajian.*`, `kurikulum.profil-lulusan.*`
- Mapping matrices: `cpl-pl`, `bk-mk`, `cpl-bk-mk`, `cpl-cpmk-mk`, `pemenuhan-cpl`
- `kurikulum.mata-kuliah.*` (index, struktur)
- `mata-kuliah.rps.*` (nested resource)
- `rps.ajukan`, `rps.ekstrak-pdf`
- `rps.pertemuan.*`, `rps.penilaian.*`
- `pengampu.lihat-kelas`

### Kaprodi (auth, Kaprodi)
| Method | URI | Name |
|--------|-----|------|
| GET | `/rps/pengajuan` | rps.pengajuan |
| PATCH | `/rps/{rps}/setujui` | rps.setujui |
| PATCH | `/rps/{rps}/revisi` | rps.revisi |
| GET/POST | `/kurikulum/{kurikulum}/mata-kuliah/create` | kurikulum.mata-kuliah.create/store |
| GET/PUT/DELETE | `/kurikulum/{kurikulum}/mata-kuliah/{mk}` | kurikulum.mata-kuliah.edit/update/destroy |
| PATCH | `/mata-kuliah/{mk}/aktifkan` | mata-kuliah.aktifkan |

### Direktur `role:direktur`
| GET | `/dashboard-direktur` | dashboard-direktur |

---

## 📜 Source Code Penting

### App Layout
```blade
{{-- resources/views/layouts/app.blade.php --}}
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
        @if(auth()->user()->role == 'admin')
            @include('layouts.sidebar.admin')
        @elseif(auth()->user()->role == 'direktur')
            @include('layouts.sidebar.direktur')
        @elseif(auth()->user()->dosen &&
                 strtolower(auth()->user()->dosen->jabatan) == 'kaprodi')
            @include('layouts.sidebar.kaprodi')
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
        {{-- toast_error, toast_warning, toast_info --}}
    </div>
    @stack('scripts')
</div>
</body>
</html>
```

### Sidebar Admin
```blade
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            {{-- SVG Logo --}}
            <span class="sidebar-brand">PIKOBE</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="...">Dashboard</a>
        <a href="{{ route('tahun-akademik.index') }}">Tahun Akademik</a>
        <a href="{{ route('program-studi.index') }}">Program Studi</a>
        <a href="{{ route('dosen.index') }}">Dosen</a>
        <a href="{{ route('mahasiswa.index') }}">Mahasiswa</a>
        <a href="{{ route('pengampu.index') }}">Pengampu</a>
        <a href="{{ route('krs.index') }}">KRS</a>
        <a href="{{ route('users.index') }}">Manajemen User</a>
    </nav>
    @include('layouts.sidebar.user-info')
</aside>
```

### Sidebar Kaprodi
```blade
<nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('dosen.self') }}">Data Diri</a>
    <a href="{{ route('program-studi.kurikulum', auth()->user()->dosen->program_studi_id) }}">Kurikulum</a>
    <a href="{{ route('rps.pengajuan') }}">Pengajuan RPS</a>
</nav>
```

### Sidebar Dosen
```blade
<nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('dosen.self') }}">Data Diri</a>
    <a href="{{ route('dosen.self.riwayat') }}">Riwayat Mengajar</a>
</nav>
```

### Sidebar Direktur
```blade
<nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('tahun-akademik.index') }}">Tahun Akademik</a>
    <a href="{{ route('program-studi.index') }}">Program Studi</a>
    <a href="{{ route('dosen.index') }}">Dosen</a>
    <a href="{{ route('mahasiswa.index') }}">Mahasiswa</a>
    <a href="{{ route('pengampu.index') }}">Pengampu</a>
    <a href="{{ route('krs.index') }}">KRS</a>
</nav>
```

### RPS Controller — Fitur Utama
```php
class RpsController extends Controller
{
    // CRUD standar untuk RPS (nested resource: mata-kuliah/{mk}/rps/{rps})

    public function ajukan(Rps $rps)
    {
        // Validasi: status Draft/Revisi, pertemuan harus ada, penilaian harus ada
        $rps->update(['status' => 'Diajukan', 'catatan_revisi' => null]);
    }

    public function pengajuan()
    {
        // Kaprodi melihat semua RPS yang diajukan/direvisi/disetujui
        // Filter by program_studi_id dari dosen yang login
    }

    public function revisi(Request $request, Rps $rps)
    {
        $rps->update(['status' => 'Revisi', 'catatan_revisi' => $request->catatan_revisi]);
    }

    public function setujui(Rps $rps)
    {
        $rps->update([
            'status' => 'Disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);
    }

    public function ekstrakPdf(Rps $rps)
    {
        // Hanya RPS Disetujui yang bisa di-PDF
        // Menggunakan barryvdh/laravel-dompdf
    }

    private function authorizeRps(MataKuliah $mataKuliah)
    {
        // Admin & Kaprodi: allowed
        // Dosen: hanya jika dia mengampu mata kuliah tsb
    }
}
```

### RPS Workflow Status
```
Draft → (ajukan) → Diajukan → (setujui) → Disetujui
                        ↓
                   (revisi)
                        ↓
                     Revisi → (ajukan) → Diajukan
```

---

## 🚀 Fitur Saat Ini (Ringkasan)

1. **Manajemen Program Studi** — CRUD 5 prodi (TI, AB, BD, TRPL, AK)
2. **Manajemen Tahun Akademik** — CRUD + toggle aktif
3. **Manajemen Dosen** — CRUD + login sebagai dosen (password default = NIDN) + jabatan kaprodi
4. **Manajemen Mahasiswa** — CRUD + pendaftaran per tahun akademik
5. **Manajemen Kurikulum** — CRUD + aktivasi/arsip
6. **Kurikulum Detail** — CPL, CPMK, Bahan Kajian, Profil Lulusan (nested CRUD)
7. **Matriks Mapping** — CPL-PL, BK-MK, CPL-BK-MK, CPL-CPMK-MK, Pemenuhan CPL
8. **Manajemen Mata Kuliah** — CRUD + aktivasi + struktur kurikulum
9. **RPS** — Full lifecycle: buat → isi pertemuan → isi penilaian → ajukan → revisi → setujui → PDF
10. **Pengampu & KRS** — Kelola kelas + daftar mahasiswa
11. **Role System** — Admin, Kaprodi, Dosen, Direktur dengan sidebar berbeda
12. **PDF Generation** — Ekstrak RPS ke PDF (hanya jika status Disetujui)

---

## ⚙️ Tech Stack Lengkap

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Frontend** | Blade templates + Alpine.js 3 |
| **CSS** | Tailwind CSS 3 + custom CSS (1277 baris) |
| **Build** | Vite 7 + laravel-vite-plugin |
| **Database** | SQLite (dev), MySQL (via Laragon), SQLite :memory: (tests) |
| **Session** | Database driver |
| **Queue** | Database driver |
| **Cache** | Database driver |
| **PDF** | barryvdh/laravel-dompdf 3.1 |
| **Auth** | Laravel Breeze 2.4 |
| **Testing** | PHPUnit 11.5 |
| **Code Style** | Laravel Pint 1.24 |

### composer.json — Key Dependencies
```json
{
    "require": {
        "php": "^8.2",
        "barryvdh/laravel-dompdf": "^3.1",
        "laravel/framework": "^12.0",
        "laravel/tinker": "^2.10.1"
    },
    "require-dev": {
        "laravel/breeze": "^2.4",
        "laravel/pint": "^1.24",
        "laravel/sail": "^1.41",
        "phpunit/phpunit": "^11.5.50"
    }
}
```

### package.json — Key Dependencies
```json
{
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.2",
        "@tailwindcss/vite": "^4.0.0",
        "alpinejs": "^3.4.2",
        "autoprefixer": "^10.4.2",
        "axios": "^1.11.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^2.0.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.1.0",
        "vite": "^7.0.7"
    }
}
```

### .env.example — Konfigurasi
```
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## 📱 Tampilan (Views)

### Dashboard
- Visi & Misi kampus
- Statistik: Total Dosen, Total Mahasiswa, Tahun Akademik Aktif
- Card grid Program Studi (klik → lihat kurikulum)
- Role-based: Admin/Kaprodi/Dosen/Direktur punya dashboard masing-masing

### Layout Structure
- **Sidebar** (left, fixed) — role-based navigation
- **Main content** — `@yield('content')`
- **Toast container** — success/error/warning/info notifications
- **Guest layout** — centered auth card

---

## 🗺️ Relasi Database (ER Visual)

```
ProgramStudi
  ├── Dosen (1:N)
  ├── Mahasiswa (1:N)
  └── Kurikulum (1:N)
        ├── ProfilLulusan (1:N) ──┬── (N:M) ── Cpl
        ├── Cpl (1:N) ──────────────┬── (N:M) ── Cpmk
        ├── Cpmk (1:N) ─────────────┤
        ├── BahanKajian (1:N) ──────┤
        └── MataKuliah (1:N) ───────┘
              └── Rps (1:1)
                    ├── RpsPertemuan (1:N)
                    └── RpsPenilaian (1:1)

User (1:1) ── Dosen (1:N) ── Pengampu (N:M) ── Mahasiswa
                                  └── Krs (1:1)

TahunAkademik
  ├── SemesterMahasiswa (1:N)
  ├── Pengampu (1:N)
  ├── Krs (1:N)
  └── MahasiswaTahunAkademik (1:N)
```

---

## 💡 Ide untuk LMS (Learning Management System)

Berdasarkan struktur di atas, berikut area yang bisa dikembangkan untuk LMS:

### 1. Manajemen Materi Perkuliahan
- Setiap RpsPertemuan bisa dikaitkan dengan file materi (PDF, video, dokumen)
- Upload/download file per pertemuan
- Modul/Topik untuk mengorganisir materi

### 2. Tugas & Pengumpulan
- Dosen membuat tugas (bisa per pertemuan atau per CPMK)
- Mahasiswa mengumpulkan file jawaban
- Deadline submission
- Penilaian & feedback dari dosen
- Nilai otomatis masuk ke RpsPenilaian

### 3. Forum Diskusi
- Forum per mata kuliah
- Thread per pertemuan/topik
- Dosen sebagai moderator

### 4. Quiz & Ujian Online
- Quiz online dengan soal pilihan ganda/esai
- Timer otomatis
- Penilaian otomatis untuk PG
- Bank soal per CPMK

### 5. Absensi/Presensi
- Dosen membuka sesi presensi
- Mahasiswa check-in (QR code / kode)
- Rekap kehadiran per pertemuan

### 6. Nilai Akhir & Transkrip
- Bobot penilaian sesuai RpsPenilaian
- Input nilai komponen (tugas, quiz, UTS, UAS, praktikum, project)
- Kalkulasi nilai akhir otomatis
- Transkrip nilai per semester

### 7. Kalender Akademik
- Jadwal perkuliahan
- Jadwal ujian
- Deadline tugas
- Integrasi dengan TahunAkademik

### 8. Notifikasi
- Pengingat tugas mendekati deadline
- Pengumuman dari dosen
- Notifikasi pengumpulan tugas

### 9. Integrasi dengan Data Existing
- Mahasiswa & Dosen sudah ada (tinggal pakai)
- Mata Kuliah, Pengampu, KRS sudah ada (untuk mapping kelas)
- RPS dan CPMK sudah ada (pedoman pembelajaran)
- Tahun Akademik sudah ada (untuk filter semester)

---

## 🔧 Perintah Penting

| Perintah | Fungsi |
|----------|--------|
| `composer run dev` | Jalankan dev server + queue + logs + Vite |
| `composer run test` | Clear config + jalankan semua test |
| `composer run setup` | Setup pertama (install, key, migrate, npm) |
| `npm run build` | Vite production build |
| `php artisan test` | Jalankan PHPUnit |
| `php artisan test --filter=NamaTest` | Jalankan test spesifik |
| `php artisan migrate` | Jalankan migrasi |

---

*Dokumen ini dibuat pada 28 Juli 2026 untuk keperluan brainstorming pengembangan sistem LMS dengan AI.*
