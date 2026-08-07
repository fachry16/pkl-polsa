<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\LmsTugas;
use App\Models\LmsSubmission;
use App\Models\Rps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsTugasController extends Controller
{
    public function index(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $pengampu->load('mataKuliah', 'tahunAkademik');
        $tugas = $pengampu->lmsTugas()->withCount('submissions')->latest()->get();

        return view('lms.tugas.index', compact('pengampu', 'tugas'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'nullable|string',
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'file' => 'nullable|file|max:51200',
            'link_external' => 'nullable|string|url|max:500',
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'link_external' => $request->link_external,
        ];

        if ($request->hasFile('file')) {
            $data['file_lampiran'] = $request->file('file')->store('lms/tugas', 'public');
        }

        LmsTugas::create($data);

        return back()->with('toast_success', 'Tugas berhasil ditambahkan.');
    }

    public function show(Pengampu $pengampu, LmsTugas $tugas)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->get();

        $submissions = $tugas->submissions()->with('mahasiswa')->get()->keyBy('mahasiswa_id');

        return view('lms.tugas.show', compact('pengampu', 'tugas', 'mahasiswas', 'submissions'));
    }

    public function nilai(Request $request, LmsSubmission $submission)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $submission->lmsTugas->pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'nilai' => 'nullable|numeric|min:0|max:100',
            'catatan_dosen' => 'nullable|string',
        ]);

        $submission->update([
            'nilai' => $request->nilai,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        return back()->with('toast_success', 'Nilai berhasil disimpan.');
    }

    public function rekap(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->get();
        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();

        return view('lms.tugas.rekap', compact('pengampu', 'mahasiswas', 'tugasList'));
    }

    public function syncToRps(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $mahasiswas = $pengampu->mahasiswas()->get();
        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();

        $rataRataKelas = 0;
        $totalStudentsWithGrades = 0;

        foreach ($mahasiswas as $mahasiswa) {
            $totalNilai = 0;
            $tugasDinilai = 0;

            foreach ($tugasList as $tugas) {
                $submission = $tugas->submissions->where('mahasiswa_id', $mahasiswa->id)->first();
                if ($submission && $submission->nilai !== null) {
                    $totalNilai += $submission->nilai;
                    $tugasDinilai++;
                }
            }

            if ($tugasDinilai > 0) {
                $rataRataKelas += ($totalNilai / $tugasDinilai);
                $totalStudentsWithGrades++;
            }
        }

        $rataRataKelas = $totalStudentsWithGrades > 0
            ? round($rataRataKelas / $totalStudentsWithGrades, 2)
            : 0;

        $rps = Rps::where('mata_kuliah_id', $pengampu->mata_kuliah_id)->first();

        if ($rps) {
            $rps->penilaian()->updateOrCreate(
                ['rps_id' => $rps->id],
                ['tugas' => $rataRataKelas]
            );
        }

        return back()->with('toast_success', "Rata-rata nilai tugas ({$rataRataKelas}) berhasil disinkronkan ke komponen penilaian RPS.");
    }
}
