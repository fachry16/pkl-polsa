<?php

namespace App\Http\Controllers;
use App\Models\TahunAkademik;
use App\Models\MahasiswaTahunAKademik;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAkademiks = TahunAkademik::latest()->paginate(10);
        return view('tahun-akademik.index', compact('tahunAkademiks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tahun-akademik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => 'required|in:Ganjil,Genap',
        ]);
        $cek = TahunAkademik::where('tahun', $request->tahun)->where('semester', $request->semester)->exists();
        if ($cek) {
            return back()->withInput()->withErrors(['tahun' => 'Tahun akademik dan semester sudah ada.']);
        }
        TahunAkademik::create([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'is_active' => false,
        ]);
        return redirect()->route('tahun-akademik.index')->with('success', 'Tahun akademik berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAkademik $tahunAkademik)
    {
        return redirect()->route('tahun-akademik.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAkademik $tahunAkademik)
    {
        return view('tahun-akademik.edit', compact('tahunAkademik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'tahun' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => 'required|in:Ganjil,Genap',
        ]);
        $cek = TahunAkademik::where('tahun', $request->tahun)->where('semester', $request->semester)->where('id', '!=', $tahunAkademik->id)->exists();
        if ($cek) {
            return back()->withInput()->withErrors(['tahun' => 'Tahun akademik dan semester sudah ada.']);
        }
        $tahunAkademik->update([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
        ]);
        return redirect()->route('tahun-akademik.index')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAkademik $tahunAkademik)
    {
        if ($tahunAkademik->is_active) {
            return back()->with('error', 'Semester aktif tidak dapat dihapus');
        }
        $tahunAkademik->delete();
        return redirect()->route('tahun-akademik.index')->with('success', 'Data berhasil dihapus');
    }
    public function aktifkan(TahunAkademik $tahunAkademik) 
    {
        TahunAkademik::query()->update([
            'is_active' => false
        ]);
        $tahunAkademik->update([
            'is_active' => true
        ]);
        return back()->with('success', 'Semester berhasil diaktifkan');
    }
}


