<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\LmsSubmission;
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

        return view('dashboard', compact(
            'programStudis',
            'tahunAkademik',
            'totalDosen',
            'totalMahasiswa',
            'pengampus'
        ));
    }
}
