# PIKOBE Polsa — Konteks Project (untuk Brainstorming & Pengembangan)

Dokumen ini merangkum seluruh konteks project **PIKOBE** secara menyeluruh agar bisa digunakan sebagai prompt/knowledge-base untuk AI (mis. Gemini) saat brainstorming, perencanaan, dan pengembangan fitur.

---

## 1. Ringkasan Project

| Item | Detail |
|---|---|
| **Nama Aplikasi** | PIKOBE (Politeknik Sawunggalih Aji Information System) |
| **Lembaga** | Politeknik Sawunggalih Aji (Polsa) |
| **Domain** | Manajemen akademik: Program Studi, Kurikulum (CPL/CPMK/BK), RPS, KRS, Pengampu, dan **LMS (Learning Management System)** |
| **Fase saat ini** | Kurikulum + RPS selesai; **LMS sudah diimplementasikan tahap awal** (materi, tugas, pengumpulan, penilaian, forum diskusi) untuk Dosen & Mahasiswa |
| **Target pengguna** | Admin, Direktur, Kaprodi, Dosen, Mahasiswa |
| **Bahasa UI** | Indonesia |
| **Status repo** | Git (GitHub: `fachry16/pkl-polsa`); branch aktif: `dashboardlms_mahasiswa` (tidak ada remote; remote hanya `main`) |

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | PHP ^8.2, Laravel ^12.0 |
| **Frontend** | Blade template + Alpine.js ^3.4 |
| **CSS** | Tailwind CSS ^3.1 + custom CSS |
| **Build tool** | Vite ^7.0.7 + laravel-vite-plugin ^2.0.0 |
| **Database** | SQLite (dev/.env.example), MySQL via Laragon (.env produksi), SQLite `:memory:` (testing) |
| **Session / Cache / Queue** | Semua memakai driver `database` |
| **Auth** | Laravel Breeze ^2.4 (login, register, forgot password, email verification) |
| **PDF** | barryvdh/laravel-dompdf ^3.1 (ekstrak RPS → PDF) |
| **Testing** | PHPUnit ^11.5.50 (suite Unit + Feature) |
| **Code style** | Laravel Pint ^1.24 |

### composer.json (dependensi utama)
```json
{
  "require": {
    "php": "^8.2",
    "barryvdh/laravel-dompdf": "^3.1",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/breeze": "^2.4",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.24",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^11.5.50"
  }
}
```

### package.json (dependensi frontend)
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

---

## 3. Struktur Direktori

```
app/
  Console/Commands/MahasiswaBuatAkun.php     # artisan: buat akun login massal mahasiswa
  Http/
    Controllers/                             # resource + action controller
      Concerns/
        AuthorizesKurikulum.php              # trait auth akses kurikulum
        AuthorizesRps.php                    # trait auth akses RPS
      Auth/                                  # Breeze
      LmsController.php                      # LMS sisi dosen (kelas)
      LmsMateriController.php
      LmsTugasController.php                 # termasuk rekap nilai + sync ke RPS
      LmsForumController.php
      LmsMahasiswaController.php             # LMS sisi mahasiswa
      ... (Kurikulum, Cpl, Cpmk, Rps, Krs, Pengampu, Mahasiswa, dll.)
    Middleware/
      RoleMiddleware.php                     # alias: 'role'
      KaprodiMiddleware.php                  # alias: 'Kaprodi'
  Models/                                    # 21 model (lihat §4)
bootstrap/app.php                            # alias middleware 'role' & 'Kaprodi'
database/
  migrations/                                # ±37 file (users → LMS forum)
  seeders/                                   # DatabaseSeeder, ProgramStudiSeeder
resources/
  views/
    layouts/                                 # app, guest, navigation
    layouts/sidebar/                         # admin, direktur, dosen, kaprodi, mahasiswa, user-info
    lms/                                     # dosen: index, show, materi, tugas, forum, rekap
    lms/mahasiswa/                           # mahasiswa: index, show
    dosen/, mahasiswa/, krs/, pengampu/,
    kurikulum/, rps/, cpl/, cpmk/, bahan-kajian/, ...
routes/
  web.php     (601 baris, semua route web)
  auth.php    (Breeze)
  console.php (artisan commands)
tests/        # Unit + Feature (saat ini masih template Breeze)
```

