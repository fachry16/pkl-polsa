<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\LmsAbsensi;
use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsSesiAbsensi;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\Rps;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::all();
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();
        $totalDosen = Dosen::count();
        $totalMahasiswa = Mahasiswa::count();

        $pengampus = collect();

        $dosenKelasA = 0;
        $dosenKelasB = 0;
        $dosenTotalMahasiswa = 0;
        $dosenSubmissionsBelumDinilai = 0;
        $dosenTugasNeedGrading = collect();
        $dosenRpsStats = [
            'total_mk' => 0,
            'disetujui' => 0,
            'diajukan' => 0,
            'draft' => 0,
            'persen' => 0,
        ];
        $dosenMkBelumRps = collect();
        $dosenForumTerbaru = collect();

        if (Auth::user()->isDosen() || Auth::user()->isKaprodi()) {
            $dosen = Auth::user()->dosen;

            if ($dosen && $tahunAkademik) {
                $pengampus = $dosen->pengampus()
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah.rps', 'tahunAkademik'])
                    ->withCount(['lmsMateris', 'lmsTugas', 'mahasiswas'])
                    ->get()
                    ->map(function ($pengampu) {
                        $pengampu->submissions_belum_dinilai = LmsSubmission::whereHas('lmsTugas', function ($q) use ($pengampu) {
                            $q->where('pengampu_id', $pengampu->id);
                        })->whereNull('nilai')->count();

                        $pengampu->sesi_absensi_count = LmsSesiAbsensi::where('pengampu_id', $pengampu->id)->count();
                        $pengampu->terakhir_presensi = LmsSesiAbsensi::where('pengampu_id', $pengampu->id)->latest('tanggal_aktual')->first();

                        return $pengampu;
                    });

                $dosenPengampuIds = $pengampus->pluck('id');

                $dosenKelasA = $pengampus->filter(fn ($p) => preg_match('/A|reguler|pagi/i', $p->kelas))->count();
                $dosenKelasB = $pengampus->filter(fn ($p) => preg_match('/B|karyawan|sore|malam/i', $p->kelas))->count();

                $dosenTotalMahasiswa = Mahasiswa::whereHas('pengampus', function ($q) use ($dosenPengampuIds) {
                    $q->whereIn('pengampu_id', $dosenPengampuIds);
                })->distinct()->count();

                $dosenSubmissionsBelumDinilai = $pengampus->sum('submissions_belum_dinilai');

                // Antrean tugas yang butuh segera dinilai
                $dosenTugasNeedGrading = LmsTugas::whereIn('pengampu_id', $dosenPengampuIds)
                    ->whereHas('submissions', fn ($q) => $q->whereNull('nilai'))
                    ->with(['pengampu.mataKuliah'])
                    ->withCount(['submissions as belum_dinilai_count' => fn ($q) => $q->whereNull('nilai')])
                    ->orderByDesc('belum_dinilai_count')
                    ->limit(5)
                    ->get();

                // Status RPS Mata Kuliah yang diampu dosen ini
                $dosenMkIds = $pengampus->pluck('mata_kuliah_id')->unique();
                $totalMkDosen = $dosenMkIds->count();
                $rpsDisetujuiDosen = Rps::whereIn('mata_kuliah_id', $dosenMkIds)->where('status', 'Disetujui')->count();
                $rpsDiajukanDosen = Rps::whereIn('mata_kuliah_id', $dosenMkIds)->where('status', 'Diajukan')->count();
                $rpsDraftDosen = $totalMkDosen - ($rpsDisetujuiDosen + $rpsDiajukanDosen);

                $dosenRpsStats = [
                    'total_mk' => $totalMkDosen,
                    'disetujui' => $rpsDisetujuiDosen,
                    'diajukan' => $rpsDiajukanDosen,
                    'draft' => max(0, $rpsDraftDosen),
                    'persen' => $totalMkDosen > 0 ? round(($rpsDisetujuiDosen / $totalMkDosen) * 100) : 0,
                ];

                // Peringatan MK yang RPS-nya belum disetujui
                $dosenMkBelumRps = MataKuliah::whereIn('id', $dosenMkIds)
                    ->where(function ($q) {
                        $q->whereDoesntHave('rps')
                            ->orWhereHas('rps', fn ($sub) => $sub->where('status', '!=', 'Disetujui'));
                    })
                    ->with('rps')
                    ->get();

                // Forum diskusi terbaru di kelas dosen ini
                $dosenForumTerbaru = LmsForumDiskusi::whereIn('pengampu_id', $dosenPengampuIds)
                    ->with(['user', 'pengampu.mataKuliah'])
                    ->latest()
                    ->limit(5)
                    ->get();
            }
        }

        // Data Kaprodi
        $kaprodiProdi = null;
        $mhsProdiTotal = 0;
        $mhsProdiKelasA = 0;
        $mhsProdiKelasB = 0;
        $dosenProdiTotal = 0;
        $totalKelasPaketProdi = 0;
        $krsProdiKelasA = 0;
        $krsProdiKelasB = 0;
        $rpsDiajukanProdi = collect();
        $kaprodiRpsStats = [
            'total_mk' => 0,
            'disetujui' => 0,
            'diajukan' => 0,
            'draft' => 0,
            'persen' => 0,
        ];
        $rombelKosongProdi = collect();
        $kurikulumProdi = null;

        if (Auth::user()->isKaprodi()) {
            $kaprodiProdi = Auth::user()->dosen?->programStudi;

            if ($kaprodiProdi) {
                $prodiId = $kaprodiProdi->id;
                $mhsProdiTotal = Mahasiswa::where('program_studi_id', $prodiId)->count();
                $mhsProdiKelasA = Mahasiswa::where('program_studi_id', $prodiId)
                    ->whereHas('pengampus', function ($q) use ($tahunAkademik) {
                        if ($tahunAkademik) {
                            $q->where('tahun_akademik_id', $tahunAkademik->id);
                        }
                        $q->where(function ($sub) {
                            $sub->where('kelas', 'like', '%A%')
                                ->orWhere('kelas', 'like', '%reguler%')
                                ->orWhere('kelas', 'like', '%pagi%');
                        });
                    })
                    ->distinct()
                    ->count();

                $mhsProdiKelasB = Mahasiswa::where('program_studi_id', $prodiId)
                    ->whereHas('pengampus', function ($q) use ($tahunAkademik) {
                        if ($tahunAkademik) {
                            $q->where('tahun_akademik_id', $tahunAkademik->id);
                        }
                        $q->where(function ($sub) {
                            $sub->where('kelas', 'like', '%B%')
                                ->orWhere('kelas', 'like', '%karyawan%')
                                ->orWhere('kelas', 'like', '%sore%')
                                ->orWhere('kelas', 'like', '%malam%');
                        });
                    })
                    ->distinct()
                    ->count();

                $dosenProdiTotal = Dosen::where('program_studi_id', $prodiId)->count();

                // KRS Paket Prodi
                $krsProdiQuery = Krs::where('program_studi_id', $prodiId);
                if ($tahunAkademik) {
                    $krsProdiQuery->where('tahun_akademik_id', $tahunAkademik->id);
                }
                $krsProdi = $krsProdiQuery->with(['mataKuliah', 'dosen.user'])->withCount('mahasiswas')->get();

                $totalKelasPaketProdi = $krsProdi->count();
                $krsProdiKelasA = $krsProdi->filter(fn ($k) => preg_match('/A|reguler|pagi/i', $k->kelas))->count();
                $krsProdiKelasB = $krsProdi->filter(fn ($k) => preg_match('/B|karyawan|sore|malam/i', $k->kelas))->count();

                // Rombel Kosong di Prodi (Zero-Student Alert)
                $rombelKosongProdi = $krsProdi->filter(fn ($k) => $k->mahasiswas_count === 0);

                // RPS Diajukan Butuh Review / Approval Kaprodi
                $rpsDiajukanProdi = Rps::whereHas('mataKuliah.kurikulum', fn ($q) => $q->where('program_studi_id', $prodiId))
                    ->where('status', 'Diajukan')
                    ->with(['mataKuliah', 'pengaju'])
                    ->latest()
                    ->get();

                // Kesiapan RPS Seluruh MK di Prodi
                $mkProdiIds = MataKuliah::whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $prodiId))->pluck('id');
                $totalMkProdi = $mkProdiIds->count();
                $rpsDisetujuiProdi = Rps::whereIn('mata_kuliah_id', $mkProdiIds)->where('status', 'Disetujui')->count();
                $rpsDiajukanCount = Rps::whereIn('mata_kuliah_id', $mkProdiIds)->where('status', 'Diajukan')->count();
                $rpsDraftProdi = max(0, $totalMkProdi - ($rpsDisetujuiProdi + $rpsDiajukanCount));

                $kaprodiRpsStats = [
                    'total_mk' => $totalMkProdi,
                    'disetujui' => $rpsDisetujuiProdi,
                    'diajukan' => $rpsDiajukanCount,
                    'draft' => $rpsDraftProdi,
                    'persen' => $totalMkProdi > 0 ? round(($rpsDisetujuiProdi / $totalMkProdi) * 100) : 0,
                ];

                $kurikulumProdi = Kurikulum::where('program_studi_id', $prodiId)->where('status', 'Aktif')->first();
            }
        }

        $statKelas = 0;
        $statTugasAktif = 0;
        $statBelumDikumpul = 0;
        $tugasMendekati = collect();
        $materiBaru = collect();
        $forumTerbaru = collect();

        $statKelasLMS = 0;
        $statMateriLMS = 0;
        $statTugasLMS = 0;
        $statBelumDinilaiLMS = 0;

        // Monitoring Admin Metrics
        $statKelasA = 0;
        $statKelasB = 0;
        $mhsKelasA = 0;
        $mhsKelasB = 0;
        $rpsStats = [
            'disetujui' => 0,
            'diajukan' => 0,
            'draft' => 0,
            'total_mk' => 0,
            'persen' => 0,
        ];
        $kelasKosong = collect();
        $kelasBelumDinilai = collect();
        $mahasiswaTanpaAkun = 0;
        $userRoleStats = [];
        $pertemuanStats = [
            'total_sesi' => 0,
            'target_sesi' => 0,
            'rata_rata' => 0,
            'persen' => 0,
            'persen_kehadiran' => 0,
        ];
        $prodiRecaps = collect();

        if ((Auth::user()->isAdmin() || Auth::user()->isDirektur()) && $tahunAkademik) {
            $pengampuIds = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)->pluck('id');

            $statKelasLMS = $pengampuIds->count();
            $statMateriLMS = LmsMateri::whereIn('pengampu_id', $pengampuIds)->count();
            $statTugasLMS = LmsTugas::whereIn('pengampu_id', $pengampuIds)->count();
            $statBelumDinilaiLMS = LmsSubmission::whereNull('nilai')
                ->whereHas('lmsTugas', function ($q) use ($pengampuIds) {
                    $q->whereIn('pengampu_id', $pengampuIds);
                })
                ->count();

            // Kelas A & B
            $statKelasA = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)
                ->where(function ($q) {
                    $q->where('kelas', 'like', '%A%')
                        ->orWhere('kelas', 'like', '%reguler%')
                        ->orWhere('kelas', 'like', '%pagi%');
                })->count();

            $statKelasB = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)
                ->where(function ($q) {
                    $q->where('kelas', 'like', '%B%')
                        ->orWhere('kelas', 'like', '%karyawan%')
                        ->orWhere('kelas', 'like', '%sore%')
                        ->orWhere('kelas', 'like', '%malam%');
                })->count();

            $mhsKelasA = Mahasiswa::whereHas('pengampus', function ($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id)
                    ->where(function ($sub) {
                        $sub->where('kelas', 'like', '%A%')
                            ->orWhere('kelas', 'like', '%reguler%')
                            ->orWhere('kelas', 'like', '%pagi%');
                    });
            })->distinct()->count();

            $mhsKelasB = Mahasiswa::whereHas('pengampus', function ($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id)
                    ->where(function ($sub) {
                        $sub->where('kelas', 'like', '%B%')
                            ->orWhere('kelas', 'like', '%karyawan%')
                            ->orWhere('kelas', 'like', '%sore%')
                            ->orWhere('kelas', 'like', '%malam%');
                    });
            })->distinct()->count();

            // Kesiapan RPS
            $totalMk = MataKuliah::count();
            $rpsDisetujui = Rps::where('status', 'Disetujui')->count();
            $rpsDiajukan = Rps::where('status', 'Diajukan')->count();
            $rpsDraft = Rps::whereIn('status', ['Draft', 'Revisi'])->count() + MataKuliah::whereDoesntHave('rps')->count();

            $rpsStats = [
                'disetujui' => $rpsDisetujui,
                'diajukan' => $rpsDiajukan,
                'draft' => $rpsDraft,
                'total_mk' => $totalMk,
                'persen' => $totalMk > 0 ? round(($rpsDisetujui / $totalMk) * 100) : 0,
            ];

            // Rombel Kosong (Belum ada mahasiswa di KRS)
            $kelasKosong = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)
                ->whereDoesntHave('mahasiswas')
                ->with(['mataKuliah', 'dosen.user'])
                ->limit(5)
                ->get();

            // Antrean Penilaian Tugas
            $kelasBelumDinilai = Pengampu::whereIn('id', $pengampuIds)
                ->whereHas('lmsSubmissions', function ($q) {
                    $q->whereNull('nilai');
                })
                ->with(['mataKuliah', 'dosen.user'])
                ->withCount(['lmsSubmissions as belum_dinilai_count' => function ($q) {
                    $q->whereNull('nilai');
                }])
                ->orderByDesc('belum_dinilai_count')
                ->limit(5)
                ->get();

            // Mahasiswa Tanpa Akun User Login
            $mahasiswaTanpaAkun = Mahasiswa::whereNull('user_id')->count();

            // Sebaran Role
            $userRoleStats = [
                'admin' => User::where('role', 'admin')->orWhere('roles', 'like', '%"admin"%')->count(),
                'dosen' => User::where('role', 'dosen')->orWhere('roles', 'like', '%"dosen"%')->count(),
                'kaprodi' => User::where('roles', 'like', '%"kaprodi%')->orWhereHas('dosen', fn ($q) => $q->where('jabatan', 'like', '%kaprodi%'))->count(),
                'direktur' => User::where('role', 'direktur')->orWhere('roles', 'like', '%"direktur"%')->count(),
                'mahasiswa' => User::where('role', 'mahasiswa')->orWhere('roles', 'like', '%"mahasiswa"%')->count(),
            ];

            // Progres 16 Pertemuan LMS & Presensi
            $totalSesiDibuka = LmsSesiAbsensi::whereIn('pengampu_id', $pengampuIds)->count();
            $targetSesi = $pengampuIds->count() * 16;
            $totalAbsensi = LmsAbsensi::whereHas('sesi', fn ($q) => $q->whereIn('pengampu_id', $pengampuIds))->count();
            $totalHadir = LmsAbsensi::whereHas('sesi', fn ($q) => $q->whereIn('pengampu_id', $pengampuIds))->where('status', 'hadir')->count();

            $pertemuanStats = [
                'total_sesi' => $totalSesiDibuka,
                'target_sesi' => $targetSesi,
                'rata_rata' => $pengampuIds->count() > 0 ? round($totalSesiDibuka / $pengampuIds->count(), 1) : 0,
                'persen' => $targetSesi > 0 ? min(100, round(($totalSesiDibuka / $targetSesi) * 100)) : 0,
                'persen_kehadiran' => $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100) : 0,
            ];

            // Rekapitulasi per Program Studi POLSA
            $prodiRecaps = $programStudis->map(function ($prodi) use ($tahunAkademik) {
                $mkIds = MataKuliah::whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $prodi->id))->pluck('id');
                $dosenCount = Dosen::where('program_studi_id', $prodi->id)->count();
                $mhsCount = Mahasiswa::where('program_studi_id', $prodi->id)->count();

                $kelasQuery = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)
                    ->whereIn('mata_kuliah_id', $mkIds);

                $kelasA = (clone $kelasQuery)->where(function ($q) {
                    $q->where('kelas', 'like', '%A%')
                        ->orWhere('kelas', 'like', '%reguler%')
                        ->orWhere('kelas', 'like', '%pagi%');
                })->count();

                $kelasB = (clone $kelasQuery)->where(function ($q) {
                    $q->where('kelas', 'like', '%B%')
                        ->orWhere('kelas', 'like', '%karyawan%')
                        ->orWhere('kelas', 'like', '%sore%')
                        ->orWhere('kelas', 'like', '%malam%');
                })->count();

                $totalKelas = (clone $kelasQuery)->count();

                $rpsDisetujui = Rps::where('status', 'Disetujui')->whereIn('mata_kuliah_id', $mkIds)->count();
                $totalMkProdi = $mkIds->count();
                $rpsPersen = $totalMkProdi > 0 ? round(($rpsDisetujui / $totalMkProdi) * 100) : 0;

                return (object) [
                    'id' => $prodi->id,
                    'kode_prodi' => $prodi->kode_prodi,
                    'nama_prodi' => $prodi->nama_prodi,
                    'jenjang' => $prodi->jenjang,
                    'dosen_count' => $dosenCount,
                    'mhs_count' => $mhsCount,
                    'kelas_a' => $kelasA,
                    'kelas_b' => $kelasB,
                    'total_kelas' => $totalKelas,
                    'rps_disetujui' => $rpsDisetujui,
                    'total_mk' => $totalMkProdi,
                    'rps_persen' => $rpsPersen,
                ];
            });
        }

        if (Auth::user()->isDirektur() && $tahunAkademik) {
            $pengampuIds = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)->pluck('id');

            $statKelas = $pengampuIds->count();
        }

        if (Auth::user()->isMahasiswa()) {
            $mahasiswa = Auth::user()->mahasiswa;

            if ($mahasiswa && $tahunAkademik) {
                $kelasSaya = $mahasiswa->pengampus()
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah', 'dosen.user'])
                    ->withCount(['lmsMateris', 'lmsTugas'])
                    ->get();

                $pengampuIds = $kelasSaya->pluck('id');

                $idTugasDikumpul = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)->pluck('lms_tugas_id');

                $tugasMendekati = LmsTugas::whereIn('pengampu_id', $pengampuIds)
                    ->whereNotIn('id', $idTugasDikumpul)
                    ->where('deadline', '>=', now())
                    ->where('deadline', '<=', now()->addDays(7))
                    ->with('pengampu.mataKuliah')
                    ->orderBy('deadline')
                    ->get();

                $materiBaru = LmsMateri::whereIn('pengampu_id', $pengampuIds)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->with('pengampu.mataKuliah')
                    ->latest()
                    ->limit(5)
                    ->get();

                $forumTerbaru = LmsForumDiskusi::whereIn('pengampu_id', $pengampuIds)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->with(['user', 'pengampu.mataKuliah'])
                    ->latest()
                    ->limit(10)
                    ->get();

                $statKelas = $kelasSaya->count();
                $statTugasAktif = LmsTugas::whereIn('pengampu_id', $pengampuIds)
                    ->where('deadline', '>=', now())
                    ->count();
                $statBelumDikumpul = LmsTugas::whereIn('pengampu_id', $pengampuIds)
                    ->whereNotIn('id', $idTugasDikumpul)
                    ->count();
            }
        }

        $data = compact(
            'programStudis',
            'tahunAkademik',
            'totalDosen',
            'totalMahasiswa',
            'pengampus',
            'statKelas',
            'statTugasAktif',
            'statBelumDikumpul',
            'tugasMendekati',
            'materiBaru',
            'forumTerbaru',
            'statKelasLMS',
            'statMateriLMS',
            'statTugasLMS',
            'statBelumDinilaiLMS',
            'statKelasA',
            'statKelasB',
            'mhsKelasA',
            'mhsKelasB',
            'rpsStats',
            'kelasKosong',
            'kelasBelumDinilai',
            'mahasiswaTanpaAkun',
            'userRoleStats',
            'pertemuanStats',
            'prodiRecaps',
            'dosenKelasA',
            'dosenKelasB',
            'dosenTotalMahasiswa',
            'dosenSubmissionsBelumDinilai',
            'dosenTugasNeedGrading',
            'dosenRpsStats',
            'dosenMkBelumRps',
            'dosenForumTerbaru',
            'kaprodiProdi',
            'mhsProdiTotal',
            'mhsProdiKelasA',
            'mhsProdiKelasB',
            'dosenProdiTotal',
            'totalKelasPaketProdi',
            'krsProdiKelasA',
            'krsProdiKelasB',
            'rpsDiajukanProdi',
            'kaprodiRpsStats',
            'rombelKosongProdi',
            'kurikulumProdi'
        );

        if (request()->routeIs('dashboard-direktur')) {
            return view('dashboard-direktur', $data);
        }

        return view('dashboard', $data);
    }
}
