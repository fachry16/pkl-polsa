<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function kelas()
    {
        $kelases = Pengampu::with(['mataKuliah', 'dosen.user', 'tahunAkademik'])
            ->withCount([
                'mahasiswas',
                'lmsMateris',
                'lmsTugas',
                'lmsForumDiskusis',
                'lmsSubmissions as submissions_belum_dinilai' => function ($q) {
                    $q->whereNull('nilai');
                },
            ])
            ->orderBy('id')
            ->paginate(12);

        return view('monitoring.kelas', compact('kelases'));
    }

    public function lms()
    {
        $user = auth()->user();
        $dosen = $user->dosen;

        $programStudiId = $dosen?->program_studi_id;

        $query = Pengampu::query()
            ->with(['mataKuliah', 'dosen.user', 'tahunAkademik'])
            ->withCount([
                'mahasiswas',
                'lmsMateris',
                'lmsTugas',
                'lmsForumDiskusis',
                'lmsSubmissions as submissions_belum_dinilai' => function ($q) {
                    $q->whereNull('nilai');
                },
            ]);

        if ($programStudiId) {
            $query->whereHas('mataKuliah.kurikulum', function ($q) use ($programStudiId) {
                $q->where('program_studi_id', $programStudiId);
            });
        }

        $pengampus = $query->orderBy('id')->paginate(12);

        $programStudi = $programStudiId ? ProgramStudi::find($programStudiId) : null;

        return view('monitoring.lms', compact('pengampus', 'programStudi'));
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
