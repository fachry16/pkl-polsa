<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CplBkMkController extends Controller
{
    public function index(Request $request, Kurikulum $kurikulum)
    {
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get();
        $mataKuliahId = $request->mata_kuliah_id;
        $bahanKajians = $kurikulum->bahanKajians()->orderBy('kode_bk')->get();
        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $checked = [];

        if ($mataKuliahId) {
            $checked = DB::table('cpl_bahan_kajian_mata_kuliah')
                ->where('mata_kuliah_id', $mataKuliahId)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->cpl_id . '-' . $item->bahan_kajian_id => true];
                })
                ->toArray();
        }

        return view('cpl-bk-mk.index', compact('kurikulum', 'mataKuliahs', 'mataKuliahId', 'bahanKajians', 'cpls', 'checked'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
        ]);

        DB::table('cpl_bahan_kajian_mata_kuliah')
            ->where('mata_kuliah_id', $request->mata_kuliah_id)
            ->delete();

        foreach ($request->mapping ?? [] as $key => $value) {
            [$cplId, $bkId] = explode('-', $key);

            DB::table('cpl_bahan_kajian_mata_kuliah')->insert([
                'mata_kuliah_id' => $request->mata_kuliah_id,
                'cpl_id' => $cplId,
                'bahan_kajian_id' => $bkId,
            ]);
        }

        return back()->with('success', 'Pemetaan CPL-BK-MK berhasil disimpan.');
    }
}
