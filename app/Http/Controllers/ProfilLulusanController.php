<?php

namespace App\Http\Controllers;
use App\Models\ProfilLulusan;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class ProfilLulusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $profilLulusans = $kurikulum->profilLulusans()->latest()->paginate(10);
        return view(
            'profil-lulusan.index',
            compact('kurikulum', 'profilLulusans')
        );    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kurikulum $kurikulum)
    {
        return view('profil-lulusan.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'kode_pl' => 'required',
            'nama_pl' => 'required',
        ]);
        ProfilLulusan::create([
            'kurikulum_id' => $kurikulum->id,
            'kode_pl' => $request->kode_pl,
            'nama_pl' => $request->nama_pl,
            'profesi' => $request->profesi,
        ]);
        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil ditambahkan.');
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
    public function edit(Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        return view('profil-lulusan.edit', compact('kurikulum', 'profilLulusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        $request->validate([
            'kode_pl' => 'required',
            'nama_pl' => 'required',
        ]);
        $profilLulusan->update([
            'kode_pl' => $request->kode_pl,
            'nama_pl' => $request->nama_pl,
            'profesi' => $request->profesi,
        ]);
        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum, ProfilLulusan $profilLulusan)
    {
        $profilLulusan->delete();
        return redirect()->route('kurikulum.profil-lulusan.index', $kurikulum->id)->with('success', 'Profil Lulusan berhasil dihapus.');
    }
}
