<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\MetodeBobotPenilaian;
use Illuminate\Http\Request;

class MetodeBobotPenilaianController extends Controller
{
    use AuthorizesKurikulum;

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

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();

        return view('metode-bobot-penilaian.create', compact('kurikulum', 'cpls', 'mataKuliahs', 'cpmks'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'cpmk_id' => 'required|exists:cpmks,id',
        ]);

        $komponen = [
            'partisipasi',
            'kuis',
            'tugas_teori_individu',
            'unjuk_kerja_presentasi',
            'tes_tulis_uts',
            'tes_tulis_uas',
            'tugas_teori_kelompok',
            'tugas_praktikum',
            'responsi',
        ];

        $data = [
            'kurikulum_id' => $kurikulum->id,
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
        ];

        $total = 0;
        foreach ($komponen as $item) {
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

        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();
        $item = $metodeBobotPenilaian;

        return view('metode-bobot-penilaian.edit', compact('kurikulum', 'item', 'cpls', 'mataKuliahs', 'cpmks'));
    }

    public function update(Request $request, Kurikulum $kurikulum, MetodeBobotPenilaian $metodeBobotPenilaian)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'cpmk_id' => 'required|exists:cpmks,id',
        ]);

        $komponen = [
            'partisipasi',
            'kuis',
            'tugas_teori_individu',
            'unjuk_kerja_presentasi',
            'tes_tulis_uts',
            'tes_tulis_uas',
            'tugas_teori_kelompok',
            'tugas_praktikum',
            'responsi',
        ];

        $data = [
            'cpl_id' => $request->cpl_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'cpmk_id' => $request->cpmk_id,
        ];

        $total = 0;
        foreach ($komponen as $item) {
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
