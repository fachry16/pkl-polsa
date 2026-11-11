<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Http\Controllers\Concerns\BuildsRumusanNilaiAkhir;
use App\Models\Kurikulum;
use App\Models\RumusanNilaiAkhirCpl;
use Illuminate\Http\Request;

class RumusanNilaiAkhirCplController extends Controller
{
    use AuthorizesKurikulum;
    use BuildsRumusanNilaiAkhir;

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

        return view(
            'rumusan-nilai-akhir-cpl.create',
            $this->rumusanFormViewData($kurikulum) + compact('kurikulum')
        );
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $validated = $this->validatedRumusanIds($request, $kurikulum);
        $this->validasiRelasiRumusan($kurikulum, $validated);
        $metode = $this->metodeUntukRumusan($kurikulum, $validated);

        RumusanNilaiAkhirCpl::create([
            'kurikulum_id' => $kurikulum->id,
            ...$validated,
            'skor_maks' => $metode->total,
            'total' => $metode->total,
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-cpl.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir CPL berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, RumusanNilaiAkhirCpl $rumusanNilaiAkhirCpl)
    {
        $this->authorizeKurikulum($kurikulum);

        $item = $rumusanNilaiAkhirCpl;

        return view(
            'rumusan-nilai-akhir-cpl.edit',
            $this->rumusanFormViewData($kurikulum, $item) + compact('kurikulum', 'item')
        );
    }

    public function update(Request $request, Kurikulum $kurikulum, RumusanNilaiAkhirCpl $rumusanNilaiAkhirCpl)
    {
        $this->authorizeKurikulum($kurikulum);

        $validated = $this->validatedRumusanIds($request, $kurikulum);
        $this->validasiRelasiRumusan($kurikulum, $validated);
        $metode = $this->metodeUntukRumusan($kurikulum, $validated);

        $rumusanNilaiAkhirCpl->update([
            ...$validated,
            'skor_maks' => $metode->total,
            'total' => $metode->total,
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
