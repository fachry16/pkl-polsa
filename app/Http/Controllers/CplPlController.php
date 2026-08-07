<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class CplPlController extends Controller
{
    use AuthorizesKurikulum;

    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $profilLulusans = $kurikulum->profilLulusans()->orderBy('kode_pl')->get();

        return view('cpl-pl.index', compact('kurikulum', 'cpls', 'profilLulusans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        foreach ($kurikulum->cpls as $cpl) {
            $pilihan = $request->input('cpl.'.$cpl->id, []);
            $cpl->profilLulusans()->sync($pilihan);
        }

        return back()->with('success', 'Matriks CPL-PL berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