---

## 4. Model & Skema Database

### 4.1 User (`users`)
- `name`, `email` (unique), `password` (hashed), `role`, `email_verified_at`
- `role` = **enum: `admin`, `direktur`, `dosen`, `mahasiswa`** (default `dosen`)
- Relasi: `hasOne(Dosen)`, `hasOne(Mahasiswa)`
- Helper: `isAdmin()`, `isDirektur()`, `isDosen()`, `isKaprodi()`, `isMahasiswa()`
  - `isKaprodi()` = role `dosen` **dan** `dosen.jabatan` (lowercase) === `'kaprodi'`

### 4.2 ProgramStudi (`program_studis`)
- `kode_prodi`, `nama_prodi`, `jenjang` (D3/D4), `akreditasi`
- Seeder 5 prodi: TI(D3), AB(D3), BD(D4), TRPL(D4), AK(D3)
- Relasi: `hasMany(Dosen)`, `hasMany(Mahasiswa)`, `hasMany(Kurikulum)`

### 4.3 TahunAkademik (`tahun_akademiks`)
- `tahun`, `semester` (Ganjil/Genap), `is_active` (hanya 1 aktif)
- Relasi: `hasMany(SemesterMahasiswa)`, `hasMany(Pengampu)`, `hasMany(MahasiswaTahunAkademik)`

### 4.4 Dosen (`dosens`)
- `user_id` (FK), `program_studi_id` (FK), `nidn` (unique), `jabatan` (contoh: `kaprodi`, `dosen`)
- Relasi: `belongsTo(User)`, `belongsTo(ProgramStudi)`, `hasMany(Pengampu)`

### 4.5 Mahasiswa (`mahasiswas`)
- `user_id` (FK, nullable — akun login dibuat otomatis), `program_studi_id` (FK), `nim` (unique), `nama`, `angkatan`, `status`
- Relasi: `belongsTo(User)`, `belongsTo(ProgramStudi)`, `hasMany(SemesterMahasiswa)`, `belongsToMany(TahunAkademik)` via `semester_mahasiswas`, `belongsToMany(Pengampu)` via `pengampu_mahasiswa`, `hasMany(LmsSubmission)`
- **Akun login mahasiswa**: email = `{nim}@polsa.ac.id`, password default = NIM. Dibuat otomatis saat input mahasiswa atau via artisan `php artisan mahasiswa:buat-akun`

### 4.6 Kurikulum (`kurikulums`)
- `program_studi_id` (FK), `nama_kurikulum`, `tahun_berlaku`, `beban_studi`, `deskripsi`, `status` (Draft/Aktif/Arsip)
- Relasi: `hasMany(ProfilLulusan)`, `hasMany(Cpl)`, `hasMany(MataKuliah)`, `hasMany(BahanKajian)`, `hasMany(Cpmk)`
- Accessor: `getTotalSksAttribute()` = sum sks_teori + sks_praktikum seluruh MK

### 4.7 MataKuliah (`mata_kuliahs`)
- `kurikulum_id` (FK), `kode`, `nama`, `sks_teori`, `sks_praktikum`, `semester`, `jenis`
- Relasi: `belongsTo(Kurikulum)`, `belongsToMany(BahanKajian)` via `bahan_kajian_mata_kuliah`, `belongsToMany(Cpmk)` via `cpmk_mata_kuliah`, `belongsToMany(Cpl)` via `cpl_bahan_kajian_mata_kuliah`, `hasMany(Pengampu)`, `hasOne(Rps)`
- Accessor: `getTotalSksAttribute()`

### 4.8 ProfilLulusan (`profil_lulusans`) — PL
- `kurikulum_id` (FK), `kode_pl`, `nama_pl`, `profesi`
- Relasi: `belongsTo(Kurikulum)`, `belongsToMany(Cpl)` via `profil_lulusan_cpl`

### 4.9 Cpl (`cpls`) — Capaian Pembelajaran Lulusan
- `kurikulum_id` (FK), `kode_cpl`, `deskripsi`
- Relasi: `belongsToMany(ProfilLulusan)`, `belongsToMany(BahanKajian)`, `belongsToMany(Cpmk)` via `cpl_cpmk_semesters`, `belongsToMany(MataKuliah)`

