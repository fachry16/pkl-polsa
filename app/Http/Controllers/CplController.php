<?php

namespace App\Http\Controllers;
use App\Models\Cpl;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class CplController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $cpls = $kurikulum->cpls()->latest()->paginate(10);
        return view('cpl.index', compact('kurikulum', 'cpls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        return view('cpl.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'kode_cpl' => 'required',
            'deskripsi' => 'required',
        ]);
        Cpl::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_cpl' => strtoupper($request->kode_cpl),
            'deskripsi' => $request->deskripsi,
        ]);
        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil ditambahkan,');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum, Cpl $cpl)
    {
        return view('cpl.edit',compact('kurikulum', 'cpl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, Cpl $cpl)
    {
        $request->validate([
            'kode_cpl' => 'required',
            'deskripsi' => 'required',
        ]);
        $cpl->update([
            'kode_cpl' => strtoupper($request->kode_cpl),
            'deskripsi' => $request->deskripsi,
        ]);
        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, Cpl $cpl)
    {
        $cpl->delete();
        return redirect()->route('kurikulum.cpl.index', $kurikulum->id)->with('success', 'CPL berhasil dihapus.');
    }
}
