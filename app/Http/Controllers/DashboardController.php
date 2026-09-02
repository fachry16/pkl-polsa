<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
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

        if (Auth::user()->isDosen() || Auth::user()->isKaprodi()) {
            $dosen = Auth::user()->dosen;

            if ($dosen && $tahunAkademik) {
                $pengampus = $dosen->pengampus()
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah'])
                    ->withCount(['lmsMateris', 'lmsTugas'])
                    ->get()
                    ->map(function ($pengampu) {
                        $pengampu->submissions_belum_dinilai = LmsSubmission::whereHas('lmsTugas', function ($q) use ($pengampu) {
                            $q->where('pengampu_id', $pengampu->id);
                        })->whereNull('nilai')->count();

                        return $pengampu;
                    });
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

        if (Auth::user()->isAdmin() && $tahunAkademik) {
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

        return view('dashboard', compact(
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
            'prodiRecaps'
        ));
    }
}
