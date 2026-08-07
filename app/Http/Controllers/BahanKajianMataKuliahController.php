<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Http\Request;

class BahanKajianMataKuliahController extends Controller
{
    public function index(Kurikulum $kurikulum)
    {
        $mataKuliahs = $kurikulum->mataKuliahs()
            ->orderBy('kode')
            ->orderBy('nama')
            ->get();

        $bahanKajians = $kurikulum->bahanKajians()
            ->orderBy('kode_bk')
            ->get();

        return view('bk-mk.index', compact('kurikulum', 'mataKuliahs', 'bahanKajians'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        foreach ($kurikulum->mataKuliahs as $mataKuliah) {
            $pilihan = $request->input('mataKuliah.' . $mataKuliah->id, []);
            $mataKuliah->bahanKajians()->sync($pilihan);
        }

        return back()->with('success', 'Matriks BK-MK berhasil diperbarui.');
    }
}
