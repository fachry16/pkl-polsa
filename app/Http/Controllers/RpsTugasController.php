<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use App\Models\RpsTugas;
use Illuminate\Http\Request;

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

        return view('rps-tugas.index', compact('rps', 'tugas'));
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
}