### 4.10 Cpmk (`cpmks`) — Capaian Pembelajaran Mata Kuliah
- `kurikulum_id` (FK), `kode_cpmk`, `deskripsi`
- Relasi: `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)`

### 4.11 BahanKajian (`bahan_kajians`) — BK
- `kurikulum_id` (FK), `kode_bk`, `nama_bk`, `referensi`
- Relasi: `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)`

### 4.12 Rps (`rps`) — Rencana Pembelajaran Semester
- `mata_kuliah_id` (FK), `kode_rps` (nullable), `semester`, `dosen_pengampu`, `deskripsi_mata_kuliah`
- Workflow: `status` = **Draft → Diajukan → Disetujui** atau **Revisi**; `disetujui_oleh` (FK users, nullable), `tanggal_disetujui` (datetime), `catatan_revisi` (text)
- Relasi: `belongsTo(MataKuliah)`, `hasMany(RpsPertemuan)`, `hasMany(RpsPenilaian)` + `hasOne(RpsPenilaian)`, `belongsTo(User, disetujui_oleh)`

### 4.13 RpsPertemuan (`rps_pertemuans`)
- `rps_id` (FK), `minggu`, `sub_cpmk`, `materi`, `metode`, `pengalaman_belajar`, `indikator`, `bobot`
- Relasi: `hasMany(LmsMateri)`, `hasMany(LmsTugas)` — **jembatan antara RPS dan LMS**

### 4.14 RpsPenilaian (`rps_penilaians`)
- `rps_id` (FK), `tugas`, `quiz`, `uts`, `uas`, `praktikum`, `project` (decimal 5,2, default 0)
- Satu RPS punya satu baris penilaian (default komponen)

### 4.15 Pengampu (`pengampus`) — kelas LMS (dosen mengampu MK)
- `krs_id` (FK, nullable), `dosen_id`, `mata_kuliah_id`, `tahun_akademik_id`, `semester_akademik` (Ganjil/Genap), `kelas`
- **Satu Pengampu = satu kelas di LMS**. Dibuat otomatis saat KRS dibuat.
- Relasi: `belongsTo(Krs)`, `belongsTo(Dosen)`, `belongsTo(MataKuliah)`, `belongsTo(TahunAkademik)`, `belongsToMany(Mahasiswa)` via `pengampu_mahasiswa`, `hasMany(LmsMateri)`, `hasMany(LmsTugas)`, `hasMany(LmsForumDiskusi)`

### 4.16 Krs (`krs`) — Kartu Rencana Studi (kelas)
- `program_studi_id`, `mata_kuliah_id`, `dosen_id`, `tahun_akademik_id`, `kelas`
- Relasi: `belongsToMany(Mahasiswa)` via `krs_mahasiswa`, `hasOne(Pengampu)` (lewat `krs_id`)
- **Alur**: Admin/Kaprodi buat KRS → otomatis membuat Pengampu → tambah mahasiswa ke KRS → otomatis masuk ke kelas Pengampu (sync via `krs_mahasiswa` ↔ `pengampu_mahasiswa`)

### 4.17 SemesterMahasiswa (`semester_mahasiswas`)
- `mahasiswa_id`, `tahun_akademik_id`, `semester`, `status`

### 4.18 MahasiswaTahunAkademik (`mahasiswa_tahun_akademiks`)
- `mahasiswa_id`, `tahun_akademik_id`, `semester`, `status`
- Digunakan admin untuk mendaftarkan mahasiswa ke tahun akademik

### 4.19 LmsMateri (`lms_materis`) — **LMS**
- `pengampu_id` (FK), `rps_pertemuan_id` (FK, nullable), `judul`, `deskripsi`, `file_path`, `link_external`
- File disimpan di storage `public` (folder `lms/materi`), upload max 50 MB

### 4.20 LmsTugas (`lms_tugas`) — **LMS**
- `pengampu_id` (FK), `rps_pertemuan_id` (FK, nullable), `judul`, `instruksi`, `file_lampiran`, `deadline` (datetime), `bobot_nilai` (int, default 100)
- Relasi: `belongsTo(Pengampu)`, `belongsTo(RpsPertemuan)`, `hasMany(LmsSubmission)`
- File lampiran di folder `lms/tugas`

