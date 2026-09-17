<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\BahanKajian;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\LmsAbsensi;
use App\Models\LmsForumDiskusi;
use App\Models\LmsInstrumenCpmk;
use App\Models\LmsMateri;
use App\Models\LmsMateriMahasiswa;
use App\Models\LmsPengumuman;
use App\Models\LmsSesiAbsensi;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MahasiswaTahunAkademik;
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
use App\Models\RumusanNilaiAkhirCpl;
use App\Models\RumusanNilaiAkhirMk;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoPresentationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Program Studi POLSA
        $prodiTrpl = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'TRPL'],
            ['nama_prodi' => 'Teknik Rekayasa Perangkat Lunak', 'jenjang' => 'D4', 'akreditasi' => 'Unggul']
        );
        $prodiTi = ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'TI'],
            ['nama_prodi' => 'Teknik Informatika', 'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali']
        );

        // 2. Roles
        Role::firstOrCreate(['kode' => 'admin'], ['nama' => 'Administrator', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'dosen'], ['nama' => 'Dosen', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'mahasiswa'], ['nama' => 'Mahasiswa', 'is_system' => true]);
        Role::firstOrCreate(['kode' => 'direktur'], ['nama' => 'Direktur', 'is_system' => true]);

        // 3. User Accounts (Password: 'password')
        $passwordHash = Hash::make('password');

        // Admin User
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@polsa.ac.id'],
            ['name' => 'Admin POLSA', 'password' => $passwordHash, 'role' => 'admin']
        );

        // Direktur User
        $userDirektur = User::firstOrCreate(
            ['email' => 'direktur@polsa.ac.id'],
            ['name' => 'Dr. Ir. Direktur POLSA, M.T.', 'password' => $passwordHash, 'role' => 'direktur']
        );

        // Kaprodi User (Dosen + Role Kaprodi)
        $userKaprodi = User::firstOrCreate(
            ['email' => 'kaprodi.trpl@polsa.ac.id'],
            ['name' => 'Dr. Eng. Kaprodi TRPL, M.Kom', 'password' => $passwordHash, 'role' => 'dosen']
        );
        $dosenKaprodi = Dosen::firstOrCreate(
            ['user_id' => $userKaprodi->id],
            [
                'nidn' => '0601018001',
                'program_studi_id' => $prodiTrpl->id,
                'jabatan' => 'Kaprodi',
            ]
        );

        // Dosen 1: Budi Santoso, M.Kom
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

        // Dosen 2: Siti Rahma, M.T.
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

        // Dosen 3: Hendra Wijaya, M.Eng
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

        // Mahasiswa 1: Ahmad Rizky (Sem 3 - TRPL)
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

        // Mahasiswa 2: Dewi Lestari (Sem 3 - TRPL)
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

        // Mahasiswa 3: Budi Pratama (Sem 1 - TI)
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

        // Mahasiswa 4: Citra Ananda (Sem 5 - TRPL)
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

        // Link Mahasiswa ke Tahun Akademik
        foreach ([[$mhs1, 3], [$mhs2, 3], [$mhs3, 1], [$mhs4, 5]] as [$mhs, $sem]) {
            MahasiswaTahunAkademik::firstOrCreate([
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taAktif->id,
            ], [
                'semester' => $sem,
                'status' => 'Aktif',
            ]);
        }

        // 5. Kurikulum OBE TRPL 2024
        $kurikulumTrpl = Kurikulum::firstOrCreate(
            ['nama_kurikulum' => 'Kurikulum OBE D4 TRPL 2024'],
            [
                'program_studi_id' => $prodiTrpl->id,
                'tahun_berlaku' => 2024,
                'beban_studi' => 144,
                'deskripsi' => 'Kurikulum berbasis Outcome-Based Education (OBE) untuk D4 TRPL.',
                'status' => 'aktif',
            ]
        );

        // Profil Lulusan (PL)
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

        // CPL (Capaian Pembelajaran Lulusan)
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

        // Bahan Kajian (BK)
        $bk1 = BahanKajian::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_bk' => 'BK-WEB'],
            ['nama_bk' => 'Rekayasa Perangkat Lunak & Arsitektur Web', 'referensi' => 'Buku Acuan RPL & Standar IEEE']
        );
        $bk2 = BahanKajian::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'kode_bk' => 'BK-DB'],
            ['nama_bk' => 'Sistem Basis Data & Manajerial Informasi', 'referensi' => 'Database System Concepts (Silberschatz)']
        );

        // CPMK
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

        // 6. Mata Kuliah Lintas Semester
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
                'kurikulum_id' => $kurikulumTrpl->id,
            ]
        );
        $mkMobile = MataKuliah::firstOrCreate(
            ['kode' => 'TRPL401'],
            [
                'nama' => 'Pemrograman Mobile Modern',
                'sks_teori' => 1,
                'sks_praktikum' => 2,
                'semester' => 4,
                'jenis' => 'Wajib',
                'kurikulum_id' => $kurikulumTrpl->id,
            ]
        );
        $mkManpro = MataKuliah::firstOrCreate(
            ['kode' => 'TRPL601'],
            [
                'nama' => 'Manajemen Proyek Perangkat Lunak',
                'sks_teori' => 2,
                'sks_praktikum' => 1,
                'semester' => 6,
                'jenis' => 'Wajib',
                'kurikulum_id' => $kurikulumTrpl->id,
            ]
        );

        // 7. RPS (Rencana Pembelajaran Semester) Pemrograman Web Lanjut (APPROVED)
        $rpsWeb = Rps::firstOrCreate(
            ['mata_kuliah_id' => $mkWebLanjut->id],
            [
                'kode_rps' => 'RPS-TRPL301-2025',
                'semester' => 3,
                'dosen_pengampu' => 'Budi Santoso, M.Kom',
                'deskripsi_mata_kuliah' => 'Mata kuliah ini membahas pengembangan aplikasi web modern fullstack dengan Laravel 12, REST API, Blade Component, Alpine.js, Tailwind CSS, dan integrasi cloud storage.',
                'status' => 'Disetujui',
                'disetujui_oleh' => $userKaprodi->id,
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
            9 => 'Manajemen Upload File & Integrasi Storage Backend Cloud (Google Drive)',
            10 => 'Keamanan Web: CSRF Protection, XSS Sanitization & Rate Limiting',
            11 => 'Form Request Validation & Custom Validation Rules',
            12 => 'Eloquent Relationship Lanjut: One-to-Many & Many-to-Many',
            13 => 'Pengujian Otomatis (Unit & Feature Testing dengan PHPUnit)',
            14 => 'Optimasi Performa Query & Caching Strategi',
            15 => 'Persiapan Capstone Project & Code Review',
            16 => 'Ujian Akhir Semester (UAS) - Presentasi & Demo Web Fullstack',
        ];

        $rpsPertemuanObjects = [];
        foreach ($topikPertemuan as $minggu => $topik) {
            $rpsPertemuanObjects[$minggu] = RpsPertemuan::firstOrCreate(
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
                    'metode_daring' => 'E-Learning POLSA LMS & Zoom',
                    'metode_luring' => 'Tatap Muka di Lab Komputer 1',
                ]
            );
        }

        // RPS Tugas
        $rpsTugas1 = RpsTugas::firstOrCreate(
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
        $rpsTugas2 = RpsTugas::firstOrCreate(
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

        // 8. KRS & Pengampu LMS
        $krsWeb = Krs::firstOrCreate(
            [
                'program_studi_id' => $prodiTrpl->id,
                'mata_kuliah_id' => $mkWebLanjut->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => '3A',
            ],
            [
                'dosen_id' => $dosen1->id,
            ]
        );

        $pengampuWeb = Pengampu::firstOrCreate(
            [
                'mata_kuliah_id' => $mkWebLanjut->id,
                'tahun_akademik_id' => $taAktif->id,
                'kelas' => '3A',
            ],
            [
                'krs_id' => $krsWeb->id,
                'dosen_id' => $dosen1->id,
                'semester_akademik' => 'ganjil',
            ]
        );

        // Hubungkan Mahasiswa ke KRS & Pengampu
        $krsWeb->mahasiswas()->syncWithoutDetaching([$mhs1->id, $mhs2->id]);
        $pengampuWeb->mahasiswas()->syncWithoutDetaching([$mhs1->id, $mhs2->id]);

        // 9. LMS Content Live Data

        // LMS Pengumuman
        LmsPengumuman::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'judul' => 'Selamat Datang di Perkuliahan Pemrograman Web Lanjut TA 2025/2026 Ganjil'],
            [
                'isi' => 'Selamat datang rekan-rekan mahasiswa kelas TRPL 3A. Silakan mengunduh modul perkuliahan dan RPS yang telah disediakan pada menu LMS ini. Pastikan Anda aktif mengikuti forum diskusi dan memenuhi kriteria penugasan.',
            ]
        );

        // LMS Materi
        $materi1 = LmsMateri::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'judul' => 'Modul 1: Pengenalan Arsitektur Laravel 12 & Konsep MVC'],
            [
                'rps_pertemuan_id' => $rpsPertemuanObjects[1]->id,
                'deskripsi' => 'Materi pengenalan dasar struktur folder Laravel 12, konfigurasi environment .env, routing dasar, dan arsitektur Model-View-Controller.',
                'file_path' => 'lms/materi/modul1_laravel12_mvc.pdf',
            ]
        );

        $materi2 = LmsMateri::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'judul' => 'Modul 2: Database Migration, Seeder & Eloquent ORM'],
            [
                'rps_pertemuan_id' => $rpsPertemuanObjects[2]->id,
                'deskripsi' => 'Panduan praktikum skema database migration, seeder data demo, dan manipulasi query database menggunakan Eloquent ORM.',
                'file_path' => 'lms/materi/modul2_migration_eloquent.pdf',
            ]
        );

        // Progress Dibaca Materi
        LmsMateriMahasiswa::firstOrCreate(
            ['materi_id' => $materi1->id, 'mahasiswa_id' => $mhs1->id],
            ['dibaca_pada' => now()->subDays(3)]
        );
        LmsMateriMahasiswa::firstOrCreate(
            ['materi_id' => $materi1->id, 'mahasiswa_id' => $mhs2->id],
            ['dibaca_pada' => now()->subDays(2)]
        );

        // LMS Tugas
        $lmsTugas1 = LmsTugas::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'judul' => 'Tugas 1: Implementasi REST API & Validation'],
            [
                'rps_pertemuan_id' => $rpsPertemuanObjects[6]->id,
                'rps_tugas_id' => $rpsTugas1->id,
                'instruksi' => 'Silakan kerjakan Tugas REST API sesuai petunjuk pada RPS Tugas 1. Buatlah controller Resource, tentukan Form Request validation, dan unggah berkas laporan serta link repositori GitHub.',
                'deadline' => now()->addDays(5),
                'bobot_nilai' => 20,
                'batas_upload_mb' => 10,
                'is_active' => true,
            ]
        );

        // LMS Submisi Mahasiswa
        // Submisi 1: Ahmad Rizky (Sudah Dinilai: 90)
        LmsSubmission::firstOrCreate(
            ['lms_tugas_id' => $lmsTugas1->id, 'mahasiswa_id' => $mhs1->id],
            [
                'file_jawaban' => 'lms/tugas/jawaban_ahmad_rizky_rest_api.pdf',
                'link_jawaban' => 'https://github.com/ahmadrizky/polsa-web-api',
                'catatan_mahasiswa' => 'Tugas 1 REST API telah selesai dikerjakan sesuai petunjuk modul.',
                'nilai' => 90.00,
                'catatan_dosen' => 'Sangat rapi! Implementasi JSON Resource & Validasi Form Request sesuai standar.',
                'dikumpulkan_pada' => now()->subDays(1),
            ]
        );

        // Submisi 2: Dewi Lestari (Belum Dinilai / Draf Kiriman)
        LmsSubmission::firstOrCreate(
            ['lms_tugas_id' => $lmsTugas1->id, 'mahasiswa_id' => $mhs2->id],
            [
                'file_jawaban' => 'lms/tugas/jawaban_dewi_lestari_rest_api.pdf',
                'link_jawaban' => 'https://github.com/dewilestari/lms-rest-api',
                'catatan_mahasiswa' => 'Berikut berkas tugas dan dokumentasi Postman.',
                'nilai' => null,
                'catatan_dosen' => null,
                'dikumpulkan_pada' => now()->subHours(5),
            ]
        );

        // LMS Forum Diskusi
        $forum1 = LmsForumDiskusi::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'user_id' => $userDosen1->id, 'parent_id' => null],
            [
                'pesan' => 'Diskusi Modul 2: Optimization & Eager Loading Eloquent. Silakan berikan tanggapan atau pertanyaan seputar penggunaan relationship `with()`!',
            ]
        );

        LmsForumDiskusi::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'user_id' => $userMhs1->id, 'parent_id' => $forum1->id],
            [
                'pesan' => 'Pak Budi, mau bertanya apakah metode Eager Loading with() sebaiknya digunakan untuk relasi bersarang 3 tingkat?',
            ]
        );

        LmsForumDiskusi::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'user_id' => $userDosen1->id, 'parent_id' => $forum1->id],
            [
                'pesan' => 'Pertanyaan bagus Ahmad! Ya, gunakan `with()` dengan memilih kolom tertentu untuk menghemat memori RAM server.',
            ]
        );

        // LMS Sesi Absensi & Kehadiran
        $sesi1 = LmsSesiAbsensi::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'rps_pertemuan_id' => $rpsPertemuanObjects[1]->id],
            [
                'tanggal_aktual' => now()->subDays(7)->toDateString(),
                'waktu_buka' => now()->subDays(7),
            ]
        );
        LmsAbsensi::firstOrCreate(['sesi_id' => $sesi1->id, 'mahasiswa_id' => $mhs1->id], ['status' => 'hadir']);
        LmsAbsensi::firstOrCreate(['sesi_id' => $sesi1->id, 'mahasiswa_id' => $mhs2->id], ['status' => 'hadir']);

        $sesi2 = LmsSesiAbsensi::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'rps_pertemuan_id' => $rpsPertemuanObjects[2]->id],
            [
                'tanggal_aktual' => now()->toDateString(),
                'waktu_buka' => now(),
            ]
        );
        LmsAbsensi::firstOrCreate(['sesi_id' => $sesi2->id, 'mahasiswa_id' => $mhs1->id], ['status' => 'hadir']);
        LmsAbsensi::firstOrCreate(['sesi_id' => $sesi2->id, 'mahasiswa_id' => $mhs2->id], ['status' => 'izin']);

        // 10. Penilaian OBE & Instrumen CPMK
        LmsInstrumenCpmk::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'cpmk_id' => $cpmk1->id],
            [
                'komponen' => 'Penilaian Tugas 1 (REST API & Validation)',
                'bobot_kontribusi' => 50.00,
            ]
        );
        LmsInstrumenCpmk::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id, 'cpmk_id' => $cpmk2->id],
            [
                'komponen' => 'Penilaian Tugas 2 (Frontend Blade & Alpine)',
                'bobot_kontribusi' => 50.00,
            ]
        );

        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampuWeb->id],
            [
                'status' => Assessment::STATUS_DINILAI,
                'created_by' => $userDosen1->id,
            ]
        );

        AssessmentScore::firstOrCreate(
            ['assessment_id' => $assessment->id, 'mahasiswa_id' => $mhs1->id, 'cpmk_id' => $cpmk1->id],
            [
                'nilai' => 90.00,
            ]
        );

        AssessmentScore::firstOrCreate(
            ['assessment_id' => $assessment->id, 'mahasiswa_id' => $mhs2->id, 'cpmk_id' => $cpmk1->id],
            [
                'nilai' => 85.00,
            ]
        );

        RumusanNilaiAkhirMk::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'cpl_id' => $cpl1->id, 'mata_kuliah_id' => $mkWebLanjut->id, 'cpmk_id' => $cpmk1->id],
            [
                'skor_maks' => 100.00,
                'total' => 90.00,
            ]
        );

        RumusanNilaiAkhirCpl::firstOrCreate(
            ['kurikulum_id' => $kurikulumTrpl->id, 'cpl_id' => $cpl1->id, 'mata_kuliah_id' => $mkWebLanjut->id, 'cpmk_id' => $cpmk1->id],
            [
                'skor_maks' => 100.00,
                'total' => 90.00,
            ]
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeder Data Demo Presentasi POLSA LMS Berhasil Dijalankan 100%!');
    }
}
