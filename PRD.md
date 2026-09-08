# Product Requirement Document (PRD) — Eduva

> **System Name**: Eduva (POLSA Academic, OBE Assessment & LMS Platform)  
> **Institution**: Politeknik Sawunggalih Aji (POLSA)  
> **Tech Stack**: Laravel 12, PHP 8.2+, Blade, Alpine.js, Tailwind CSS (v3), Vite, MySQL / SQLite  
> **Document Version**: 1.0.0  
> **Status**: Active / In Development  

---

## 1. Executive Summary & Vision

**Eduva** adalah platform sistem informasi akademik terpadu berbasis *Outcome-Based Education* (OBE) dan *Learning Management System* (LMS) yang dirancang khusus untuk Politeknik Sawunggalih Aji (POLSA). 

Sistem ini memfasilitasi seluruh siklus pembelajaran tinggi vokasi—mulai dari perencanaan kurikulum (CPL & CPMK), penyusunan RPS (Rencana Pembelajaran Semester), pengajaran harian melalui LMS yang terintegrasi langsung dengan RPS, penilaian berbobot otomatis, hingga pelaporan kinerja akademik eksekutif untuk Manajemen/Direktur.

---

## 2. Core Architectural Philosophy (Anti-AI Slop & Ponytail Standard)

Untuk menjamin kualitas perangkat lunak tetap bersih, ramah pemeliharaan, serta terhindar dari *over-engineering* / *AI Slop*:

1. **Idiomatic Laravel First**: Menggunakan pola bawaan Laravel 12 secara murni (MVC, Eloquent Relationships, Form Requests, Blade Components). Tidak membuat abstraksi berlebihan (seperti Repository Pattern buatan atau DTO tanpa alasan terukur).
2. **Ponytail Methodology (YAGNI)**: Solusi paling sederhana yang terbukti bekerja adalah solusi terbaik. Utamakan pustaka standar & fitur native platform sebelum menambah pustaka eksternal.
3. **Single Source of Truth**: Setiap logika bisnis (seperti keterkaitan Tugas RPS dengan Tugas LMS) tersinkronisasi secara otomatis tanpa redundansi data yang rapuh.
4. **Strict Code Hygiene**: Mengikuti panduan PSR-12 dan format Laravel Pint (`laravel/pint`). Bebas dari komentar kode buatan AI yang bertele-tele.

---

## 3. User Roles & Access Control (RBAC)

Eduva menerapkan kontrol akses terstruktur berbasis peran (*Role-Based Access Control*):

| Role | Deskripsi & Hak Akses Utama |
|---|---|
| **Admin** | Akses penuh ke Master Data & User Management. **Read-Only pada LMS** (Dapat memantau seluruh kelas LMS tanpa hak memasukkan/mengedit nilai, materi, atau forum). |
| **Dosen** | Mengelola RPS mata kuliah yang diampu, mengunggah materi/tugas dari RPS ke LMS, melakukan absensi, memeriksa & memberi nilai kiriman mahasiswa. |
| **Kaprodi** | Menyetujui/merevisi RPS dosen prodi, mengelola KRS & verifikasi persetujuan KHS mahasiswa prodi, memantau kesiapan kurikulum OBE. |
| **Direktur** | Dashboard Eksekutif & Laporan Mutu Kinerja Institusi (Statistik RPS, Keterlaksanaan LMS, Capaian CPL/CPMK, Rekapitulasi Prodi). |
| **Mahasiswa** | Mengakses ruang kelas LMS, mengunduh materi, mengumpulkan tugas (dengan deteksi keterlambatan), mengisi KRS, melihat Rekap Nilai & Cetak KHS PDF. |

---

## 4. Module Specifications

### Module 1: Master Data & Academic Setup
- **Program Studi & Kurikulum**: Manajemen data prodi (D3/D4), kurikulum berlaku, beban SKS.
- **CPL & CPMK**: Pemetaan Capaian Pembelajaran Lulusan (CPL) dan Capaian Pembelajaran Mata Kuliah (CPMK) sesuai standar kriteria akreditasi OBE.
- **Mata Kuliah & Plotting Pengampu**: Pengaturan mata kuliah per semester, plotting dosen pengampu kelas reguler (Kelas A) & karyawan (Kelas B).
- **Tahun Akademik**: Aktivasi tahun akademik & semester berjalan (Ganjil/Genap).