### 4.21 LmsSubmission (`lms_submissions`) — **LMS**
- `lms_tugas_id` (FK), `mahasiswa_id` (FK), `file_jawaban`, `catatan_mahasiswa`, `nilai` (decimal 5,2, nullable), `catatan_dosen`, `dikumpulkan_pada` (datetime)
- **Unique**: `[lms_tugas_id, mahasiswa_id]` → satu mahasiswa satu pengumpulan per tugas (updateOrCreate)
- Helper: `isTerlambat()` (bandingkan `dikumpulkan_pada` vs `deadline`)
- File jawaban di folder `lms/submissions`

### 4.22 LmsForumDiskusi (`lms_forum_diskusis`) — **LMS**
- `pengampu_id` (FK), `user_id` (FK — dosen/mahasiswa), `parent_id` (FK self, nullable → balasan/reply), `pesan` (text), `file_path`, `link_external`
- Thread = `parent_id` null; balasan = `parent_id` merujuk thread
- Relasi: `belongsTo(Pengampu)`, `belongsTo(User)`, `belongsTo(self, parent_id)`, `hasMany(self, parent_id)` (replies, urut `oldest`)

---

## 5. Auth & Role System

### 5.1 Alias middleware (`bootstrap/app.php`)
```php
$middleware->alias([
    'role'    => \App\Http\Middleware\RoleMiddleware::class,
    'Kaprodi' => \App\Http\Middleware\KaprodiMiddleware::class,
]);
```

### 5.2 RoleMiddleware (`role:admin,direktur,kaprodi,dosen,...`)
- Menerima daftar role; jika salah satu cocok → lanjut.
- **Khusus** `kaprodi`: lolos jika `role === 'admin'` **atau** `isKaprodi()`.
- Selain itu cocok dengan `$user->role === $role`. Jika tidak ada yang cocok → `abort(403)`.

### 5.3 KaprodiMiddleware (`Kaprodi`)
- Lolos jika `role === 'admin'` atau `isKaprodi()`; selain itu `abort(403)`.

### 5.4 Akses per halaman (konsep)
| Halaman | Akses |
|---|---|
| Dashboard | Semua yang login |
| Tahun Akademik, Program Studi, Dosen, Mahasiswa, Pengampu (read) | Admin + Direktur + Kaprodi |
| CRUD semua master + Manajemen User + pengaturan aktif | **Admin saja** |
| KRS (index/show read) | Admin + Direktur + Kaprodi |
| KRS (create/store/hapus/tambah mahasiswa) | Admin + Kaprodi |
| Kurikulum & turunannya (CPL, CPMK, BK, PL, mapping, MK) | Admin + Kaprodi (mutasi); semua auth (read) |
| RPS CRUD + pertemuan + penilaian | Admin, Kaprodi, Dosen (dengan cek hak via `AuthorizesRps`; dosen hanya untuk MK yang diampunya) |
| Pengajuan/setujui/revisi RPS | Kaprodi (middleware `Kaprodi`) |
| Dashboard Direktur | Direktur |
| LMS Dosen (`/kelas/...`) | Dosen pengampu kelas tsb (cek `pengampu->dosen_id === user->dosen->id`) |
| LMS Mahasiswa (`/mahasiswa/kelas/...`) | Mahasiswa yang terdaftar di `pengampu_mahasiswa` |

### 5.5 Sidebar per role
`layouts/app.blade.php` memilih sidebar: `admin`, `direktur`, `dosen` (default), `kaprodi` (jika dosen.jabatan kaprodi), `mahasiswa`.

---

## 6. Route Map (`routes/web.php`, 601 baris)

### 6.1 Umum
- `GET /` → welcome; `GET /dashboard` → `DashboardController@index` (auth, verified)
- `profile.*` (auth)

### 6.2 Read-only bersama (Admin + Direktur + Kaprodi)
- `tahun-akademik.index`, `program-studi.index`, `dosen.index`, `dosen.riwayat`, `mahasiswa.index`, `pengampu.index`, `tahun-akademik.mahasiswa.index`

