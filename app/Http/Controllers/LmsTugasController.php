<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\LmsInstrumenCpmk;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSubmission;
use App\Models\LmsTopikKomentar;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use App\Models\RpsPertemuan;
use App\Notifications\NilaiDiberikan;
use App\Notifications\TugasBaru;
use App\Rules\LmsFileMime;
use App\Services\AssessmentCalculationService;
use App\Services\PenilaianService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsTugasController extends Controller
{
    private function authorizeRead(Pengampu $pengampu): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        $dosen = $user->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    private function authorizeWrite(Pengampu $pengampu): void
    {
        $user = Auth::user();
        abort_if($user->isAdmin(), 403, 'Admin hanya memiliki akses melihat (read-only) pada kelas LMS.');

        $dosen = $user->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    public function index(Pengampu $pengampu)
    {
        $this->authorizeRead($pengampu);

        $pengampu->load(['mataKuliah.rps.tugas', 'mataKuliah.rps.pertemuans', 'tahunAkademik']);
        $tugas = $pengampu->lmsTugas()->withCount('submissions')->latest()->paginate(10);
        $pertemuans = $pengampu->rpsPertemuans();
        $rpsTugasList = $pengampu->mataKuliah?->rps?->tugas ?? collect();

        return view('lms.tugas.index', compact('pengampu', 'tugas', 'pertemuans', 'rpsTugasList'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'required|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'batas_upload_mb' => $request->batas_upload_mb,
        ];

        if ($request->hasFile('file')) {
            $data['file_lampiran'] = $request->file('file')->store('lms/tugas', 'public');
        }

        $tugas = LmsTugas::create($data);

        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new TugasBaru($pengampu, $tugas));
            }
        }

        return back()->with('toast_success', 'Tugas berhasil ditambahkan.');
    }

    public function tugaskan(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        $tugas->update(['is_active' => true]);

        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new TugasBaru($pengampu, $tugas));
            }
        }

        return back()->with('toast_success', 'Tugas berhasil ditugaskan ke mahasiswa.');
    }

    public function show(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeRead($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        Auth::user()->unreadNotifications()
            ->where('data->pengampu_id', $pengampu->id)
            ->where('data->url', route('lms.tugas.show', [$pengampu->id, $tugas->id]))
            ->update(['read_at' => now()]);

        $pengampu->load('mataKuliah', 'tahunAkademik', 'dosen.user');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->paginate(20);
        $submissions = $tugas->submissions()->with('mahasiswa')->get()->keyBy('mahasiswa_id');

        $komentarsKelas = LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', false)
            ->with('user')
            ->oldest()
            ->get();

        $komentarsPribadi = LmsTopikKomentar::where('tipe_topik', 'tugas')
            ->where('topik_id', $tugas->id)
            ->where('is_private', true)
            ->with(['user', 'mahasiswa.user'])
            ->oldest()
            ->get()
            ->groupBy('mahasiswa_id');

        return view('lms.tugas.show', compact('pengampu', 'tugas', 'mahasiswas', 'submissions', 'komentarsKelas', 'komentarsPribadi'));
    }

    public function edit(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return redirect()->route('lms.tugas.index', $pengampu->id)
                ->with('toast_error', 'Batas waktu 1x24 jam untuk mengedit tugas telah berakhir.');
        }

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $pertemuans = $pengampu->rpsPertemuans();

        return view('lms.tugas.edit', compact('pengampu', 'tugas', 'pertemuans'));
    }

    public function update(Request $request, Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return redirect()->route('lms.tugas.index', $pengampu->id)
                ->with('toast_error', 'Batas waktu 1x24 jam untuk mengedit tugas telah berakhir.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'required|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'batas_upload_mb' => $request->batas_upload_mb,
        ];

        if ($request->hasFile('file')) {
            if ($tugas->file_lampiran) {
                Storage::disk('public')->delete($tugas->file_lampiran);
            }

            $data['file_lampiran'] = $request->file('file')->store('lms/tugas', 'public');
        }

        $tugas->update($data);

        return redirect()
            ->route('lms.tugas.index', $pengampu->id)
            ->with('toast_success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Pengampu $pengampu, LmsTugas $tugas)
    {
        $this->authorizeWrite($pengampu);
        abort_if($tugas->pengampu_id !== $pengampu->id, 404);

        if (! $tugas->canBeModified()) {
            return back()->with('toast_error', 'Batas waktu 1x24 jam untuk menghapus tugas telah berakhir.');
        }

        if ($tugas->file_lampiran) {
            Storage::disk('public')->delete($tugas->file_lampiran);
        }

        foreach ($tugas->submissions as $submission) {
            if ($submission->file_jawaban) {
                Storage::disk('public')->delete($submission->file_jawaban);
            }

            $submission->delete();
        }

        $tugas->delete();

        return back()->with('toast_success', 'Tugas berhasil dihapus.');
    }

    public function nilai(Request $request, LmsSubmission $submission)
    {
        $user = Auth::user();
        abort_if($user->isAdmin(), 403, 'Admin hanya memiliki akses melihat (read-only) pada kelas LMS.');

        $dosen = $user->dosen;
        abort_if(! $dosen || $submission->lmsTugas->pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'nilai' => 'nullable|numeric|min:0|max:100',
            'catatan_dosen' => 'nullable|string',
        ]);

        $submission->update([
            'nilai' => $request->nilai,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        $pengampu = $submission->lmsTugas->pengampu;

        app(PenilaianService::class)->simpanNilaiMahasiswa($pengampu, $submission->mahasiswa);

        if ($submission->nilai !== null && $submission->mahasiswa?->user) {
            $submission->mahasiswa->user->notify(new NilaiDiberikan($pengampu, $submission));
        }

        return back()->with('toast_success', 'Nilai berhasil disimpan.');
    }

    public function rekap(Pengampu $pengampu)
    {
        $this->authorizeRead($pengampu);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->get();
        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();

        $nilaiByMhs = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
            ->whereIn('mahasiswa_id', $mahasiswas->pluck('id'))
            ->get()
            ->groupBy('mahasiswa_id');

        $bobot = app(PenilaianService::class)->bobotKomponen($pengampu);

        $instrumenCpmk = $pengampu->instrumenCpmk()->with('cpmk')->get();

        $calc = app(AssessmentCalculationService::class);
        $cpmkConfig = $calc->cpmkConfigForMataKuliah($pengampu->mataKuliah);

        return view('lms.tugas.rekap', compact('pengampu', 'mahasiswas', 'tugasList', 'nilaiByMhs', 'bobot', 'instrumenCpmk', 'cpmkConfig'));
    }

    public function simpanKomponen(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*.quiz' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.praktikum' => 'nullable|numeric|min:0|max:100',
            'nilai.*.project' => 'nullable|numeric|min:0|max:100',
        ]);

        $service = app(PenilaianService::class);
        $komponenLain = ['quiz', 'uts', 'uas', 'praktikum', 'project'];

        foreach ($request->input('nilai', []) as $mahasiswaId => $nilaiKomponen) {
            $mahasiswa = $pengampu->mahasiswas()->find($mahasiswaId);

            if (! $mahasiswa) {
                continue;
            }

            foreach ($komponenLain as $komponen) {
                $nilai = $nilaiKomponen[$komponen] ?? null;

                $nilai = ($nilai === '' || $nilai === null) ? null : $nilai;

                if ($nilai === null) {
                    LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->where('komponen', $komponen)
                        ->delete();

                    continue;
                }

                LmsNilaiMahasiswa::updateOrCreate(
                    ['pengampu_id' => $pengampu->id, 'mahasiswa_id' => $mahasiswa->id, 'komponen' => $komponen],
                    ['nilai' => $nilai]
                );
            }

            $service->simpanNilaiMahasiswa($pengampu, $mahasiswa);
        }

        $synced = $this->syncToAssessment($pengampu, $service);

        if (! $synced) {
            return back()->with('toast_success', 'Nilai LMS berhasil disimpan. Catatan: Bobot CPMK di RPS belum dikonfigurasi, silakan lengkapi RPS agar nilai terkirim ke Asesmen OBE.');
        }

        return back()->with('toast_success', 'Penilaian kelas berhasil disimpan & dikirim ke Modul Asesmen OBE.');
    }

    public function hitungUlangNilai(Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $service = app(PenilaianService::class);
        $service->simpanNilaiKelas($pengampu);

        $synced = $this->syncToAssessment($pengampu, $service);

        if (! $synced) {
            return back()->with('toast_success', 'Nilai LMS berhasil dihitung ulang. Catatan: Bobot CPMK di RPS belum dikonfigurasi, silakan lengkapi RPS agar nilai terkirim ke Asesmen OBE.');
        }

        return back()->with('toast_success', 'Penilaian kelas berhasil disimpan & dikirim ke Modul Asesmen OBE.');
    }

    public function simpanInstrumenCpmk(Request $request, Pengampu $pengampu)
    {
        $this->authorizeWrite($pengampu);

        $request->validate([
            'cpmk_id' => 'required|exists:cpmks,id',
            'komponen' => 'required|in:tugas,quiz,uts,uas,praktikum,project',
            'bobot_kontribusi' => 'required|numeric|min:0|max:100',
        ]);

        LmsInstrumenCpmk::updateOrCreate(
            [
                'pengampu_id' => $pengampu->id,
                'cpmk_id' => $request->cpmk_id,
                'komponen' => $request->komponen,
            ],
            ['bobot_kontribusi' => $request->bobot_kontribusi]
        );

        return back()->with('toast_success', 'Pemetaan instrumen → CPMK berhasil disimpan.');
    }

    public function hapusInstrumenCpmk(Pengampu $pengampu, LmsInstrumenCpmk $instrumen)
    {
        $this->authorizeWrite($pengampu);
        abort_if($instrumen->pengampu_id !== $pengampu->id, 404);

        $instrumen->delete();

        return back()->with('toast_success', 'Pemetaan instrumen → CPMK berhasil dihapus.');
    }

    private function syncToAssessment(Pengampu $pengampu, PenilaianService $service): bool
    {
        $assessment = Assessment::firstOrCreate(
            ['pengampu_id' => $pengampu->id],
            ['status' => Assessment::STATUS_DRAFT, 'created_by' => Auth::id()]
        );

        $calc = app(AssessmentCalculationService::class);
        $config = $calc->cpmkConfigForMataKuliah($pengampu->mataKuliah);

        $instrumenMap = LmsInstrumenCpmk::where('pengampu_id', $pengampu->id)
            ->get()
            ->groupBy('cpmk_id');

        $scoredCount = 0;
        if ($config->isNotEmpty()) {
            foreach ($pengampu->mahasiswas as $mahasiswa) {
                $nilaiByKomponen = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->pluck('nilai', 'komponen');

                foreach ($config as $cpmkId => $meta) {
                    $instrumens = $instrumenMap->get($cpmkId);

                    if ($instrumens && $instrumens->isNotEmpty()) {
                        $totalBobot = $instrumens->sum('bobot_kontribusi');
                        $weightedSum = 0;
                        $hasValue = false;

                        foreach ($instrumens as $instrumen) {
                            $nilai = $nilaiByKomponen->get($instrumen->komponen);
                            if ($nilai !== null) {
                                $weightedSum += ($nilai / 100) * $instrumen->bobot_kontribusi;
                                $hasValue = true;
                            }
                        }

                        $skorCpmk = $hasValue && $totalBobot > 0
                            ? round(($weightedSum / $totalBobot) * $meta['bobot'], 2)
                            : null;
                    } else {
                        $nilaiAkhir = $service->hitungNilaiAkhir($pengampu, $mahasiswa);
                        $skorCpmk = $nilaiAkhir !== null
                            ? round(($nilaiAkhir / 100) * $meta['bobot'], 2)
                            : null;
                    }

                    if ($skorCpmk !== null) {
                        AssessmentScore::updateOrCreate(
                            [
                                'assessment_id' => $assessment->id,
                                'mahasiswa_id' => $mahasiswa->id,
                                'cpmk_id' => $cpmkId,
                            ],
                            ['nilai' => $skorCpmk]
                        );
                        $scoredCount++;
                    }
                }
            }
        }

        $status = $scoredCount > 0 ? Assessment::STATUS_DINILAI : Assessment::STATUS_DRAFT;
        $assessment->update(['status' => $status]);

        return $scoredCount > 0;
    }

    private function pertemuanMilikKelas(Pengampu $pengampu): Closure
    {
        return function ($attribute, $value, $fail) use ($pengampu) {
            if ($value === null) {
                return;
            }

            $rpsId = $pengampu->mataKuliah?->rps?->id;

            if (! $rpsId || ! RpsPertemuan::where('id', $value)->where('rps_id', $rpsId)->exists()) {
                $fail('Pertemuan tidak valid untuk mata kuliah ini.');
            }
        };
    }
}
