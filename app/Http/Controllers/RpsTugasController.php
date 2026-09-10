<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\LmsTugas;
use App\Models\Pengampu;
use App\Models\Rps;
use App\Models\RpsTugas;
use App\Rules\LmsFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RpsTugasController extends Controller
{
    use AuthorizesRps;

    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $tugas = $rps->tugas()
            ->withCount('lmsTugas')
            ->orderBy('minggu_topik')
            ->paginate(16);

        $pertemuans = $rps->pertemuans()->orderBy('minggu')->get();
        $pengampuKelas = $this->pengampuKelas($rps);

        return view('rps-tugas.index', compact('rps', 'tugas', 'pertemuans', 'pengampuKelas'));
    }

    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-tugas.create', compact('rps'));
    }

    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu_topik' => 'required|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'kategori_komponen' => 'nullable|string|in:tugas,quiz,uts,uas,praktikum,project',
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
            'kategori_komponen' => $request->kategori_komponen ?? 'tugas',
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

        $tugas = RpsTugas::create($data);
        $synced = $this->syncToPengampu($rps, $tugas);

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', "Rancangan tugas berhasil ditambahkan & tersinkron ke {$synced} kelas LMS.");
    }

    public function edit(Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-tugas.edit', compact('rps', 'tugas'));
    }

    public function update(Request $request, Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu_topik' => 'required|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'kategori_komponen' => 'nullable|string|in:tugas,quiz,uts,uas,praktikum,project',
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
            'kategori_komponen' => $request->kategori_komponen ?? 'tugas',
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

        $oldTitle = $tugas->getOriginal('nama_tugas');
        $tugas->update($data);

        $instruksiText = $this->buildInstruksi($tugas);
        $deadline = $tugas->deadline ?? now()->addDays(7);
        $bobot = $tugas->bobot_nilai ?? 100;

        LmsTugas::where('rps_tugas_id', $tugas->id)
            ->orWhere(function ($query) use ($oldTitle, $rps) {
                $query->whereNull('rps_tugas_id')
                    ->where('judul', $oldTitle)
                    ->whereHas('pengampu', function ($q) use ($rps) {
                        $q->where('mata_kuliah_id', $rps->mata_kuliah_id);
                    });
            })
            ->update([
                'rps_tugas_id' => $tugas->id,
                'judul' => $tugas->nama_tugas,
                'instruksi' => $instruksiText,
                'file_lampiran' => $tugas->file_soal,
                'deadline' => $deadline,
                'bobot_nilai' => $bobot,
            ]);

        $synced = $this->syncToPengampu($rps, $tugas);

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', "Rancangan tugas berhasil diperbarui & tersinkron ke {$synced} kelas LMS.");
    }

    public function destroy(Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        LmsTugas::where('rps_tugas_id', $tugas->id)->update(['is_active' => false]);

        if ($tugas->file_soal) {
            Storage::disk('public')->delete($tugas->file_soal);
        }

        $tugas->delete();

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Rancangan tugas berhasil dihapus & tugas LMS terkait dinonaktifkan.');
    }

    public function uploadKeLms(Request $request, Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        $synced = $this->syncToPengampu($rps, $tugas);

        if ($synced === 0) {
            return redirect()
                ->route('rps.tugas.index', $rps->id)
                ->with('success', 'Tugas RPS sudah pernah diunggah ke seluruh kelas LMS.');
        }

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', "Rancangan tugas berhasil diunggah ke {$synced} kelas LMS sebagai Draf. Silakan klik \"Tugaskan\" pada kelas LMS untuk mengaktifkan tugas.");
    }

    protected function pengampuKelas(Rps $rps)
    {
        return Pengampu::where('mata_kuliah_id', $rps->mata_kuliah_id)
            ->with(['tahunAkademik', 'dosen.user'])
            ->orderBy('kelas')
            ->get();
    }

    private function syncToPengampu(Rps $rps, RpsTugas $tugas): int
    {
        $pengampus = $this->pengampuKelas($rps);

        if ($pengampus->isEmpty()) {
            return 0;
        }

        $pertemuan = $this->resolvePertemuan($rps, $tugas);
        $instruksiText = $this->buildInstruksi($tugas);
        $deadline = $tugas->deadline ?? now()->addDays(7);
        $bobot = $tugas->bobot_nilai ?? 100;
        $createdCount = 0;

        foreach ($pengampus as $pengampu) {
            $sudahAda = LmsTugas::where('pengampu_id', $pengampu->id)
                ->where('rps_tugas_id', $tugas->id)
                ->exists();

            if (! $sudahAda) {
                LmsTugas::create([
                    'pengampu_id' => $pengampu->id,
                    'rps_pertemuan_id' => $pertemuan?->id,
                    'rps_tugas_id' => $tugas->id,
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

        return $createdCount;
    }

    private function resolvePertemuan(Rps $rps, RpsTugas $tugas)
    {
        if (! $tugas->minggu_topik || ! preg_match('/\d+/', $tugas->minggu_topik, $matches)) {
            return null;
        }

        return $rps->pertemuans()->where('minggu', (int) $matches[0])->first();
    }

    private function buildInstruksi(RpsTugas $tugas): string
    {
        $parts = [];
        if ($tugas->penugasan) {
            $parts[] = 'Penugasan: '.$tugas->penugasan;
        }
        if ($tugas->ruang_lingkup) {
            $parts[] = 'Ruang Lingkup: '.$tugas->ruang_lingkup;
        }
        if ($tugas->cara_pengerjaan) {
            $parts[] = 'Cara Pengerjaan: '.$tugas->cara_pengerjaan;
        }
        if ($tugas->luaran_tugas) {
            $parts[] = 'Luaran Tugas: '.$tugas->luaran_tugas;
        }

        return implode("\n\n", $parts) ?: ($tugas->nama_tugas ?? 'Tugas RPS');
    }
}
