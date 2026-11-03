# Alur Kurikulum OBE - Eduva (Tanpa LMS)

Dokumen ini menjelaskan alur perancangan dan pemetaan kurikulum berbasis *Outcome-Based Education* (OBE) pada aplikasi **Eduva**. Bagian **LMS, RPS pengajaran harian, penilaian pembelajaran, dan forum kelas** dikecualikan dari penjelasan ini.

## 1. Tujuan

Alur kurikulum ini bertujuan untuk menjamin keterkaitan yang sistematis antara:

1. Kebutuhan lulusan (*Profil Lulusan*)
2. Capaian yang diharapkan (*CPL*)
3. Materi pembelajaran (*Bahan Kajian*)
4. Mata Kuliah (*Mata Kuliah*)
5. Capaian per mata kuliah (*CPMK*)
6. Keterkaitan antar komponen melalui matriks pemetaan

Sehingga terbentuk kurikulum yang terukur, konsisten, dan siap digunakan pada proses perencanaan pembelajaran.

## 2. Hierarki Kurikulum OBE

Hierarki yang diterapkan pada Eduva:

```text
Program Studi
└── Kurikulum (Tahun Berlaku, Status: Draft | Aktif | Arsip)
    ├── Profil Lulusan (PL)
    ├── Capaian Pembelajaran Lulusan (CPL)
    ├── Bahan Kajian (BK)
    ├── Mata Kuliah (MK)
    └── Capaian Pembelajaran Mata Kuliah (CPMK)
```

Pemetaan yang menghubungkan komponen di atas:

| Pemetaan | Keterangan |
| --- | --- |
| `PL - CPL` | Menghubungkan Profil Lulusan dengan CPL yang dibebankan padanya |
| `BK - MK` | Menentukan Bahan Kajian mana yang termuat pada suatu Mata Kuliah |
| `MK - CPMK` | Menentukan CPMK yang menjadi capaian dari suatu Mata Kuliah |
| `CPL - BK - MK` | Menelusuri kontribusi Bahan Kajian dan Mata Kuliah terhadap pencapaian CPL |
| `CPL - CPMK - MK` | Menelusuri kontribusi CPMK per Mata Kuliah terhadap pencapaian CPL |
| `Pemenuhan CPL` | Rekapitulasi sejauh mana CPL telah dipenuhi oleh keseluruhan Mata Kuliah dalam kurikulum |

## 3. Alur Perancangan Kurikulum

Alur ini mengacu pada route & controller di `routes/web.php` pada grup kurikulum.

### Tahap 1. Persiapan Kurikulum

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Buat Kurikulum | `KurikulumController@create` / `store` | Dibuat per Program Studi, berisi `nama_kurikulum`, `tahun_berlaku`, `beban_studi`, `deskripsi`. |
| Pengaturan Status | `KurikulumController@aktifkan` | Hanya **satu** kurikulum yang dapat berstatus `Aktif` dalam satu Program Studi. Kurikulum lain menjadi `Arsip`. |
| Lihat Detail Kurikulum | `KurikulumController@detail` | Menampilkan ringkasan seluruh komponen kurikulum beserta relasinya. |

> Akses mutasi: **Admin** dan **Kaprodi**. Akses baca: seluruh user yang telah login.

### Tahap 2. Menentukan Profil Lulusan (PL)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Kelola PL | `ProfilLulusanController` | Menambahkan profil lulusan berupa `kode_pl`, `nama_pl`, dan `profesi` yang diharapkan dari lulusan prodi. |
| Import PL | `ProfilLulusanController@import` | Mendukung impor massal menggunakan template. |

PL menjadi dasar perumusan CPL yang relevan dengan peran/profesi lulusan.

### Tahap 3. Merumuskan Capaian Pembelajaran Lulusan (CPL)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Kelola CPL | `CplController` | Merumuskan CPL per kurikulum dengan `kode_cpl` dan `deskripsi`. |
| Pemetaan PL-CPL | `CplPlController@update` | Menentukan CPL mana yang mendukung masing-masing Profil Lulusan. |
| Import CPL | `CplController@import` | Mendukung impor massal menggunakan template. |

### Tahap 4. Menyusun Bahan Kajian (BK)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Kelola BK | `BahanKajianController` | Menyusun cakupan materi inti dengan `kode_bk`, `nama_bk`, dan `deskripsi`. |
| Import BK | `BahanKajianController@import` | Mendukung impor massal menggunakan template. |

Bahan Kajian menjadi jembatan antara ranah keilmuan dengan mata kuliah yang dirancang.

### Tahap 5. Menyusun Mata Kuliah (MK)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Kelola MK | `MataKuliahController` | Menentukan mata kuliah per kurikulum dengan `kode`, `nama`, `sks_teori`, `sks_praktikum`, `semester`, dan `jenis`. |
| Struktur Kurikulum | `MataKuliahController@struktur` | Menampilkan distribusi MK per semester untuk melihat beban belajar. |
| Import MK | `MataKuliahController@import` | Mendukung impor massal menggunakan template. |

Accessor `getTotalSksAttribute()` pada model `MataKuliah` menghitung total SKS (`sks_teori + sks_praktikum`).

### Tahap 6. Pemetaan Bahan Kajian ke Mata Kuliah (BK-MK)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Pemetaan BK-MK | `BahanKajianMataKuliahController@update` | Menentukan Bahan Kajian mana yang dibebankan pada tiap Mata Kuliah. |

