<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class BahanKajianMataKuliahController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
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
        $this->authorizeKurikulum($kurikulum);
        foreach ($kurikulum->mataKuliahs as $mataKuliah) {
            $pilihan = $request->input('mataKuliah.'.$mataKuliah->id, []);
            $mataKuliah->bahanKajians()->sync($pilihan);
        }

        return back()->with('success', 'Matriks BK-MK berhasil diperbarui.');
    }
}
