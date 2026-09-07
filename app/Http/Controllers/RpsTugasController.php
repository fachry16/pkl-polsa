<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use App\Models\Rps;
use App\Models\RpsTugas;
use App\Notifications\TugasBaru;
use App\Rules\LmsFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RpsTugasController extends Controller
{
    use AuthorizesRps;

    /**
     * Menampilkan daftar rancangan tugas dan latihan.
     */
    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $tugas = $rps->tugas()
            ->orderBy('minggu_topik')
            ->paginate(16);

        $pertemuans = $rps->pertemuans()->orderBy('minggu')->get();
        $pengampuKelas = $this->pengampuKelas($rps);

        return view('rps-tugas.index', compact('rps', 'tugas', 'pertemuans', 'pengampuKelas'));
    }

    /**
     * Form tambah rancangan tugas.
     */
    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-tugas.create', compact('rps'));
    }

    /**
     * Simpan rancangan tugas.
     */
    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu_topik' => 'required|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'sub_cpmk' => 'nullable|string|max:255',
            'penugasan' => 'nullable|string|max:255',
            'ruang_lingkup' => 'nullable|string',
            'cara_pengerjaan' => 'nullable|string',
            'batas_waktu' => 'nullable|string|max:255',
            'luaran_tugas' => 'nullable|string',
            'deadline' => 'nullable|date',
            'bobot_nilai' => 'nullable|integer|min:0|max:100',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'rps_id' => $rps->id,
            'minggu_topik' => $request->minggu_topik,
            'nama_tugas' => $request->nama_tugas,
            'sub_cpmk' => $request->sub_cpmk,
            'penugasan' => $request->penugasan,
            'ruang_lingkup' => $request->ruang_lingkup,
            'cara_pengerjaan' => $request->cara_pengerjaan,
            'batas_waktu' => $request->batas_waktu,
            'luaran_tugas' => $request->luaran_tugas,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai ?? 100,
        ];

        if ($request->hasFile('file')) {
            $data['file_soal'] = $request->file('file')->store('lms/tugas', 'public');
        }

        RpsTugas::create($data);

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Rancangan tugas berhasil ditambahkan.');
    }

    /**
     * Form edit.
     */
    public function edit(Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-tugas.edit', compact('rps', 'tugas'));
    }

    /**
     * Update rancangan tugas.
     */
    public function update(Request $request, Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu_topik' => 'required|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'sub_cpmk' => 'nullable|string|max:255',
            'penugasan' => 'nullable|string|max:255',
            'ruang_lingkup' => 'nullable|string',
            'cara_pengerjaan' => 'nullable|string',
            'batas_waktu' => 'nullable|string|max:255',
            'luaran_tugas' => 'nullable|string',
            'deadline' => 'nullable|date',
            'bobot_nilai' => 'nullable|integer|min:0|max:100',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'minggu_topik' => $request->minggu_topik,
            'nama_tugas' => $request->nama_tugas,
            'sub_cpmk' => $request->sub_cpmk,
            'penugasan' => $request->penugasan,
            'ruang_lingkup' => $request->ruang_lingkup,
            'cara_pengerjaan' => $request->cara_pengerjaan,
            'batas_waktu' => $request->batas_waktu,
            'luaran_tugas' => $request->luaran_tugas,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai ?? 100,
        ];

        if ($request->hasFile('file')) {
            if ($tugas->file_soal) {
                Storage::disk('public')->delete($tugas->file_soal);
            }
            $data['file_soal'] = $request->file('file')->store('lms/tugas', 'public');
        }

        $tugas->update($data);

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Rancangan tugas berhasil diperbarui.');
    }

    /**
     * Hapus rancangan tugas.
     */
    public function destroy(Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        if ($tugas->file_soal) {
            Storage::disk('public')->delete($tugas->file_soal);
        }

        $tugas->delete();

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Rancangan tugas berhasil dihapus.');
    }

    /**
     * Upload / Konfirmasi rancangan tugas dari RPS ke seluruh kelas LMS sebagai Draf.
     */
    public function uploadKeLms(Request $request, Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        $pengampuKelas = $this->pengampuKelas($rps);

        if ($pengampuKelas->isEmpty()) {
            return redirect()
                ->route('rps.tugas.index', $rps->id)
                ->with('error', 'Belum ada kelas LMS (pengampu) yang Anda ampu untuk mata kuliah ini.');
        }

        $mingguNo = null;
        if ($tugas->minggu_topik && preg_match('/\d+/', $tugas->minggu_topik, $matches)) {
            $mingguNo = (int) $matches[0];
        }

        $pertemuan = $mingguNo
            ? $rps->pertemuans()->where('minggu', $mingguNo)->first()
            : null;

        $instruksiArr = [];
        if ($tugas->penugasan) {
            $instruksiArr[] = "Penugasan: " . $tugas->penugasan;
        }
        if ($tugas->ruang_lingkup) {
            $instruksiArr[] = "Ruang Lingkup: " . $tugas->ruang_lingkup;
        }
        if ($tugas->cara_pengerjaan) {
            $instruksiArr[] = "Cara Pengerjaan: " . $tugas->cara_pengerjaan;
        }
        if ($tugas->luaran_tugas) {
            $instruksiArr[] = "Luaran Tugas: " . $tugas->luaran_tugas;
        }

        $instruksiText = implode("\n\n", $instruksiArr) ?: ($tugas->nama_tugas ?? 'Tugas RPS');
        $deadline = $tugas->deadline ?? now()->addDays(7);
        $bobot = $tugas->bobot_nilai ?? 100;

        $createdCount = 0;

        foreach ($pengampuKelas as $pengampu) {
            $sudahAda = LmsTugas::where('pengampu_id', $pengampu->id)
                ->where('judul', $tugas->nama_tugas)
                ->exists();

            if (! $sudahAda) {
                LmsTugas::create([
                    'pengampu_id' => $pengampu->id,
                    'rps_pertemuan_id' => $pertemuan?->id,
                    'judul' => $tugas->nama_tugas,
                    'instruksi' => $instruksiText,
                    'file_lampiran' => $tugas->file_soal,
                    'deadline' => $deadline,
                    'bobot_nilai' => $bobot,
                    'is_active' => false,
                ]);
                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            return redirect()
                ->route('rps.tugas.index', $rps->id)
                ->with('success', 'Tugas RPS sudah pernah diunggah ke seluruh kelas LMS.');
        }

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', "Rancangan tugas berhasil diunggah ke {$createdCount} kelas LMS sebagai Draf. Silakan klik \"Tugaskan\" pada kelas LMS untuk mengaktifkan tugas.");
    }

    /**
     * Daftar kelas (pengampu) milik dosen saat ini untuk mata kuliah RPS.
     */
    protected function pengampuKelas(Rps $rps)
    {
        $dosen = Auth::user()->dosen;

        $query = Pengampu::where('mata_kuliah_id', $rps->mata_kuliah_id);

        if ($dosen && ! Auth::user()->isAdmin()) {
            $query->where('dosen_id', $dosen->id);
        }

        return $query->with(['tahunAkademik'])
            ->orderBy('kelas')
            ->get();
    }
}
