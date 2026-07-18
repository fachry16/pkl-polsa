<?php

namespace App\Http\Controllers;
use App\Models\Kurikulum;
use App\Models\BahanKajian;
use Illuminate\Http\Request;

class BahanKajianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $bahanKajians = $kurikulum->bahanKajians()->latest()->paginate(10);
        return view('bahan-kajian.index', compact('kurikulum', 'bahanKajians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        return view('bahan-kajian.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'kode_bk' => 'required',
            'nama_bk' => 'required',
        ]);
        BahanKajian::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_bk' => strtoupper($request->kode_bk),
            'nama_bk' => $request->nama_bk,
            'referensi' => $request->referensi,
        ]);
        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        return view('bahan-kajian.edit', compact('kurikulum', 'bahanKajian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        $request->validate([
            'kode_bk' => 'required',
            'nama_bk' => 'required',
        ]);
        $bahanKajian->update([
            'kode_bk' => strtoupper($request->kode_bk),
            'nama_bk' => $request->nama_bk,
            'referensi' => $request->referensi,
        ]);
        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, BahanKajian $bahanKajian)
    {
        $bahanKajian->delete();
        return redirect()->route('kurikulum.bahan-kajian.index', $kurikulum->id)->with('success', 'Bahan Kajian berhasil dihapus');
    }
}