### 6.3 Admin only
- `tahun-akademik` (CRUD, except index + `aktifkan`), `program-studi`, `dosen`, `mahasiswa`, `pengampu` (store/destroy), `users` (full CRUD)
- `pengampu.kelas.mahasiswa.store/destroy`

### 6.4 KRS
- Read (Admin/Direktur/Kaprodi): `krs.index`, `krs.show`
- Mutasi (Admin/Kaprodi): `krs.create/store/destroy`, `krs.mahasiswa.store/destroy`

### 6.5 Kurikulum (nested di bawah `kurikulum/{kurikulum}`)
- Read semua auth: `kurikulum.detail`, `kurikulum.struktur` (struktur MK), `program-studi.kurikulum`
- Mutasi (Admin/Kaprodi): CRUD `kurikulum`, `cpl`, `cpmk`, `bahan-kajian`, `profil-lulusan`, `mata-kuliah` + `kurikulum.aktifkan`
- Matriks mapping (PUT/POST): `cpl-pl`, `bk-mk`, `cpl-bk-mk`, `cpl-cpmk-mk`, `pemenuhan-cpl`

### 6.6 RPS
- `mata-kuliah.rps.*` (nested resource), `rps.ajukan`, `rps.ekstrak-pdf`, `rps.pertemuan.*`, `rps.penilaian.*`
- Kaprodi: `rps.pengajuan`, `rps.setujui`, `rps.revisi`

### 6.7 Direktur
- `GET /dashboard-direktur`

### 6.8 LMS — Dosen (prefix `kelas`, name `lms.`)
```
GET  /kelas-saya                      lms.index
GET  /{pengampu}                      lms.show
GET  /{pengampu}/materi               lms.materi.index
POST /{pengampu}/materi               lms.materi.store
DELETE /{pengampu}/materi/{materi}    lms.materi.destroy
GET  /{pengampu}/tugas                lms.tugas.index
POST /{pengampu}/tugas                lms.tugas.store
GET  /{pengampu}/tugas/{tugas}        lms.tugas.show
PATCH /submission/{submission}/nilai  lms.submission.nilai
GET  /{pengampu}/forum                lms.forum.index
POST /{pengampu}/forum                lms.forum.store
GET  /{pengampu}/forum/{diskusi}/edit lms.forum.edit
PATCH /{pengampu}/forum/{diskusi}     lms.forum.update
DELETE /{pengampu}/forum/{diskusi}    lms.forum.destroy
GET  /{pengampu}/forum/{diskusi}/file lms.forum.file
GET  /{pengampu}/rekap-nilai          lms.tugas.rekap
POST /{pengampu}/sync-nilai           lms.tugas.sync
```

### 6.9 LMS — Mahasiswa (prefix `mahasiswa`, name `mahasiswa.lms.`)
```
GET  /kelas-saya                      mahasiswa.lms.index
GET  /kelas/{pengampu}                mahasiswa.lms.show
POST /kelas/{pengampu}/forum          mahasiswa.lms.forum.store
POST /tugas/{tugas}/kumpul            mahasiswa.lms.tugas.kumpul
```

---

## 7. Fitur LMS (implementasi saat ini)

### 7.1 Sisi Dosen (LmsController, LmsMateriController, LmsTugasController, LmsForumController)
- **Kelas LMS Saya** (`lms.index`): daftar pengampu pada tahun akademik aktif + count materi/tugas/diskusi.
- **Detail kelas** (`lms.show`): info MK + kelas, materi & tugas & forum terbaru (limit 5).
- **Materi**: tambah (judul, deskripsi, file/link) & hapus (file dihapus dari storage).
- **Tugas**: tambah (judul, instruksi, deadline, bobot nilai, file lampiran/link). Lihat daftar pengumpulan per mahasiswa, beri **nilai & catatan dosen** (`lms.submission.nilai`).
- **Rekap Nilai** (`lms.tugas.rekap`): tabel mahasiswa × tugas (nilai per tugas).
- **Sync ke RPS** (`lms.tugas.sync`): hitung rata-rata nilai tugas per mahasiswa → rata-rata kelas → `updateOrCreate` ke `rps_penilaians.tugas` untuk RPS dengan `mata_kuliah_id` yang sama.
- **Forum Diskusi**: buat thread/balasan dengan file/link, edit & hapus (hanya pemilik), download file inline.
- Semua aksi dosen diawali cek `abort_if(!$dosen || $pengampu->dosen_id !== $dosen->id, 403)`.

