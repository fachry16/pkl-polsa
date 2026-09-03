<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\RumusanNilaiAkhirCpl;
use Illuminate\Http\Request;

class RumusanNilaiAkhirCplController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $items = $kurikulum->rumusanNilaiAkhirCpls()
            ->with(['cpl', 'mataKuliah', 'cpmk'])
            ->latest()
            ->get();

        return view('rumusan-nilai-akhir-cpl.index', compact('kurikulum', 'items'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();

        return view('rumusan-nilai-akhir-cpl.create', compact('kurikulum', 'cpls', 'mataKuliahs', 'cpmks'));
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

        RumusanNilaiAkhirCpl::create([
            'kurikulum_id' => $kurikulum->id,
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
            'skor_maks' => $skorMaks,
            'total' => $total,
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir CPL berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, RumusanNilaiAkhirCpl $rumusanNilaiAkhirCpl)
    {
        $this->authorizeKurikulum($kurikulum);

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();
        $item = $rumusanNilaiAkhirCpl;

        return view('rumusan-nilai-akhir-cpl.edit', compact('kurikulum', 'item', 'cpls', 'mataKuliahs', 'cpmks'));
    }

    public function update(Request $request, Kurikulum $kurikulum, RumusanNilaiAkhirCpl $rumusanNilaiAkhirCpl)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'cpmk_id' => 'required|exists:cpmks,id',
        ]);

        $rumusanNilaiAkhirCpl->update([
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
            'skor_maks' => (float) ($request->skor_maks ?? 0),
            'total' => (float) ($request->total ?? 0),
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir CPL berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, RumusanNilaiAkhirCpl $rumusanNilaiAkhirCpl)
    {
        $this->authorizeKurikulum($kurikulum);

        $rumusanNilaiAkhirCpl->delete();

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir CPL berhasil dihapus.');
    }
}
