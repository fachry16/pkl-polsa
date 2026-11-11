<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Http\Controllers\Concerns\BuildsRumusanNilaiAkhir;
use App\Models\Kurikulum;
use App\Models\RumusanNilaiAkhirMk;
use Illuminate\Http\Request;

class RumusanNilaiAkhirMkController extends Controller
{
    use AuthorizesKurikulum;
    use BuildsRumusanNilaiAkhir;

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

        return view(
            'rumusan-nilai-akhir-mk.create',
            $this->rumusanFormViewData($kurikulum) + compact('kurikulum')
        );
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $validated = $this->validatedRumusanIds($request, $kurikulum);
        $this->validasiRelasiRumusan($kurikulum, $validated);
        $metode = $this->metodeUntukRumusan($kurikulum, $validated);

        RumusanNilaiAkhirMk::create([
            'kurikulum_id' => $kurikulum->id,
            ...$validated,
            'skor_maks' => $metode->total,
            'total' => $metode->total,
        ]);

        return redirect()
            ->route('kurikulum.rumusan-nilai-akhir-mk.index', $kurikulum->id)
            ->with('success', 'Rumusan nilai akhir MK berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, RumusanNilaiAkhirMk $rumusanNilaiAkhirMk)
    {
        $this->authorizeKurikulum($kurikulum);

        $item = $rumusanNilaiAkhirMk;

        return view(
            'rumusan-nilai-akhir-mk.edit',
            $this->rumusanFormViewData($kurikulum, $item) + compact('kurikulum', 'item')
        );
    }

    public function update(Request $request, Kurikulum $kurikulum, RumusanNilaiAkhirMk $rumusanNilaiAkhirMk)
    {
        $this->authorizeKurikulum($kurikulum);

        $validated = $this->validatedRumusanIds($request, $kurikulum);
        $this->validasiRelasiRumusan($kurikulum, $validated);
        $metode = $this->metodeUntukRumusan($kurikulum, $validated);

        $rumusanNilaiAkhirMk->update([
            ...$validated,
            'skor_maks' => $metode->total,
            'total' => $metode->total,
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
