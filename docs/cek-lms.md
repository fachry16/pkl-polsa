# Panduan Manual Cek LMS PIKOBE (dari Awal)

Panduan ini untuk memahami rangkaian tahapan LMS dari nol sampai uji akhir. Ikuti secara berurutan.

---

## Fase 0 — Persiapan Environment (sekali saja)

Jalankan di terminal project:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

- `.env`: salin dari `.env.example`; atur koneksi DB (MySQL Laragon: `DB_DATABASE=pikobe_polsa`).
- Buka `http://127.0.0.1:8000`.

> **Catatan penting**: route `/register` aktif, tapi akun hasil register default `role=dosen`. Akun admin pertama dibuat lewat tinker (Fase 1).

---

## Fase 1 — Buat Akun Admin Pertama

```bash
php artisan tinker
```

```php
App\Models\User::updateOrCreate(
    ['email' => 'admin@polsa.ac.id'],
    ['name' => 'Admin', 'password' => bcrypt('admin123'), 'role' => 'admin', 'email_verified_at' => now()]
);
```

Login: `admin@polsa.ac.id` / `admin123`.

---

## Fase 2 — Setup Master Data (Login sebagai Admin)

Alur produksi: **TA → Dosen → Kurikulum/MK → RPS → Mahasiswa → KRS → Pengampu(otomatis) → Kelas LMS**.

### 2.1 Tahun Akademik
- Sidebar → **Tahun Akademik** → tambah `2025/2026` Ganjil → klik **Aktifkan**.

### 2.2 Dosen
- Sidebar → **Dosen** → tambah dosen (nama, email, NIDN, prodi TRPL, jabatan dosen).
- Password default = NIDN. Akun login terbuat otomatis.

### 2.3 Kurikulum + Mata Kuliah + RPS
- **Program Studi** → pilih TRPL → tambah **Kurikulum** → tambah **Mata Kuliah** mis. `TRPL101 Pemrograman Web`.
- Masuk **RPS** MK tersebut → isi `dosen_pengampu` → tambah **Pertemuan** (min 1–2 minggu, isi materi) → tambah/isi **Penilaian** (bobot harus total 100%, mis. tugas 40, quiz 10, uts 25, uas 25). Status RPS cukup Draft untuk LMS.

### 2.4 Mahasiswa
- Sidebar → **Mahasiswa** → tambah 3–4 mahasiswa (NIM, nama, prodi TRPL, angkatan).
- Akun login otomatis: `{nim}@polsa.ac.id`, password = NIM. Jika belum terbuat, jalankan `php artisan mahasiswa:buat-akun`.

### 2.5 KRS → Kelas LMS (otomatis)
- Sidebar → **KRS** → tambah KRS (prodi TRPL, MK `Pemrograman Web`, dosen, TA aktif, kelas `A`).
- **KRS otomatis membuat Pengampu = 1 kelas LMS.**
- Di halaman detail KRS → **tambah mahasiswa** ke KRS → otomatis sync ke `pengampu_mahasiswa` (kelas LMS).

---

## Fase 3 — Cek Sisi Dosen (`/kelas-saya`)

Logout admin → login dosen (email/NIDN).

1. Sidebar → **Kelas LMS Saya** → daftar kelas TA aktif muncul.
2. Buka kelas → lihat ringkasan MK + kelas.
3. **Materi** → tambah (judul, deskripsi, lampiran file/link, pilih Pertemuan) → file muncul → edit → hapus.
4. **Tugas** → tambah 2 tugas:
   - Tugas 1: deadline **1 jam lagi** (bisa dikumpul).
   - Tugas 2: deadline **kemarin** (untuk cek kunci).
   - Isi bobot & lampiran.
5. **Forum** → buat thread baru (pesan + lampiran) → balas thread (parent_id).
6. **Pengumuman** → tambah → cek muncul.
7. **Absensi** → buka sesi per pertemuan → isi H/S/I/A per mahasiswa → simpan.
8. **Rekap Nilai** (`/kelas/{pengampu}/rekap-nilai`):
   - Isi komponen (tugas/quiz/uts/uas) per mahasiswa → **Hitung Ulang Nilai** → nilai akhir keluar.
   - Kosongkan satu komponen → simpan → baris nilai itu hilang (bukan 0).
9. **Nilai Submission**: buka detail tugas → lihat daftar mahasiswa → beri nilai + catatan.

---

## Fase 4 — Cek Sisi Mahasiswa (`/mahasiswa/kelas-saya`)

Logout dosen → login mahasiswa (email `{nim}@polsa.ac.id`, password NIM).

1. Sidebar → **Kelas LMS Saya** → kelas yang diikuti muncul (grid + info semester).
2. Buka kelas → tab **Beranda** → **Materi** (klik tandai selesai) → **Tugas** (kumpul file + catatan; tugas deadline lampau tampil **Terkunci**) → **Forum** (post + balas + edit) → **Pengumuman**.
3. Tab **Nilai** → lihat komponen + bobot + kontribusi + nilai akhir.
4. Tab **Kehadiran** → lihat sesi + status H/S/I/A + % kehadiran.
5. Perbarui kiriman: upload ulang file → centang **hapus file jawaban** → simpan.

---

## Fase 5 — Cek Keamanan File (negatif)

Gunakan **akun kedua** (login di browser incognito) lalu akses langsung:
- Mahasiswa lain buka `GET /mahasiswa/file/submission/{id}` → **403** (bukan miliknya).
- Dosen lain buka `GET /kelas/file/materi/{id}` → **403**.
- Posting forum dengan `parent_id` dari kelas lain → ditolak (validasi).

---

## Fase 6 — Cek Sisi Admin

- Login admin → **Kelas LMS** (`/kelas/monitor-kelas`) → daftar semua kelas TA aktif + count materi/tugas/diskusi/submission **Belum Dinilai**.

---

## Fase 7 — Verifikasi Otomatis (regresi)

```bash
php artisan config:clear
php artisan test          # target 56 test hijau
php artisan view:cache    # pastikan semua blade terkompilasi
```

---

## Ringkasan Alur

```
Admin setup master (TA, Dosen, Kurikulum, MK, RPS, Mahasiswa)
  └─▶ KRS ──▶ Pengampu (1 kelas LMS) otomatis
        └─▶ attach mahasiswa ──▶ sync pengampu_mahasiswa

Dosen: upload materi → buat tugas → posting forum → pengumuman → absensi → nilai submission → rekap
Mahasiswa: lihat materi → kumpul tugas → forum → pengumuman → tab Nilai → tab Kehadiran
Admin: monitor seluruh kelas + count Belum Dinilai
```