Hasil pemetaan menjawab: *"Mata Kuliah ini mengkaji bahan kajian apa saja?"*

### Tahap 7. Merumuskan Capaian Pembelajaran Mata Kuliah (CPMK)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Kelola CPMK | `CpmkController` | Merumuskan CPMK per kurikulum dengan `kode_cpmk` dan `deskripsi`. |
| Import CPMK | `CpmkController@import` | Mendukung impor massal menggunakan template. |

CPMK dirumuskan dengan mempertimbangkan CPL, Bahan Kajian, dan karakteristik Mata Kuliah.

### Tahap 8. Pemetaan MK - CPMK

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Pemetaan MK-CPMK | `MkCpmkController@update` | Mengaitkan CPMK dengan Mata Kuliah pemilik capaian tersebut. |

### Tahap 9. Pemetaan Lintas-Komponen (CPL-BK-MK & CPL-CPMK-MK)

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Pemetaan CPL-BK-MK | `CplBkMkController@store` | Menentukan relasi CPL terhadap kombinasi Bahan Kajian dan Mata Kuliah untuk menelusuri kontribusi materi terhadap CPL. |
| Pemetaan CPL-CPMK-MK | `CplCpmkMkController@store` | Menentukan relasi CPL terhadap CPMK per Mata Kuliah untuk melihat kontribusi capaian mata kuliah terhadap CPL. |

Kedua pemetaan menjadi dasar analisis *traceability* OBE dalam kurikulum.

### Tahap 10. Analisis Pemenuhan CPL

| Aksi | Controller | Keterangan |
| --- | --- | --- |
| Pemenuhan CPL | `PemenuhanCplsController@index` / `store` | Menampilkan rekap keterkaitan CPL dengan seluruh Mata Kuliah hasil pemetaan. Digunakan untuk menilai apakah seluruh CPL telah terwakili oleh beban pembelajaran kurikulum. |

## 4. Relasi Model Terkait Kurikulum

| Model | Relasi | Keterangan |
| --- | --- | --- |
| `Kurikulum` | `hasMany(ProfilLulusan, Cpl, Cpmk, BahanKajian, MataKuliah)` | Induk seluruh komponen kurikulum. |
| `ProfilLulusan` | `belongsToMany(Cpl)` via `profil_lulusan_cpl` | Pemetaan PL-CPL. |
| `Cpl` | `belongsToMany(ProfilLulusan)`, `belongsToMany(BahanKajian)`, `belongsToMany(Cpmk)`, `belongsToMany(MataKuliah)` | Terhubung ke hampir seluruh komponen untuk pelacakan CPL. |
| `BahanKajian` | `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)` via `bahan_kajian_mata_kuliah` | Pemetaan BK-MK dan kontribusinya ke CPL. |
| `MataKuliah` | `belongsTo(Kurikulum)`, `belongsToMany(BahanKajian)`, `belongsToMany(Cpmk)`, `belongsToMany(Cpl)`, `hasMany(Pengampu)` | Entitas pembelajaran utama dalam struktur kurikulum. |
| `Cpmk` | `belongsToMany(Cpl)`, `belongsToMany(MataKuliah)` via `cpmk_mata_kuliah` | Capaian pembelajaran per mata kuliah yang dikaitkan ke CPL. |

## 5. Hak Akses

| Peran | Hak Akses |
| --- | --- |
| **Admin** | Akses penuh: buat, ubah, hapus, aktifkan kurikulum, kelola seluruh komponen dan matriks pemetaan. |
| **Kaprodi** | Dapat mengelola kurikulum prodi, seluruh komponen OBE, dan matriks pemetaan. |
| **Dosen, Direktur** | Hanya **baca** seluruh halaman kurikulum, detail, struktur MK, CPL, CPMK, BK, PL, pemetaan, dan pemenuhan CPL. |
| **Mahasiswa** | Tidak memiliki akses modul perancangan kurikulum. |

Mutasi diatur middleware `role:admin,kaprodi`, akses baca cukup `auth`.

## 6. Alur Singkat

```text
1. Program Studi menentukan Kurikulum (Draft)
2. Rumuskan Profil Lulusan (PL)
3. Rumuskan CPL -> Petakan PL-CPL
4. Susun Bahan Kajian (BK)
5. Susun Mata Kuliah per semester -> Petakan BK-MK
6. Rumuskan CPMK -> Petakan MK-CPMK
7. Lakukan pemetaan CPL-BK-MK dan CPL-CPMK-MK
8. Evaluasi Pemenuhan CPL
9. Aktifkan Kurikulum (menjadi Kurikulum Aktif untuk Prodi)
```

## 7. Catatan Penting

- **Status Kurikulum**: `Draft` (dalam penyusunan), `Aktif` (menjadi acuan), `Arsip` (tidak lagi menjadi acuan aktif).
- **Satu sumber acuan**: Hanya kurikulum berstatus `Aktif` yang menjadi landasan struktur dan pemetaan kurikulum prodi.
- **Berorientasi OBE**: Pemetaan dibangun untuk menjamin keterkaitan terbalik (*backward design*), dimulai dari Profil Lulusan hingga Mata Kuliah.
- **Terpisah dari LMS**: Dokumen ini **tidak** membahas kelas, materi, tugas, absensi, forum, atau aktivitas pembelajaran harian. Fokus hanya pada **perancangan, pemetaan, dan analisis kurikulum**.
