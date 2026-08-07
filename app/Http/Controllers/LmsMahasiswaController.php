<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\LmsTugas;
use App\Models\LmsSubmission;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsMahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();

        abort_if(! $mahasiswa, 403);

        $pengampus = collect();

        if ($tahunAkademik) {
            $pengampus = $mahasiswa->pengampus()
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->with(['mataKuliah', 'dosen.user'])
                ->withCount(['lmsMateris', 'lmsTugas'])
                ->get();
        }

        return view('lms.mahasiswa.index', compact('pengampus', 'tahunAkademik'));
    }

    public function show(Pengampu $pengampu)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);
        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $pengampu->load([
            'mataKuliah',
            'dosen.user',
            'tahunAkademik',
            'lmsMateris' => function ($q) {
                $q->latest();
            },
            'lmsTugas' => function ($q) {
                $q->withCount('submissions')->latest();
            },
            'lmsForumDiskusis' => function ($q) {
                $q->whereNull('parent_id')->with('user', 'replies.user')->latest();
            },
        ]);

        $submissions = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('lms_tugas_id', $pengampu->lmsTugas->pluck('id'))
            ->get()
            ->keyBy('lms_tugas_id');

        $materiCount = $pengampu->lmsMateris->count();
        $tugasCount = $pengampu->lmsTugas->count();

        return view('lms.mahasiswa.show', compact(
            'pengampu', 'materiCount', 'tugasCount', 'submissions'
        ));
    }

    public function storeSubmission(Request $request, LmsTugas $tugas)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        abort_if(! $mahasiswa, 403);

        $pengampu = $tugas->pengampu;

        abort_if(! $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $request->validate([
            'file_jawaban' => 'nullable|file|max:51200',
            'link_external' => 'nullable|string|url|max:500',
            'catatan_mahasiswa' => 'nullable|string',
        ]);

        $data = [
            'lms_tugas_id' => $tugas->id,
            'mahasiswa_id' => $mahasiswa->id,
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'dikumpulkan_pada' => now(),
        ];

        if ($request->hasFile('file_jawaban')) {
            $data['file_jawaban'] = $request->file('file_jawaban')->store('lms/submissions', 'public');
        }

        if ($request->link_external) {
            $data['link_external'] = $request->link_external;
        }

        LmsSubmission::updateOrCreate(
            ['lms_tugas_id' => $tugas->id, 'mahasiswa_id' => $mahasiswa->id],
            $data
        );

        return back()->with('toast_success', 'Tugas berhasil dikumpulkan.');
    }
}
