<?php

namespace App\Http\Controllers;

use App\Models\Cpmk;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class CpmkController extends Controller
{
    public function index(Kurikulum $kurikulum)
    {
        $cpmks = $kurikulum->cpmks()->latest()->paginate(10);
        return view('cpmk.index', compact('kurikulum', 'cpmks'));
    }

    public function create(Kurikulum $kurikulum)
    {
        return view('cpmk.create', compact('kurikulum'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'kode_cpmk' => 'required',
            'deskripsi' => 'required',
        ]);

        Cpmk::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpmk' => strtoupper($request->kode_cpmk),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil ditambahkan.');
    }

    public function show(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        //
    }

    public function edit(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        return view('cpmk.edit', compact('kurikulum', 'cpmk'));
    }

    public function update(Request $request, Kurikulum $kurikulum, Cpmk $cpmk)
    {
        $request->validate([
            'kode_cpmk' => 'required',
            'deskripsi' => 'required',
        ]);

        $cpmk->update([
            'kode_cpmk' => strtoupper($request->kode_cpmk),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, Cpmk $cpmk)
    {
        $cpmk->delete();

        return redirect()
            ->route('kurikulum.cpmk.index', $kurikulum->id)
            ->with('success', 'CPMK berhasil dihapus.');
    }
}
