<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CplBkMkController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);
        $mataKuliahs = $kurikulum->mataKuliahs()->orderBy('kode')->get()->load('cpls', 'bahanKajians');
        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();

        foreach ($cpls as $cpl) {
            $cpl->jumlahBk = $cpl->bahanKajians()->distinct()->count('bahan_kajians.id');
            $cpl->jumlahMk = $cpl->mataKuliahs()->distinct()->count('mata_kuliahs.id');
        }

        return view('cpl-bk-mk.index', compact('kurikulum', 'mataKuliahs', 'cpls'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
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
