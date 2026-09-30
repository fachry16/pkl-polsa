<?php

namespace Database\Seeders;

use App\Models\BahanKajian;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProfilLulusan;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\Rps;
use App\Models\RpsBentukEvaluasi;
use App\Models\RpsPenilaian;
use App\Models\RpsPertemuan;
use App\Models\RpsTugas;
use App\Models\SemesterMahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PolsaObeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Program Studi Politeknik Sawunggalih Aji (POLSA)
        $prodiTrpl = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'TRPL'],
            ['nama_prodi' => 'Teknik Rekayasa Perangkat Lunak', 'jenjang' => 'D4', 'akreditasi' => 'Baik']
        );
        $prodiTi = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'TI'],
            ['nama_prodi' => 'Teknik Informatika', 'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali']
        );
        $prodiAb = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'AB'],
            ['nama_prodi' => 'Administrasi Bisnis', 'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali']
        );
        $prodiAk = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'AK'],
            ['nama_prodi' => 'Akuntansi', 'jenjang' => 'D3', 'akreditasi' => 'Baik']
        );
        $prodiBd = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'BD'],
            ['nama_prodi' => 'Bisnis Digital', 'jenjang' => 'D4', 'akreditasi' => 'Baik']
        );

        // 2. Roles
        Role::firstOrCreate(['kode' => 'admin'], ['nama' => 'Administrator', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'dosen'], ['nama' => 'Dosen', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'mahasiswa'], ['nama' => 'Mahasiswa', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'direktur'], ['nama' => 'Direktur', 'is_system' => true]);

        $passwordHash = Hash::make('password');

        // 3. Akun Pengguna
        // Admin
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@polsa.ac.id'],
            ['name' => 'Admin POLSA', 'password' => $passwordHash, 'role' => 'admin']
        );

        // Direktur
        $userDirektur = User::firstOrCreate(
            ['email' => 'direktur@polsa.ac.id'],
            ['name' => 'Dr. Ir. Direktur POLSA, M.T.', 'password' => $passwordHash, 'role' => 'direktur']
        );

        // Kaprodi TRPL
        $userKaprodiTrpl = User::firstOrCreate(
            ['email' => 'kaprodi.trpl@polsa.ac.id'],
            ['name' => 'Dr. Eng. Kaprodi TRPL, M.Kom', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosenKaprodiTrpl = Dosen::firstOrCreate(
            ['user_id' => $userKaprodiTrpl->id],
            [
                'nidn' => '0601018001',
                'program_studi_id' => $prodiTrpl->id,
                'jabatan' => 'Kaprodi',
            ]
        );

        // Kaprodi TI
        $userKaprodiTi = User::firstOrCreate(
            ['email' => 'kaprodi.ti@polsa.ac.id'],
            ['name' => 'M. Kom. Kaprodi TI, M.Kom', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosenKaprodiTi = Dosen::firstOrCreate(
            ['user_id' => $userKaprodiTi->id],
            [
                'nidn' => '0601018002',
                'program_studi_id' => $prodiTi->id,
                'jabatan' => 'Kaprodi',
            ]
        );

        // Dosen 1: Budi Santoso (TRPL)
        $userDosen1 = User::firstOrCreate(
            ['email' => 'budi.santoso@polsa.ac.id'],
            ['name' => 'Budi Santoso, M.Kom', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosen1 = Dosen::firstOrCreate(
            ['user_id' => $userDosen1->id],
            [
                'nidn' => '0612058502',
                'program_studi_id' => $prodiTrpl->id,
                'jabatan' => 'Lektor',
            ]
        );

        // Dosen 2: Siti Rahma (TRPL)
        $userDosen2 = User::firstOrCreate(
            ['email' => 'siti.rahma@polsa.ac.id'],
            ['name' => 'Siti Rahma, M.T.', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosen2 = Dosen::firstOrCreate(
            ['user_id' => $userDosen2->id],
            [
                'nidn' => '0620088803',
                'program_studi_id' => $prodiTrpl->id,
                'jabatan' => 'Asisten Ahli',
            ]
        );

        // Dosen 3: Hendra Wijaya (TI)
        $userDosen3 = User::firstOrCreate(
            ['email' => 'hendra.wijaya@polsa.ac.id'],
            ['name' => 'Hendra Wijaya, M.Eng', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosen3 = Dosen::firstOrCreate(
            ['user_id' => $userDosen3->id],
            [
                'nidn' => '0615039004',
                'program_studi_id' => $prodiTi->id,
                'jabatan' => 'Lektor',
            ]
        );

        // Mahasiswa 1: Ahmad Rizky (TRPL - Sem 3)
        $userMhs1 = User::firstOrCreate(
            ['email' => 'ahmad.rizky@polsa.ac.id'],
            ['name' => 'Ahmad Rizky', 'password' => $passwordHash, 'role' => 'mahasiswa']
        );
        $mhs1 = Mahasiswa::firstOrCreate(
            ['user_id' => $userMhs1->id],
            [
                'nim' => '202401001',
                'nama' => 'Ahmad Rizky',
                'program_studi_id' => $prodiTrpl->id,
                'angkatan' => '2024',
                'jenis_kelas' => 'Reguler',
                'status' => 'Aktif',
            ]
        );

        // Mahasiswa 2: Dewi Lestari (TRPL - Sem 3)
        $userMhs2 = User::firstOrCreate(
            ['email' => 'dewi.lestari@polsa.ac.id'],
            ['name' => 'Dewi Lestari', 'password' => $passwordHash, 'role' => 'mahasiswa']
        );
        $mhs2 = Mahasiswa::firstOrCreate(
            ['user_id' => $userMhs2->id],
            [
                'nim' => '202401002',
                'nama' => 'Dewi Lestari',
                'program_studi_id' => $prodiTrpl->id,
                'angkatan' => '2024',
                'jenis_kelas' => 'Reguler',
                'status' => 'Aktif',
            ]
        );

        // Mahasiswa 3: Budi Pratama (TI - Sem 1)
        $userMhs3 = User::firstOrCreate(
            ['email' => 'budi.pratama@polsa.ac.id'],
            ['name' => 'Budi Pratama', 'password' => $passwordHash, 'role' => 'mahasiswa']
        );
        $mhs3 = Mahasiswa::firstOrCreate(
            ['user_id' => $userMhs3->id],
            [
                'nim' => '202501005',
                'nama' => 'Budi Pratama',
                'program_studi_id' => $prodiTi->id,
                'angkatan' => '2025',
                'jenis_kelas' => 'Reguler',
                'status' => 'Aktif',
            ]
        );

        // Mahasiswa 4: Citra Ananda (TRPL - Sem 5)
        $userMhs4 = User::firstOrCreate(
            ['email' => 'citra.ananda@polsa.ac.id'],
            ['name' => 'Citra Ananda', 'password' => $passwordHash, 'role' => 'mahasiswa']
        );
        $mhs4 = Mahasiswa::firstOrCreate(
            ['user_id' => $userMhs4->id],
            [
                'nim' => '202301010',
                'nama' => 'Citra Ananda',
                'program_studi_id' => $prodiTrpl->id,
                'angkatan' => '2023',
                'jenis_kelas' => 'Karyawan',
                'status' => 'Aktif',
            ]
        );

        // 4. Tahun Akademik
        $taAktif = TahunAkademik::firstOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'ganjil'],
            ['is_active' => true]
        );
        $taLama = TahunAkademik::firstOrCreate(
            ['tahun' => '2024/2025', 'semester' => 'genap'],
            ['is_active' => false]
        );

        // Hubungkan Mahasiswa ke Tahun Akademik
        foreach ([[$mhs1, 3], [$mhs2, 3], [$mhs3, 1], [$mhs4, 5]] as [$mhs, $sem]) {
            SemesterMahasiswa::firstOrCreate([
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taAktif->id,
            ], [
                'semester' => $sem,
                'status' => 'Aktif',
            ]);
        }

        // 5. Kurikulum OBE TRPL & TI 2024
        $kurikulumTrpl = Kurikulum::firstOrCreate(
            ['nama_kurikulum' => 'Kurikulum OBE D4 TRPL 2024'],
            [
                'program_studi_id' => $prodiTrpl->id,
                'tahun_berlaku' => 2024,
                'beban_studi' => 144,
                'deskripsi' => 'Kurikulum berbasis Outcome-Based Education (OBE) untuk program sarjana terapan D4 TRPL Politeknik Sawunggalih Aji.',
                'status' => 'aktif',
            ]
        );

        $kurikulumTi = Kurikulum::firstOrCreate(
            ['nama_kurikulum' => 'Kurikulum OBE D3 TI 2024'],
            [
                'program_studi_id' => $prodiTi->id,
                'tahun_berlaku' => 2024,
                'beban_studi' => 108,
                'deskripsi' => 'Kurikulum berbasis Outcome-Based Education (OBE) untuk program diploma D3 Teknik Informatika Politeknik Sawunggalih Aji.',
                'status' => 'aktif',
            ]
        );

        // Profil Lulusan TRPL
        $pl1 = ProfilLulusan::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_pl' => 'PL-01'],
            [
                'nama_pl' => 'Software Engineer / Fullstack Developer Utama',
                'profesi' => 'Fullstack Developer, Software Engineer, Web Developer',
            ]
        );
        $pl2 = ProfilLulusan::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_pl' => 'PL-02'],
            [
                'nama_pl' => 'System Analyst & Software Architect',
                'profesi' => 'System Analyst, Solution Architect, IT Consultant',
            ]
        );

        // CPL TRPL
        $cpl1 = Cpl::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpl' => 'CPL-01'],
            ['deskripsi' => 'Mampu merancang, mengimplementasikan, dan menguji perangkat lunak berbasis web & mobile sesuai standar industri.']
        );
        $cpl2 = Cpl::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpl' => 'CPL-02'],
            ['deskripsi' => 'Mampu menguasai arsitektur basis data relasional/NoSQL, keamanan informasi, serta optimasi query sistem.']
        );
        $cpl3 = Cpl::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpl' => 'CPL-03'],
            ['deskripsi' => 'Mampu menerapkan metodologi Agile/Scrum dan manajemen proyek rekayasa perangkat lunak secara profesional.']
        );

        // Hubungkan PL ke CPL
        DB::table('profil_lulusan_cpl')->updateOrInsert(
            ['profil_lulusan_id' => $pl1->id, 'cpl_id' => $cpl1->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('profil_lulusan_cpl')->updateOrInsert(
            ['profil_lulusan_id' => $pl1->id, 'cpl_id' => $cpl2->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('profil_lulusan_cpl')->updateOrInsert(
            ['profil_lulusan_id' => $pl2->id, 'cpl_id' => $cpl3->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Bahan Kajian (BK) TRPL
        $bk1 = BahanKajian::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_bk' => 'BK-WEB'],
            ['nama_bk' => 'Rekayasa Perangkat Lunak & Arsitektur Web', 'referensi' => 'Buku Acuan RPL & Standar IEEE']
        );
        $bk2 = BahanKajian::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_bk' => 'BK-DB'],
            ['nama_bk' => 'Sistem Basis Data & Manajerial Informasi', 'referensi' => 'Database System Concepts (Silberschatz)']
        );

        // CPMK TRPL
        $cpmk1 = Cpmk::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpmk' => 'CPMK-01'],
            ['deskripsi' => 'Mahasiswa mampu membangun backend REST API terstruktur menggunakan Laravel 12 & Eloquent ORM.']
        );
        $cpmk2 = Cpmk::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpmk' => 'CPMK-02'],
            ['deskripsi' => 'Mahasiswa mampu membangun antarmuka web reaktif Blade Templates, Alpine.js, dan Tailwind CSS.']
        );
        $cpmk3 = Cpmk::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_cpmk' => 'CPMK-03'],
            ['deskripsi' => 'Mahasiswa mampu merancang dan melakukan optimasi Query & Relationship pada Basis Data Relasional MySQL.']
        );

        // Pemetaan CPL - CPMK
        DB::table('cpl_cpmk_semesters')->updateOrInsert(
            [
                'kurikulum_id' => $kurikulumTrpl->id,
                'cpl_id' => $cpl1->id,
                'cpmk_id' => $cpmk1->id,
                'semester' => 3,
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('cpl_cpmk_semesters')->updateOrInsert(
            [
                'kurikulum_id' => $kurikulumTrpl->id,
                'cpl_id' => $cpl1->id,
                'cpmk_id' => $cpmk2->id,
                'semester' => 3,
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('cpl_cpmk_semesters')->updateOrInsert(
            [
                'kurikulum_id' => $kurikulumTrpl->id,
                'cpl_id' => $cpl2->id,
                'cpmk_id' => $cpmk3->id,
                'semester' => 3,
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // 6. Mata Kuliah
        $mkWebLanjut = MataKuliah::firstOrCreate(
            ['kode' => 'TRPL301'],
            [
                'nama' => 'Pemrograman Web Lanjut',
                'sks_teori' => 1,
                'sks_praktikum' => 2,
                'semester' => 3,
                'jenis' => 'Wajib',
                'kurikulum_id' => $kurikulumTrpl->id,
            ]
        );
        $mkBdLanjut = MataKuliah::firstOrCreate(
            ['kode' => 'TRPL302'],
            [
                'nama' => 'Basis Data Lanjut',
                'sks_teori' => 1,
                'sks_praktikum' => 2,
                'semester' => 3,
                'jenis' => 'Wajib',
                'kurikulum_id' => $kurikulumTrpl->id,
            ]
        );
        $mkAlgo = MataKuliah::firstOrCreate(
            ['kode' => 'TI101'],
            [
                'nama' => 'Algoritma & Pemrograman',
                'sks_teori' => 1,
                'sks_praktikum' => 2,
                'semester' => 1,
                'jenis' => 'Wajib',
                'kurikulum_id' => $kurikulumTi->id,
            ]
        );

        // Relasi CPMK - Mata Kuliah
        DB::table('cpmk_mata_kuliah')->updateOrInsert(
            ['cpmk_id' => $cpmk1->id, 'mata_kuliah_id' => $mkWebLanjut->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('cpmk_mata_kuliah')->updateOrInsert(
            ['cpmk_id' => $cpmk2->id, 'mata_kuliah_id' => $mkWebLanjut->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('cpmk_mata_kuliah')->updateOrInsert(
            ['cpmk_id' => $cpmk3->id, 'mata_kuliah_id' => $mkBdLanjut->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // 7. RPS (Rencana Pembelajaran Semester) Pemrograman Web Lanjut
        $rpsWeb = Rps::firstOrCreate(
            ['mata_kuliah_id' => $mkWebLanjut->id],
            [
                'kode_rps' => 'RPS-TRPL301-2025',
                'semester' => 3,
                'dosen_pengampu' => 'Budi Santoso, M.Kom',
                'deskripsi_mata_kuliah' => 'Mata kuliah ini membahas pengembangan aplikasi web modern fullstack dengan Laravel 12, REST API, Blade Component, Alpine.js, Tailwind CSS, dan integrasi cloud storage.',
                'status' => 'Disetujui',
                'disetujui_oleh' => $userKaprodiTrpl->id,
                'tanggal_disetujui' => now()->subDays(5),
                'catatan_revisi' => 'RPS disetujui. Sesuai dengan standar kurikulum OBE 2024.',
                'rumpun_mk' => 'Rekayasa Perangkat Lunak',
                'mk_prasyarat' => 'Pemrograman Web Dasar',
                'dosen_pengembang_rps' => 'Budi Santoso, M.Kom',
                'koordinator_rmk' => 'Budi Santoso, M.Kom',
                'ketua_prodi' => 'Dr. Eng. Kaprodi TRPL, M.Kom',
            ]
        );

        // RPS Pertemuan (16 Minggu)
        $topikPertemuan = [
            1 => 'Pengenalan Arsitektur Laravel 12 & Konsep MVC Modern',
            2 => 'Database Migration, Seeder & Eloquent Model',
            3 => 'Controller, Resourceful Routing & Request Lifecycle',
            4 => 'Blade Templating Engine, Component & Directive',
            5 => 'Interaktivitas Frontend dengan Alpine.js & Tailwind CSS',
            6 => 'Pengembangan RESTful API & JSON Response Standard',
            7 => 'Otentikasi & Otorisasi Sistem (Breeze & Multi-Role Middleware)',
            8 => 'Ujian Tengah Semester (UTS) - Praktikum Coding REST API',
            9 => 'Manajemen Upload File & Integrasi Storage Backend Cloud',
            10 => 'Keamanan Web: CSRF Protection, XSS Sanitization & Rate Limiting',
            11 => 'Form Request Validation & Custom Validation Rules',
            12 => 'Eloquent Relationship Lanjut: One-to-Many & Many-to-Many',
            13 => 'Pengujian Otomatis (Unit & Feature Testing dengan PHPUnit)',
            14 => 'Optimasi Performa Query & Caching Strategi',
            15 => 'Persiapan Capstone Project & Code Review',
            16 => 'Ujian Akhir Semester (UAS) - Presentasi & Demo Web Fullstack',
        ];

        foreach ($topikPertemuan as $minggu => $topik) {
            RpsPertemuan::firstOrCreate(
                ['rps_id' => $rpsWeb->id, 'minggu' => $minggu],
                [
                    'sub_cpmk' => "Sub-CPMK Minggu {$minggu}",
                    'materi' => $topik,
                    'metode' => 'Kuliah Teori & Praktikum Lab (2 SKS)',
                    'pengalaman_belajar' => 'Diskusi kelompok & Koding Mandiri',
                    'indikator' => "Ketepatan implementasi sintaks dan logika {$topik}",
                    'bobot' => 5.00,
                    'cpmk_induk' => 'CPMK-01',
                    'teknik_kriteria' => 'Rubrik Penilaian Koding',
                    'metode_daring' => 'E-Learning POLSA & Zoom',
                    'metode_luring' => 'Tatap Muka di Lab Komputer 1',
                ]
            );
        }

        // RPS Tugas
        RpsTugas::firstOrCreate(
            ['rps_id' => $rpsWeb->id, 'nama_tugas' => 'Tugas 1: Implementasi REST API & Form Validation'],
            [
                'minggu_topik' => 6,
                'kategori_komponen' => 'Tugas Mandiri',
                'sub_cpmk' => 'Sub-CPMK 1',
                'penugasan' => 'Membuat RESTful API CRUD menggunakan Laravel 12',
                'deskripsi' => 'Buatlah RESTful API CRUD menggunakan Laravel 12 dengan menerapkan Form Request Validation, JSON Resource Response, dan autentikasi Bearer Token.',
                'ruang_lingkup' => 'Backend API Development',
                'cara_pengerjaan' => 'Individu',
                'batas_waktu' => '7 Hari',
                'luaran_tugas' => 'Laporan PDF & Repository GitHub',
                'deadline' => now()->addDays(7),
                'bobot_nilai' => 20.00,
            ]
        );
        RpsTugas::firstOrCreate(
            ['rps_id' => $rpsWeb->id, 'nama_tugas' => 'Tugas 2: Aplikasi Fullstack Web LMS dengan Alpine.js'],
            [
                'minggu_topik' => 12,
                'kategori_komponen' => 'Tugas Kelompok',
                'sub_cpmk' => 'Sub-CPMK 2',
                'penugasan' => 'Kembangkan aplikasi web interaktif fullstack',
                'deskripsi' => 'Kembangkan aplikasi web interaktif fullstack memanfaatkan Blade Component, Alpine.js untuk reactive UI, dan Tailwind CSS.',
                'ruang_lingkup' => 'Fullstack Web Application',
                'cara_pengerjaan' => 'Kelompok 2 Orang',
                'batas_waktu' => '14 Hari',
                'luaran_tugas' => 'Aplikasi Web Active & Source Code',
                'deadline' => now()->addDays(14),
                'bobot_nilai' => 30.00,
            ]
        );

        // RPS Penilaian & Evaluasi
        RpsPenilaian::firstOrCreate(
            ['rps_id' => $rpsWeb->id],
            [
                'tugas' => 20.00,
                'quiz' => 10.00,
                'uts' => 30.00,
                'uas' => 40.00,
                'praktikum' => 0.00,
                'project' => 0.00,
                'absensi' => 0.00,
                'keaktifan' => 0.00,
                'etika' => 0.00,
            ]
        );

        RpsBentukEvaluasi::firstOrCreate(
            ['rps_id' => $rpsWeb->id, 'bentuk_evaluasi' => 'Tugas & Praktikum'],
            ['sub_cpmk' => 'Sub-CPMK 1 & 2', 'instrumen' => 'Rubrik Koding REST API', 'frekuensi' => '2 Kali', 'tagihan' => 'Laporan PDF & Repository GitHub', 'bobot' => 20.00, 'formatif' => true, 'sumatif' => true]
        );
        RpsBentukEvaluasi::firstOrCreate(
            ['rps_id' => $rpsWeb->id, 'bentuk_evaluasi' => 'Ujian Tengah Semester (UTS)'],
            ['sub_cpmk' => 'Sub-CPMK 1', 'instrumen' => 'Ujian Praktikum Lab', 'frekuensi' => '1 Kali', 'tagihan' => 'Source Code Project', 'bobot' => 30.00, 'formatif' => false, 'sumatif' => true]
        );
        RpsBentukEvaluasi::firstOrCreate(
            ['rps_id' => $rpsWeb->id, 'bentuk_evaluasi' => 'Ujian Akhir Semester (UAS)'],
            ['sub_cpmk' => 'Sub-CPMK 2 & 3', 'instrumen' => 'Presentasi & Live Demo App', 'frekuensi' => '1 Kali', 'tagihan' => 'Aplikasi Fullstack Web', 'bobot' => 40.00, 'formatif' => false, 'sumatif' => true]
        );

        // 8. KRS & Pengampu (Kelas Plotting)
        // Kelas TRPL 3A
        $krsWeb = Krs::firstOrCreate(
            [
                'program_studi_id' => $prodiTrpl->id,
                'mata_kuliah_id' => $mkWebLanjut->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => 'TRPL 3A',
            ],
            [
                'dosen_id' => $dosen1->id,
            ]
        );

        $pengampuWeb = Pengampu::firstOrCreate(
            [
                'mata_kuliah_id' => $mkWebLanjut->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => 'TRPL 3A',
            ],
            [
                'krs_id' => $krsWeb->id,
                'dosen_id' => $dosen1->id,
                'semester_akademik' => 'ganjil',
            ]
        );

        // Hubungkan Mahasiswa TRPL ke Kelas TRPL 3A
        $krsWeb->mahasiswas()->syncWithoutDetaching([$mhs1->id, $mhs2->id]);
        $pengampuWeb->mahasiswas()->syncWithoutDetaching([$mhs1->id, $mhs2->id]);

        // Kelas TI 1A
        $krsAlgo = Krs::firstOrCreate(
            [
                'program_studi_id' => $prodiTi->id,
                'mata_kuliah_id' => $mkAlgo->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => 'TI 1A',
            ],
            [
                'dosen_id' => $dosen3->id,
            ]
        );

        $pengampuAlgo = Pengampu::firstOrCreate(
            [
                'mata_kuliah_id' => $mkAlgo->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => 'TI 1A',
            ],
            [
                'krs_id' => $krsAlgo->id,
                'dosen_id' => $dosen3->id,
                'semester_akademik' => 'ganjil',
            ]
        );

        // Hubungkan Mahasiswa TI ke Kelas TI 1A
        $krsAlgo->mahasiswas()->syncWithoutDetaching([$mhs3->id]);
        $pengampuAlgo->mahasiswas()->syncWithoutDetaching([$mhs3->id]);

        // CATATAN: Bagian LMS (Materi, Tugas, Submisi, Absensi, Forum) sengaja dikosongkan
        // agar kelas yang terbentuk dapat diisi secara mandiri/organik.

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeder OBE Politeknik Sawunggalih Aji (POLSA) berhasil dijalankan!');
    }
}
