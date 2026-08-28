<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\LmsForumDiskusi;
use App\Models\LmsMateri;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
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
            'statBelumDinilaiLMS'
        ));
    }
}
