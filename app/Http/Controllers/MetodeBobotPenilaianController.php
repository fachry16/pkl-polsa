<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Http\Controllers\Concerns\BuildsPemetaanCplCpmkMk;
use App\Models\Kurikulum;
use App\Models\MetodeBobotPenilaian;
use Illuminate\Http\Request;

class MetodeBobotPenilaianController extends Controller
{
    use AuthorizesKurikulum;
    use BuildsPemetaanCplCpmkMk;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $items = $kurikulum->metodeBobotPenilaians()
            ->with(['cpl', 'mataKuliah', 'cpmk'])
            ->latest()
            ->get();

        return view('metode-bobot-penilaian.index', compact('kurikulum', 'items'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $data = $this->pemetaanViewData($kurikulum);
        $initial = ['mk' => '', 'cpmk' => '', 'cpl' => ''];

        return view('metode-bobot-penilaian.create', compact('kurikulum', 'data', 'initial'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $ids = $this->validatedPemetaanIds($request, $kurikulum);
        $this->validasiRelasiPemetaan($kurikulum, $ids);

        $data = ['kurikulum_id' => $kurikulum->id, ...$ids];

        $total = 0;
        foreach (array_keys(MetodeBobotPenilaian::KOMPONEN) as $item) {
            $nilai = (float) ($request->input($item, 0));
            $data[$item] = $nilai;
            $total += $nilai;
        }
        $data['total'] = $total;

        MetodeBobotPenilaian::create($data);

        return redirect()
            ->route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id)
            ->with('success', 'Metode dan bobot penilaian berhasil ditambahkan.');
    }

    public function edit(Kurikulum $kurikulum, MetodeBobotPenilaian $metodeBobotPenilaian)
    {
        $this->authorizeKurikulum($kurikulum);

        $data = $this->pemetaanViewData($kurikulum);
        $item = $metodeBobotPenilaian;
        $initial = [
            'mk' => (string) old('mata_kuliah_id', $item->mata_kuliah_id),
            'cpmk' => (string) old('cpmk_id', $item->cpmk_id),
            'cpl' => (string) old('cpl_id', $item->cpl_id),
        ];

        return view('metode-bobot-penilaian.edit', compact('kurikulum', 'item', 'data', 'initial'));
    }

    public function update(Request $request, Kurikulum $kurikulum, MetodeBobotPenilaian $metodeBobotPenilaian)
    {
        $this->authorizeKurikulum($kurikulum);

        $ids = $this->validatedPemetaanIds($request, $kurikulum);
        $this->validasiRelasiPemetaan($kurikulum, $ids);

        $data = $ids;

        $total = 0;
        foreach (array_keys(MetodeBobotPenilaian::KOMPONEN) as $item) {
            $nilai = (float) ($request->input($item, 0));
            $data[$item] = $nilai;
            $total += $nilai;
        }
        $data['total'] = $total;

        $metodeBobotPenilaian->update($data);

        return redirect()
            ->route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id)
            ->with('success', 'Metode dan bobot penilaian berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, MetodeBobotPenilaian $metodeBobotPenilaian)
    {
        $this->authorizeKurikulum($kurikulum);

        $metodeBobotPenilaian->delete();

        return redirect()
            ->route('kurikulum.metode-bobot-penilaian.index', $kurikulum->id)
            ->with('success', 'Metode dan bobot penilaian berhasil dihapus.');
    }
}
