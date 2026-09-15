<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class MkCpmkController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);
        $mataKuliahs = $kurikulum->mataKuliahs()
            ->orderBy('kode')
            ->orderBy('nama')
            ->get();

        $cpmks = $kurikulum->cpmks()
            ->orderBy('kode_cpmk')
            ->get();

        return view('mk-cpmk.index', compact('kurikulum', 'mataKuliahs', 'cpmks'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        foreach ($kurikulum->mataKuliahs as $mataKuliah) {
            $pilihan = $request->input('mataKuliah.'.$mataKuliah->id, []);
            $mataKuliah->cpmks()->sync($pilihan);
        }

        return back()->with('success', 'Matriks MK-CPMK berhasil diperbarui.');
    }
}
