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
            'lmsMateris' => function ($q) {
                $q->latest()->limit(5);
            },
            'lmsTugas' => function ($q) {
                $q->withCount(['submissions'])->latest()->limit(5);
            },
            'lmsForumDiskusis' => function ($q) {
                $q->whereNull('parent_id')->with('user')->latest()->limit(5);
            },
            'lmsForumDiskusis.replies.user',
            'lmsPengumumans' => function ($q) {
                $q->latest()->limit(5);
            },
        ]);

        $materiCount = $pengampu->lmsMateris()->count();
        $tugasCount = $pengampu->lmsTugas()->count();

        return view('lms.show', compact('pengampu', 'materiCount', 'tugasCount'));
    }
}
