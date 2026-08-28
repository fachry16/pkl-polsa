# Rencana Perbaikan & Pengembangan LMS — PIKOBE

> Dokumen ini adalah catatan rencana (plan) yang telah disepakati. Dipakai sebagai referensi saat
> implementasi dan ketika meninjau ulang kendala. Diperbarui: 2026-08-11.

## Status ringkas saat ini
- Kelas A/B KRS sudah dipisah benar (berdasarkan digit ke-5 NIM: `3224-1xxx` → A, `3224-2xxx` → B)
  lewat command `php artisan krs:split-classes` (`app/Console/Commands/SplitKrsClasses.php`).
- Formula nilai akhir **saat ini** = bobot dari `rps_penilaians`
  (tugas/quiz/uts/uas/praktikum/project), normalisasi ke bobot terisi
  (`app/Services/PenilaianService.php`).
- **Menunggu konfirmasi dosen**: formula nilai akhir diminta "mengikuti CPMK, bukan RPS".
  Detail masih ambigu (bobot per CPMK? tampilkan ketercapaian per CPMK?). CPMK saat ini belum
  punya bobot, belum terhubung ke MK/pertemuan/tugas.

## Keputusan arsitektur (disepakati)
- **LMS** = penghasil nilai mentah per instrumen (tugas + komponen UTS/UAS/quiz/praktikum/project).
  Berhenti di situ — tidak menghitung apa pun di atasnya.
- **PIKOBE** = menghitung nilai akhir berbobot RPS + agregasi CPMK lewat `PenilaianService`;
  hasil disimpan kembali ke PIKOBE (untuk laporan CPL, dll.).
- **Pemetaan instrumen→CPMK** disimpan di tabel `lms_*` baru (opsi a) — **tidak** menyentuh
  skema inti PIKOBE. Data `cpmks`/`cpl_cpmk_semesters` PIKOBE hanya **dibaca** oleh LMS.
- **Tanpa fitur ekspor nilai** (hasil penilaian akhir dikembalikan ke PIKOBE, bukan diekspor).

## A. Penilaian (prioritas)
### A1. Input nilai semua komponen (UTS/UAS/Quiz/Praktikum/Project)
- Saat ini seluruh UI LMS hanya bisa menilai **tugas**. Komponen UTS/UAS/quiz/praktikum/project
  ada di `lms_nilai_mahasiswas` & `rps_penilaians`, tapi tak ada form/route input.
- Akibat: nilai akhir = nilai tugas saja. Data dev: hanya komponen `tugas` & `akhir` yang terisi.
- Rencana: tambah form input nilai per komponen per mahasiswa (di halaman rekap dan/atau per
  mahasiswa), sehingga `hitungNilaiAkhir` menghitung semua komponen sesuai bobot `rps_penilaians`.

### A2. Formula/lapisan nilai akhir mengikuti CPMK
- Tujuan: nilai akhir dibuat/dihitung menganut CPMK
  (hierarki: Kurikulum → CPL → CPMK → Sub-CPMK → RPS; CPMK > RPS).
- Mengacu myITS Academics: nilai akhir tetap berbasis komponen terbobot (standar universal),
  dan **CPMK adalah lapisan tambahan yang dihitung otomatis** (mode read-only), bukan input terpisah.
- Rencana: tabel baru `lms_cpmk_mata_kuliah` (pemetaan CPMK↔MK dari data PIKOBE),
  `lms_instrumen_cpmk` (instrumen/komponen↔CPMK + bobot kontribusi). Laporan ketercapaian
  per CPMK per mahasiswa (read-only, mirip mode CPMK myITS).
- Prasyarat data PIKOBE: CPMK belum punya bobot, pivot `cpmk_mata_kuliah` tak punya migration,
  `rps_pertemuans` tak punya `cpmk_id` — namun karena hanya dibaca, tidak perlu diubah.

### A3. Tampilkan bobot komponen di Rekap Nilai
- Tambah kolom/petunjuk bobot (dari `rps_penilaians`) pada `resources/views/lms/tugas/rekap.blade.php`
  agar dosen tahu komposisi nilai akhir.

### A4. Pengembalian nilai akhir ke PIKOBE
- Hasil penilaian akhir dikembalikan ke PIKOBE. Tanpa fitur ekspor nilai (CSV/Excel).
- Fokus: sinkronisasi nilai akhir dari LMS ke data PIKOBE — detail mekanisme menyusul.

## B. Bug & inkonsistensi
### B1. Mahasiswa bisa menimpa tugas yang sudah dinilai
- `storeSubmission` (`app/Http/Controllers/LmsMahasiswaController.php:127`) tak cek
  `nilai !== null`; `updateSubmission` (`:183`) mengecek. Tambahkan guard yang sama di `storeSubmission`.

### B2. Aturan deadline tidak konsisten
- `storeSubmission` (kumpul pertama) tidak cek deadline; `updateSubmission` menolak setelah deadline.
  Samakan kebijakan (misal: kumpul telat diizinkan tetapi ditandai `isTerlambat()`, atau keduanya menolak).

### B3. Tugas LMS tidak bisa diedit/dihapus
- `lms_tugas` hanya punya index/store/show + nilai; materi & forum punya edit/update/destroy.
  Tambahkan route + form edit/delete tugas (judul, instruksi, deadline, bobot, batas_upload, file).

### B4. Dead code di Rekap
- `resources/views/lms/tugas/rekap.blade.php` (baris 37–38, 50–53) menghitung `$totalNilai`/
  `$tugasDinilai` tapi tidak ditampilkan — hapus atau gunakan.

## C. Fitur standar LMS (opsional, menyusul)
- Pagination untuk daftar materi/tugas/forum/submission.
- Penandaan "selesai dibaca/dikerjakan" materi (progress mahasiswa).
- Fitur pengumuman/announcement terpisah dari forum.
- Notifikasi ke mahasiswa saat nilai sudah keluar.
- Validasi MIME file di sisi server (whitelist), bukan hanya `accept` di HTML.
- Isi kolom `rps_pertemuan_id` di `lms_materis`/`lms_tugas` saat buat materi/tugas
  (saat ini menganggur).

## D. Catatan teknis
- Test suite butuh SQLite `:memory:`; PHP lokal belum mengaktifkan `pdo_sqlite`/`sqlite3`.
  Jalankan via `php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit`
  (php.ini sistem tidak bisa diedit karena UAC).
- 1 kegagalan test pre-existing: `AuthenticationTest::test_users_can_logout`
  (redirect `/` vs `/login`) — tidak terkait LMS.
- Referensi LMS kampus lain: myITS Academics (dua mode: komponen & CPMK read-only),
  UB, UNESA, LMS Primakara, Polman Bandung, Moodle SiberMu.
