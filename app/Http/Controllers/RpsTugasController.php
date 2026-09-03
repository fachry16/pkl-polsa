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
        ]);

        RpsTugas::create([
            'rps_id' => $rps->id,
            'minggu_topik' => $request->minggu_topik,
            'nama_tugas' => $request->nama_tugas,
            'sub_cpmk' => $request->sub_cpmk,
            'penugasan' => $request->penugasan,
            'ruang_lingkup' => $request->ruang_lingkup,
            'cara_pengerjaan' => $request->cara_pengerjaan,
            'batas_waktu' => $request->batas_waktu,
            'luaran_tugas' => $request->luaran_tugas,
        ]);

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
        ]);

        $tugas->update([
            'minggu_topik' => $request->minggu_topik,
            'nama_tugas' => $request->nama_tugas,
            'sub_cpmk' => $request->sub_cpmk,
            'penugasan' => $request->penugasan,
            'ruang_lingkup' => $request->ruang_lingkup,
            'cara_pengerjaan' => $request->cara_pengerjaan,
            'batas_waktu' => $request->batas_waktu,
            'luaran_tugas' => $request->luaran_tugas,
        ]);

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

        $tugas->delete();

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Rancangan tugas berhasil dihapus.');
    }

    /**
     * Upload rancangan tugas ke LMS sebagai LmsTugas pada pertemuan tertentu.
     */
    public function uploadKeLms(Request $request, Rps $rps, RpsTugas $tugas)
    {
        $this->authorizeRpsModel($rps);

        $dosen = Auth::user()->dosen;

        $request->validate([
            'pengampu_id' => ['required', 'exists:pengampus,id', function ($attribute, $value, $fail) use ($dosen, $rps) {
                $kelas = Pengampu::find($value);

                if (! $kelas
                    || $kelas->mata_kuliah_id !== $rps->mata_kuliah_id
                    || ($dosen && $kelas->dosen_id !== $dosen->id)) {
                    $fail('Kelas tidak valid untuk tugas ini.');
                }
            }],
            'judul' => 'required|string|max:255',
            'instruksi' => 'required|string',
            'rps_pertemuan_id' => ['required', 'exists:rps_pertemuans,id', function ($attribute, $value, $fail) use ($rps) {
                if (! $rps->pertemuans()->where('id', $value)->exists()) {
                    $fail('Pertemuan tidak valid untuk RPS ini.');
                }
            }],
            'deadline' => 'required|date',
            'bobot_nilai' => 'required|integer|min:0|max:100',
            'batas_upload_mb' => 'nullable|integer|min:1|max:50',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $pengampu = Pengampu::findOrFail($request->pengampu_id);

        $sudahAda = LmsTugas::where('pengampu_id', $pengampu->id)
            ->where('rps_pertemuan_id', $request->rps_pertemuan_id)
            ->where('judul', $request->judul)
            ->exists();

        if ($sudahAda) {
            return redirect()
                ->route('rps.tugas.index', $rps->id)
                ->with('success', 'Tugas sudah pernah diunggah ke LMS untuk kelas dan pertemuan tersebut.');
        }

        $data = [
            'pengampu_id' => $pengampu->id,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
            'judul' => $request->judul,
            'instruksi' => $request->instruksi,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai,
            'batas_upload_mb' => $request->batas_upload_mb,
        ];

        if ($request->hasFile('file')) {
            $data['file_lampiran'] = $request->file('file')->store('lms/tugas', 'public');
        }

        $lmsTugas = LmsTugas::create($data);

        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new TugasBaru($pengampu, $lmsTugas));
            }
        }

        return redirect()
            ->route('rps.tugas.index', $rps->id)
            ->with('success', 'Tugas berhasil diunggah ke LMS.');
    }

    /**
     * Daftar kelas (pengampu) milik dosen saat ini untuk mata kuliah RPS.
     */
    protected function pengampuKelas(Rps $rps)
    {
        $dosen = Auth::user()->dosen;

        if (! $dosen) {
            return collect();
        }

        return Pengampu::where('mata_kuliah_id', $rps->mata_kuliah_id)
            ->where('dosen_id', $dosen->id)
            ->with(['tahunAkademik'])
            ->orderBy('kelas')
            ->get();
    }
}
