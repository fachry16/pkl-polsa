<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programStudis = ProgramStudi::latest()->paginate(10);

        return view('program-studi.index', compact('programStudis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('program-studi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|unique:program_studis,kode_prodi',
            'nama_prodi' => 'required',
            'jenjang' => 'required',
            'akreditasi' => 'required|in:Baik,Baik Sekali,Unggul',
        ]);
        ProgramStudi::create([
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'jenjang' => $request->jenjang,
            'akreditasi' => $request->akreditasi,
        ]);

        return redirect()->route('program-studi.index')->with('success', 'Program studi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudi $programStudi)
    {
        return redirect()->route('program-studi.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudi $programStudi)
    {
        return view('program-studi.edit', compact('programStudi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'kode_prodi' => 'required|unique:program_studis,kode_prodi,'.$programStudi->id,
            'nama_prodi' => 'required',
            'jenjang' => 'required',
            'akreditasi' => 'required|in:Baik,Baik Sekali,Unggul',
        ]);
        $programStudi->update([
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'jenjang' => $request->jenjang,
            'akreditasi' => $request->akreditasi,
        ]);

        return redirect()->route('program-studi.index')->with('success', 'Program studi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi->delete();

        return redirect()->route('program-studi.index')->with('success', 'Program studi berhasil dihapus');
    }
}