### 7.2 Sisi Mahasiswa (LmsMahasiswaController)
- **Kelas LMS Saya** (`index`): daftar kelas yang diikuti (dari `pengampu_mahasiswa`) pada tahun akademik aktif.
- **Detail kelas** (`show`): materi, tugas (dengan status sudah/belum kumpul via `LmsSubmission`), forum diskusi.
- **Kumpul tugas** (`storeSubmission`): upload file jawaban / link external / catatan; `updateOrCreate` per (tugas, mahasiswa) sehingga bisa mengumpulkan ulang.
- **Posting forum** (`storeForum`): thread & balasan (parent_id) dengan file/link.

### 7.3 Dashboard (DashboardController)
- **Statistik umum**: total dosen, total mahasiswa, tahun akademik aktif.
- **Dosen/Kaprodi**: kartu "Ruang Kelas LMS" — tiap kelas menampilkan jumlah materi, tugas, dan `submissions_belum_dinilai` (submission yang `nilai` masih null).
- **Mahasiswa**: 3 kartu stat (jumlah kelas, tugas aktif = deadline ≥ now, belum dikumpul), daftar **tugas mendekati deadline** (7 hari), **materi terbaru** (7 hari), dan **diskusi terbaru** (7 hari).

### 7.4 Keterbatasan / catatan teknis yang diketahui
- File disimpan di **disk `public`** (symlink `storage:link` harus aktif). Upload max **50 MB** (`max:51200`).
- `LmsSubmission` belum punya kolom `link_external` di migration (controller menerima `link_external` di validasi tapi field tidak tersimpan di DB saat ini — potensi perbaikan).
- `LmsTugas` / `LmsMateri` juga menerima `link_external` namun kolomnya tidak ada di migration (perlu dicek/ditambahkan jika ingin dipakai).
- Belum ada fitur quiz/ujian online, absensi, notifikasi real-time, kalender akademik.
- `rps_pertemuan_id` tersedia di LmsMateri/LmsTugas tapi belum diisi dari form (nullable).
- Tidak ada route API (murni web/monolitik).

---

## 8. Alur Kerja Utama (Business Flow)

### 8.1 RPS Workflow
```
Draft ──ajukan──▶ Diajukan ──setujui(kaprodi)──▶ Disetujui ──ekstrakPdf──▶ PDF
                    ▲                               │
                    └──────revisi + catatan_revisi───┘
```
- Syarat `ajukan`: status Draft/Revisi, punya ≥1 pertemuan, dan punya penilaian.
- PDF hanya untuk status `Disetujui`.

### 8.2 KRS → Pengampu → Kelas LMS
```
Admin/Kaprodi buat KRS (prodi, MK, dosen, TA, kelas)
   └─▶ otomatis membuat Pengampu (satu kelas)
Tambah mahasiswa ke KRS ──▶ sync otomatis ke pengampu_mahasiswa
   └─▶ mahasiswa muncul di "Kelas LMS Saya"
```

### 8.3 Alur belajar mengajar di LMS
```
Dosen: buat kelas (dari KRS) → upload materi → buat tugas (deadline + bobot)
Mahasiswa: buka kelas → baca materi → kumpul tugas (file) → post forum
Dosen: buka daftar pengumpulan → beri nilai + catatan → rekap → sync rata-rata ke RPS
```

---

## 9. Struktur View Penting

- `layouts/app.blade.php`: sidebar role-based + main content (`@yield('content')`) + toast container (`x-toast`).
- `layouts/sidebar/*`: `admin` (8 menu), `direktur`, `dosen` (Data Diri, Riwayat Mengajar, Kelas LMS), `kaprodi` (tambah Kurikulum/Pengajuan RPS/KRS), `mahasiswa` (Dashboard + Kelas LMS Saya).
- `dashboard.blade.php`: visi-misi + statistik + daftar program studi; blok khusus mahasiswa (tugas deadline, materi, diskusi) dan dosen/kaprodi (ruang kelas LMS).
- `lms/`: `index`, `show`, `materi/index`, `tugas/index`, `tugas/show` (daftar pengumpulan + form nilai), `tugas/rekap`, `forum/index`, `forum/edit`.
- `lms/mahasiswa/`: `index`, `show` (tab materi/tugas/forum + status submission).
- `components/file-link.blade.php`: komponen link unduhan file (baru, belum di-commit).
- `rps/pdf.blade.php` & `rps/preview-pdf.blade.php`: template PDF RPS via dompdf.

