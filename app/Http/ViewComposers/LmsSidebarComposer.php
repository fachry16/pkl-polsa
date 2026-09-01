<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Pengampu;
use App\Models\User;
use App\Models\TahunAkademik;
use App\Models\LmsTugas;
use App\Models\LmsSubmission;

class LmsSidebarComposer
{
    public function compose(View $view)
    {
        $auth = auth();

        if (! $auth->check()) {
            return;
        }

        $user = $auth->user();
        $view->with('auth_user', $user);

        $tahunAkademik = TahunAkademik::where('is_active', true)->first();
        $view->with('tahun_akademik', $tahunAkademik);

        $kelasTerdaftar = collect();
        $pendingSubmissions = 0;

        if ($tahunAkademik) {
            if ($user->isMahasiswa()) {
                $mahasiswa = $user->mahasiswa;
                if ($mahasiswa) {
                    $kelasTerdaftar = $mahasiswa->pengampus()
                        ->where('pengampus.tahun_akademik_id', $tahunAkademik->id)
                        ->with(['mataKuliah', 'dosen.user'])
                        ->get();

                    $idTugasDikumpul = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)
                        ->pluck('lms_tugas_id');

                    $pengampuIds = $kelasTerdaftar->pluck('id');
                    $allTugas = LmsTugas::whereIn('pengampu_id', $pengampuIds)->get();
                    $pendingSubmissions = $allTugas->filter(function ($tugas) use ($idTugasDikumpul) {
                        return !$idTugasDikumpul->contains($tugas->id);
                    })->count();
                }
            } elseif ($user->isDosen() || $user->isKaprodi() || $user->isDirektur() || $user->isAdmin()) {
                $query = Pengampu::query()->where('tahun_akademik_id', $tahunAkademik->id);

                if ($user->isDosen() || $user->isKaprodi() || $user->isDirektur()) {
                    $dosen = $user->dosen;
                    if ($dosen) {
                        $query->where('dosen_id', $dosen->id);
                    }
                }

                $kelasTerdaftar = $query->with(['mataKuliah', 'dosen.user'])
                    ->get();

                if ($user->isAdmin() || $user->isDosen() || $user->isKaprodi()) {
                    $pengampuIds = $kelasTerdaftar->pluck('id');
                    $pendingSubmissions = LmsSubmission::whereHas('lmsTugas', function ($q) use ($pengampuIds) {
                        $q->whereIn('pengampu_id', $pengampuIds);
                    })->whereNull('nilai')->count();
                }
            }
        }

        $view->with('kelas_terdaftar', $kelasTerdaftar);
        $view->with('pending_submissions', $pendingSubmissions);
    }
}
