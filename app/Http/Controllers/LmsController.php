<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\LmsNilaiMahasiswa;
use App\Models\Pengampu;
use App\Models\TahunAkademik;
use App\Services\AssessmentCalculationService;
use App\Services\PenilaianService;
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
        $user = Auth::user();

        $pengampus = collect();

        if ($user->isAdmin()) {
            if ($tahunAkademik) {
                $pengampus = Pengampu::query()
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah', 'dosen.user'])
                    ->withCount(['lmsMateris', 'lmsTugas', 'lmsForumDiskusis'])
                    ->get();
            }
        } else {
            $dosen = $user->dosen;

            if ($dosen && $tahunAkademik) {
                $pengampus = $dosen->pengampus()
                    ->where('tahun_akademik_id', $tahunAkademik->id)
                    ->with(['mataKuliah', 'dosen.user'])
                    ->withCount(['lmsMateris', 'lmsTugas', 'lmsForumDiskusis'])
                    ->get();
            }
        }

        return view('lms.index', compact('pengampus', 'tahunAkademik'));
    }

    public function show(Pengampu $pengampu)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $isAdmin = $user->isAdmin();
        $isOwner = $dosen && $pengampu->dosen_id === $dosen->id;
        $isKaprodiProdi = $user->isKaprodi()
            && $dosen
            && (int) $dosen->program_studi_id === (int) ($pengampu->mataKuliah->kurikulum->program_studi_id ?? null);

        if (! $isAdmin && ! $isOwner && ! $isKaprodiProdi) {
            abort(403);
        }

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
        $nilaiByMhs = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->whereIn('mahasiswa_id', $pengampu->mahasiswas->pluck('id'))
            ->get()
            ->groupBy('mahasiswa_id');
        $bobot = app(PenilaianService::class)->bobotKomponen($pengampu);

        $assessment = Assessment::where('pengampu_id', $pengampu->id)->first();
        $pengampu->setRelation('assessment', $assessment);

        $assessmentSummary = null;
        if ($assessment) {
            $assessment->load('pengampu.mataKuliah');
            $assessmentSummary = app(AssessmentCalculationService::class)->mkSummary(
                $assessment,
                $pengampu->mahasiswas()->orderBy('nim')->get()
            );
        }

        return view('lms.show', compact(
            'pengampu',
            'materiCount',
            'tugasCount',
            'mahasiswaCount',
            'pertemuans',
            'sesis',
            'tugasList',
            'nilaiByMhs',
            'bobot',
            'assessmentSummary'
        ));
    }
}