---

## 10. Database — Tabel Relasi (ER ringkas)

```
users (role: admin/direktur/dosen/mahasiswa)
  │ 1:1 dosens (program_studi_id → program_studis)
  └ 1:1 mahasiswas (program_studi_id → program_studis, nim)

program_studis ─1:N─ kurikulums
   kurikulums ─1:N─ profil_lulusans / cpls / cpmks / bahan_kajians / mata_kuliahs
   cpls N:M profil_lulusans (profil_lulusan_cpl)
   cpls N:M cpmks (cpl_cpmk_semesters)
   bahan_kajians N:M mata_kuliahs (bahan_kajian_mata_kuliah)
   cpls N:M bahan_kajians N:M mata_kuliahs (cpl_bahan_kajian_mata_kuliah)
   cpmks N:M mata_kuliahs (cpmk_mata_kuliah)
   mata_kuliahs 1:1 rps ─1:N─ rps_pertemuans / rps_penilaians

tahun_akademiks
   │ 1:N semester_mahasiswas (mahasiswa)
   │ 1:N mahasiswa_tahun_akademiks
   └ 1:N krs ─1:1─ pengampus ─N:M─ mahasiswas (pengampu_mahasiswa)
       │             │ N:M (lewat krs_mahasiswa)
       └─────────────┴ N:M mahasiswas
   pengampus 1:N lms_materis / lms_tugas / lms_forum_diskusis
   lms_tugas 1:N lms_submissions (N:M mahasiswas)
   lms_forum_diskusis 1:N (self, parent_id) balasan
```

---

## 11. Perintah Penting

| Perintah | Fungsi |
|---|---|
| `composer run dev` | Jalankan server + queue + log + Vite sekaligus |
| `composer run test` | `config:clear` lalu PHPUnit (semua test) |
| `composer run setup` | Setup penuh (composer install, .env, key, migrate, npm) |
| `php artisan test` | Jalankan PHPUnit |
| `php artisan test --filter=NamaTest` | Jalankan satu test |
| `php artisan migrate` | Jalankan migrasi |
| `php artisan mahasiswa:buat-akun` | Buat akun login semua mahasiswa tanpa user_id (email `{nim}@polsa.ac.id`, pass = NIM) |
| `npm run build` / `npm run dev` | Build / dev Vite |

---

## 12. Roadmap / Area Pengembangan Potensial (untuk diskusi dengan AI)

1. **Quiz & Ujian Online** — bank soal per CPMK, PG esai, timer, nilai otomatis (terkait `RpsPenilaian.quiz/uts/uas`).
2. **Absensi/Presensi** — sesi presensi per pertemuan (RpsPertemuan), QR/kode, rekap.
3. **Nilai Akhir & Transkrip** — kalkulasi nilai akhir sesuai bobot `RpsPenilaian`, transkrip per semester.
4. **Notifikasi** — email/push pengingat deadline tugas, pengumpulan, revisi RPS.
5. **Kalender Akademik & Jadwal** — integrasi TahunAkademik + Pengampu.
6. **Perbaikan LMS** — tambah kolom `link_external` di tabel LMS, pemilihan `rps_pertemuan_id` saat buat materi/tugas, versi/edit materi, soft-delete.
7. **Forum lebih kaya** — pin, mention, reaksi, lampiran gambar, unread.
8. **Integrasi nilai LMS → penilaian RPS** — sinkronisasi penuh komponen (tugas/quiz/uts/uas/praktikum/project), bukan hanya rata-rata tugas.
9. **Analitik/Dashboard lanjutan** — progres kelas, engagement mahasiswa, distribusi nilai.
10. **API/mobile** — route API untuk aplikasi mobile mahasiswa.

---

*Diperbarui: 11 Agustus 2026. Bersumber dari kondisi terkini kode (`routes/web.php`, controllers, models, migrations).*