### Module 2: RPS Management System (OBE)
- **Penyusunan RPS**: Form penyusunan RPS berbasis 16 minggu pertemuan (Sub-CPMK, Indikator, Bobot Nilai, Metode Pembelajaran).
- **Penetapan Tugas & Bobot RPS**: Pembuatan rancangan tugas per minggu beserta indikator dan bobot penilaian.
- **Workflow Persetujuan RPS**: Dosen mengajukan RPS $\rightarrow$ Kaprodi meninjau (Setujui / Minta Revisi) $\rightarrow$ Status RPS diperbarui.

### Module 3: RPS-Driven LMS (Ruang Kelas Digital)
- **Integrasi RPS $\rightarrow$ LMS**:
  - Dosen mengunggah materi/tugas dari RPS ke LMS secara langsung.
  - Tugas yang diunggah masuk ke LMS sebagai **Draf** yang perlu dikonfirmasi/ditugaskan oleh Dosen pengampu.
  - Pembaruan pada RPS Tugas otomatis mensinkronkan judul, instruksi, dan deadline di LMS.
- **Pengumpulan & Deteksi Deadline**:
  - Deteksi tenggat waktu presisi (Zona Waktu `Asia/Jakarta`).
  - Mahasiswa yang mengumpulkan melewati deadline tetap diperbolehkan dengan penandaan otomatis **Terlambat**.
  - Catatan dan file jawaban mahasiswa dapat dipratinjau langsung oleh Dosen pada panel penilaian.
- **Forum & Diskusi Kelas**: Diskusi interaktif per kelas dengan batas waktu pengeditan/penghapusan pesan 30 menit.
- **Absensi & Rekap Kehadiran**: Pembukaan sesi absensi pertemuan dan pencatatan otomatis status kehadiran mahasiswa.

### Module 4: KRS & KHS Management
- **Pengisian & Plotting KRS**: Mahasiswa mengambil paket matakuliah, validasi pembimbing akademik/Kaprodi.
- **Verifikasi KHS & Cetak PDF**:
  - Kaprodi melakukan verifikasi kelayakan KHS mahasiswa (pemberian persetujuan secara individu maupun *bulk approval*).
  - Mahasiswa yang disetujui dapat mencetak dokumen KHS berformat PDF resmi. Admin & Kaprodi memiliki akses unduh PDF draf untuk keperluan verifikasi.

### Module 5: OBE Assessment & Executive Dashboard
- **Kalkulasi Nilai Komponen & Huruf**:
  - Perhitungan nilai akhir tertimbang (Tugas LMS + Quiz + UTS + UAS + Praktikum + Project).
  - Konversi otomatis Nilai Angka ke Nilai Huruf (A, B+, B, C+, C, D, E) & Bobot Indeks Prestasi.
- **Perhitungan Ketercapaian CPL / CPMK**: Matrix evaluasi mutu pembelajaran berdasarkan hasil penilaian mahasiswa pada tiap instrumen penilaian OBE.
- **Dashboard Eksekutif Direktur**: Visualisasi real-time tingkat kesiapan RPS prodi, keterlaksanaan 16 pertemuan LMS, dan statistik mahasiswa.

---

## 5. Technical Requirements & Verification Standard

1. **Database Consistency**: Migration terstruktur dengan constraint Foreign Key cascade/restrict yang tepat.
2. **Quality Assurance**:
   - Seluruh pengujian otomatis dijalankan menggunakan PHPUnit (`php artisan test`) pada environment SQLite memory.
   - Target pengujian: Pass 100% tanpa adanya *regression bug*.
3. **Environment & Setup**:
   - Skrip setup terintegrasi via `composer run setup` dan `composer run dev`.

---

## 6. Document Maintenance & Roadmap

Dokumen PRD ini menjadi panduan utama untuk setiap iterasi pengembangan pada repositori **Eduva**. Setiap penambahan fitur baru harus diselaraskan dengan arsitektur dasar dan aturan *Anti-AI Slop* yang tercantum pada dokumen ini.
