<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\RumusanNilaiAkhirMk;
use Illuminate\Http\Request;

class RumusanNilaiAkhirMkController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $items = $kurikulum->rumusanNilaiAkhirMks()
            ->with(['cpl', 'mataKuliah', 'cpmk'])
            ->latest()
            ->get();

        return view('rumusan-nilai-akhir-mk.index', compact('kurikulum', 'items'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();

        return view('rumusan-nilai-akhir-mk.create', compact('kurikulum', 'cpls', 'mataKuliahs', 'cpmks'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'cpmk_id' => 'required|exists:cpmks,id',
        ]);

        $skorMaks = (float) ($request->skor_maks ?? 0);
        $total = (float) ($request->total ?? 0);

        RumusanNilaiAkhirMk::create([
            'kurikulum_id' => $kurikulum->id,
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
            'skor_maks' => $skorMaks,
            'total' => $total,
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir MK berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, RumusanNilaiAkhirMk $rumusanNilaiAkhirMk)
    {
        $this->authorizeKurikulum($kurikulum);

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();
        $item = $rumusanNilaiAkhirMk;

        return view('rumusan-nilai-akhir-mk.edit', compact('kurikulum', 'item', 'cpls', 'mataKuliahs', 'cpmks'));
    }

    public function update(Request $request, Kurikulum $kurikulum, RumusanNilaiAkhirMk $rumusanNilaiAkhirMk)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'cpmk_id' => 'required|exists:cpmks,id',
        ]);

        $rumusanNilaiAkhirMk->update([
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
            'skor_maks' => (float) ($request->skor_maks ?? 0),
            'total' => (float) ($request->total ?? 0),
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir MK berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, RumusanNilaiAkhirMk $rumusanNilaiAkhirMk)
    {
        $this->authorizeKurikulum($kurikulum);

        $rumusanNilaiAkhirMk->delete();

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir MK berhasil dihapus.');
    }
}
