<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\TahunAkademik;
use Illuminate\Support\Facades\Auth;

class LmsController extends Controller
{
    public function monitor()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $tahunAkademik = TahunAkademik::where('is_active', true)->first();

        if (! $tahunAkademik) {
            return view('lms.monitor', ['pengampus' => collect(), 'tahunAkademik' => null]);
        }

        $pengampus = Pengampu::query()
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->with(['mataKuliah', 'dosen.user', 'tahunAkademik'])
            ->withCount([
                'lmsMateris',
                'lmsTugas',
                'lmsForumDiskusis',
                'lmsSubmissions as submissions_belum_dinilai' => function ($q) {
                    $q->whereNull('nilai');
                },
            ])
            ->orderBy('id')
            ->paginate(12);

        return view('lms.monitor', compact('pengampus', 'tahunAkademik'));
    }

    public function index()
    {
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();
        $dosen = Auth::user()->dosen;

        $pengampus = collect();

        if ($dosen && $tahunAkademik) {
            $pengampus = $dosen->pengampus()
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->with('mataKuliah')
                ->withCount(['lmsMateris', 'lmsTugas', 'lmsForumDiskusis'])
                ->get();
        }

        return view('lms.index', compact('pengampus', 'tahunAkademik'));
    }

    public function show(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $pengampu->load([
            'mataKuliah',
            'tahunAkademik',
            'dosen.user',
            'mahasiswas.user',
            'lmsMateris.rpsPertemuan',
            'lmsTugas.rpsPertemuan',
            'lmsTugas' => function ($q) {
                $q->withCount(['submissions'])->latest();
            },
            'lmsForumDiskusis' => function ($q) {
                $q->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
            },
            'lmsPengumumans' => function ($q) {
                $q->latest();
            },
        ]);

        $materiCount = $pengampu->lmsMateris->count();
        $tugasCount = $pengampu->lmsTugas->count();
        $mahasiswaCount = $pengampu->mahasiswas->count();
        $pertemuans = $pengampu->rpsPertemuans();

        $sesis = $pengampu->lmsSesiAbsensis()
            ->with('absensis')
            ->get()
            ->keyBy('rps_pertemuan_id');

        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();
        $nilaiByMhs = \App\Models\LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->whereIn('mahasiswa_id', $pengampu->mahasiswas->pluck('id'))
            ->get()
            ->groupBy('mahasiswa_id');
        $bobot = app(\App\Services\PenilaianService::class)->bobotKomponen($pengampu);

        return view('lms.show', compact(
            'pengampu',
            'materiCount',
            'tugasCount',
            'mahasiswaCount',
            'pertemuans',
            'sesis',
            'tugasList',
            'nilaiByMhs',
            'bobot'
        ));
    }
}
