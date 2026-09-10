<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function mahasiswa(Request $request)
    {
        $query = Mahasiswa::with(['programStudi', 'semesterMahasiswas.tahunAkademik', 'user']);

        if ($programStudiId = $request->program_studi_id) {
            $query->where('program_studi_id', $programStudiId);
        }
        if ($angkatan = $request->angkatan) {
            $query->where('angkatan', $angkatan);
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        if ($jenisKelas = $request->jenis_kelas) {
            $query->where('jenis_kelas', $jenisKelas);
        }
        if ($tahunAkademikId = $request->tahun_akademik_id) {
            $query->whereHas('semesterMahasiswas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            });
        }

        $mahasiswas = $query->orderBy('nim')->paginate(15)->withQueryString();

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $angkatans = Mahasiswa::distinct()->orderBy('angkatan')->pluck('angkatan');
        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('monitoring.mahasiswa', compact('mahasiswas', 'programStudis', 'angkatans', 'tahunAkademiks'));
    }

    public function dosen(Request $request)
    {
        $query = Dosen::with(['user', 'programStudi']);

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }
        if ($request->jabatan) {
            $jabatan = strtolower($request->jabatan);
            $query->where(function ($sub) use ($jabatan) {
                $sub->whereRaw('LOWER(jabatan) = ?', [$jabatan])
                    ->orWhereHas('user', function ($uq) use ($jabatan) {
                        $uq->whereJsonContains('roles', $jabatan);
                    });
            });
        }

        $dosens = $query->orderBy('nidn')->paginate(15)->withQueryString();

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('monitoring.dosen', compact('dosens', 'programStudis'));
    }

    public function kurikulum(Request $request)
    {
        $query = Kurikulum::with(['programStudi']);

        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $kurikulums = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('monitoring.kurikulum', compact('kurikulums', 'programStudis'));
    }
}
