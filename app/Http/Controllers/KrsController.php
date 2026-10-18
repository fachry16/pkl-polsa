<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\LmsNilaiMahasiswa;
use App\Models\LmsSubmission;
use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\RpsTugas;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Notifications\KrsBaruAdmin;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KrsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $krsList = Krs::with(['programStudi', 'mataKuliah', 'dosen.user', 'tahunAkademik', 'mahasiswas'])
            ->when($kaprodiProdiId, function ($q) use ($kaprodiProdiId) {
                $q->where('program_studi_id', $kaprodiProdiId);
            })
            ->latest()
            ->paginate(10);

        return view('krs.index', compact('krsList'));
    }

    public function create()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $mataKuliahs = $kaprodiProdiId
            ? MataKuliah::whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $kaprodiProdiId))->orderBy('kode')->get()
            : MataKuliah::orderBy('kode')->get();

        $dosens = $kaprodiProdiId
            ? Dosen::where('program_studi_id', $kaprodiProdiId)->with('user')->orderBy('user_id')->get()
            : Dosen::with('user')->orderBy('user_id')->get();

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('krs.create', compact('programStudis', 'mataKuliahs', 'dosens', 'tahunAkademiks'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        $programStudiId = $kaprodiProdiId ?: $request->program_studi_id;

        $mataKuliah = MataKuliah::findOrFail($request->mata_kuliah_id);
        $kodeProdi = ProgramStudi::findOrFail($programStudiId)->kode_prodi;
        $kelas = $kodeProdi.' '.$mataKuliah->semester;

        $already = Krs::where('mata_kuliah_id', $request->mata_kuliah_id)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('dosen_id', $request->dosen_id)
            ->where('kelas', $kelas)
            ->exists();

        if ($already) {
            return back()->with('error', 'Data KRS untuk kombinasi mata kuliah, dosen, dan tahun akademik ini sudah ada.');
        }

        $krs = DB::transaction(function () use ($request, $programStudiId, $kelas) {
            $krs = Krs::create(
                array_merge($request->except('kelas'), [
                    'program_studi_id' => $programStudiId,
                    'kelas' => $kelas,
                ])
            );

            $tahunAkademik = TahunAkademik::find($request->tahun_akademik_id);

            $pengampu = Pengampu::create([
                'krs_id' => $krs->id,
                'dosen_id' => $request->dosen_id,
                'mata_kuliah_id' => $request->mata_kuliah_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester_akademik' => $tahunAkademik->semester,
                'kelas' => $kelas,
            ]);

            return $krs;
        });

        $this->syncRpsTugasToPengampu($krs, $krs->pengampu);

        $pembuat = auth()->user()->name ?? 'User';
        $adminUsers = User::where('role', 'admin')->orWhereJsonContains('roles', 'admin')->get();
        foreach ($adminUsers as $admin) {
            if ($admin->id !== auth()->id()) {
                $admin->notify(new KrsBaruAdmin($krs, $pembuat));
            }
        }

        return redirect()
            ->route('krs.show', $krs)
            ->with('success', 'KRS berhasil dibuat dan muncul di menu Pengampu.');
    }

    public function show(Krs $krs)
    {
        $this->authorizeKrsRead($krs);

        if (auth()->check() && auth()->user()->isAdmin()) {
            auth()->user()->unreadNotifications()
                ->where('type', KrsBaruAdmin::class)
                ->where('data->krs_id', $krs->id)
                ->update(['read_at' => now()]);
        }

        $krs->load(['programStudi', 'mataKuliah', 'dosen.user', 'tahunAkademik', 'mahasiswas' => function ($q) {
            $q->with('programStudi')->orderBy('nim');
        }]);

        $mahasiswaIds = $krs->mahasiswas->pluck('id');

        $semuaMahasiswa = Mahasiswa::with('programStudi')
            ->where('program_studi_id', $krs->program_studi_id)
            ->whereNotIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        return view('krs.show', compact('krs', 'semuaMahasiswa'));
    }

    public function storeMahasiswa(Request $request, Krs $krs)
    {
        $this->authorizeKrs($krs);

        $ids = array_filter((array) $request->mahasiswa_id);

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu mahasiswa.');
        }

        $validIds = Mahasiswa::whereIn('id', $ids)->pluck('id')->all();
        abort_unless(count($validIds) === count($ids), 422);

        $krs->mahasiswas()->syncWithoutDetaching($validIds);

        if ($pengampu = $krs->pengampu) {
            $pengampu->mahasiswas()->syncWithoutDetaching($ids);
        }

        return back()->with('success', count($ids).' mahasiswa berhasil ditambahkan ke KRS.');
    }

    public function destroyMahasiswa(Krs $krs, Mahasiswa $mahasiswa)
    {
        $this->authorizeKrs($krs);

        if ($pengampu = $krs->pengampu) {
            $driveService = app(GoogleDriveService::class);

            $submissions = LmsSubmission::where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('lmsTugas', fn ($q) => $q->where('pengampu_id', $pengampu->id))
                ->get();

            foreach ($submissions as $sub) {
                if ($sub->file_jawaban) {
                    $driveService->deleteFile($sub->file_jawaban);
                }
                $sub->delete();
            }

            LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->delete();
        }

        $krs->mahasiswas()->detach($mahasiswa->id);

        if ($pengampu = $krs->pengampu) {
            $pengampu->mahasiswas()->detach($mahasiswa->id);
        }

        return back()->with('success', 'Mahasiswa berhasil dihapus dari KRS.');
    }

    public function destroy(Krs $krs)
    {
        $this->authorizeKrs($krs);

        DB::transaction(function () use ($krs) {
            $driveService = app(GoogleDriveService::class);

            if ($pengampu = $krs->pengampu) {
                foreach ($pengampu->lmsTugas as $tugas) {
                    foreach ($tugas->submissions as $sub) {
                        if ($sub->file_jawaban) {
                            $driveService->deleteFile($sub->file_jawaban);
                        }
                        $sub->delete();
                    }
                    if ($tugas->file_lampiran) {
                        $driveService->deleteFile($tugas->file_lampiran);
                    }
                    $tugas->delete();
                }

                foreach ($pengampu->lmsMateris as $materi) {
                    if ($materi->file_path) {
                        $driveService->deleteFile($materi->file_path);
                    }
                    $materi->delete();
                }

                foreach ($pengampu->lmsForumDiskusis as $diskusi) {
                    foreach ($diskusi->replies as $reply) {
                        if ($reply->file_path) {
                            $driveService->deleteFile($reply->file_path);
                        }
                    }
                    $diskusi->replies()->delete();

                    if ($diskusi->file_path) {
                        $driveService->deleteFile($diskusi->file_path);
                    }
                    $diskusi->delete();
                }

                $pengampu->lmsPengumumans()->delete();
                $pengampu->lmsSesiAbsensis()->delete();
                $pengampu->assessment()->delete();
                $pengampu->mahasiswas()->detach();
                $pengampu->delete();
            }

            $krs->mahasiswas()->detach();
            $krs->delete();
        });

        return redirect()
            ->route('krs.index')
            ->with('success', 'Data KRS berhasil dihapus.');
    }

    private function authorizeKrsRead(Krs $krs)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $krs->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }

    private function authorizeKrs(Krs $krs)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $krs->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }

    private function syncRpsTugasToPengampu(Krs $krs, Pengampu $pengampu): void
    {
        $rps = $krs->mataKuliah?->rps;

        if (! $rps) {
            return;
        }

        $rpsTugasList = RpsTugas::where('rps_id', $rps->id)->get();

        foreach ($rpsTugasList as $rpsTugas) {
            $sudahAda = LmsTugas::where('pengampu_id', $pengampu->id)
                ->where('rps_tugas_id', $rpsTugas->id)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            $pertemuan = null;
            if ($rpsTugas->minggu_topik && preg_match('/\d+/', $rpsTugas->minggu_topik, $matches)) {
                $pertemuan = $rps->pertemuans()->where('minggu', (int) $matches[0])->first();
            }

            $instruksiArr = [];
            if ($rpsTugas->penugasan) {
                $instruksiArr[] = 'Penugasan: '.$rpsTugas->penugasan;
            }
            if ($rpsTugas->ruang_lingkup) {
                $instruksiArr[] = 'Ruang Lingkup: '.$rpsTugas->ruang_lingkup;
            }
            if ($rpsTugas->cara_pengerjaan) {
                $instruksiArr[] = 'Cara Pengerjaan: '.$rpsTugas->cara_pengerjaan;
            }
            if ($rpsTugas->luaran_tugas) {
                $instruksiArr[] = 'Luaran Tugas: '.$rpsTugas->luaran_tugas;
            }

            LmsTugas::create([
                'pengampu_id' => $pengampu->id,
                'rps_pertemuan_id' => $pertemuan?->id,
                'rps_tugas_id' => $rpsTugas->id,
                'judul' => $rpsTugas->nama_tugas,
                'instruksi' => implode("\n\n", $instruksiArr) ?: ($rpsTugas->nama_tugas ?? 'Tugas RPS'),
                'file_lampiran' => $rpsTugas->file_soal,
                'deadline' => $rpsTugas->deadline ?? now()->addDays(7),
                'bobot_nilai' => $rpsTugas->bobot_nilai ?? 100,
                'is_active' => false,
            ]);
        }
    }
}
