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

        // Ambil tahun akademik aktif
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();
        $view->with('tahun_akademik', $tahunAkademik);

        // Tentukan kelas berdasarkan role
        $kelasTerdaftar = collect();
        $pendingSubmissions = 0;

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            if ($mahasiswa && $tahunAkademik) {
                $kelasTerdaftar = $mahasiswa->pengampus()
                    ->where('pengampus.tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah', 'dosen.user'])
                    ->get();
            }
        } elseif ($user->isKaprodi() || $user->isDosen() || $user->isDirektur()) {
            $userDosen = null;
            if ($user->dosen) {
                $userDosen = $user->dosen;
            }

            if ($userDosen && $tahunAkademik) {
                $kelasTerdaftar = $userDosen->pengampus()
                    ->where('pengampus.tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah'])
                    ->get();
            } elseif ($user->role === 'admin') {
                $pengampuIds = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)->pluck('id');
                $kelasTerdaftar = Pengampu::whereIn('id', $pengampuIds)
                    ->with(['mataKuliah', 'dosen.user'])
                    ->get();
            }
        }

        $view->with('kelas_terdaftar', $kelasTerdaftar);

        // Hitung tugas belum dikumpulkan (only for mahasiswa and admin)
        if ($user->isMahasiswa() || $user->isAdmin()) {
            $idTugasDikumpul = collect();

            if ($user->isMahasiswa()) {
                $mahasiswa = $user->mahasiswa;
                if ($mahasiswa && $tahunAkademik) {
                    $idTugasDikumpul = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)
                        ->pluck('lms_tugas_id');
                }
            } elseif ($user->isAdmin() && $tahunAkademik) {
                $pengampuIds = Pengampu::where('tahun_akademik_id', $tahunAkademik->id)->pluck('id');
                $idTugasDikumpul = LmsSubmission::whereHas('lmsTugas', function ($q) use ($pengampuIds) {
                    $q->whereIn('pengampu_id', $pengampuIds);
                })->whereNull('nilai')->pluck('lms_tugas_id');
            }

            if ($kelasTerdaftar->count() > 0) {
                $pengampuIds = $kelasTerdaftar->pluck('id');
                $allTugas = LmsTugas::whereIn('pengampu_id', $pengampuIds)->get();
                $pendingSubmissions = $allTugas->filter(function ($tugas) use ($idTugasDikumpul) {
                    return !$idTugasDikumpul->contains($tugas->id);
                })->count();
            }
        }

        $view->with('pending_submissions', $pendingSubmissions);
    }
}
