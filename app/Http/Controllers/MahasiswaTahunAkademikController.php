<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MahasiswaTahunAkademik;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class MahasiswaTahunAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TahunAkademik $tahunAkademik)
    {
        $mahasiswas = $tahunAkademik
            ->mahasiswaTahunAkademik()
            ->with('mahasiswa')
            ->paginate(10);

        return view(
            'mahasiswa-tahun-akademik.index',
            compact(
                'tahunAkademik',
                'mahasiswas'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TahunAkademik $tahunAkademik)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();

        return view(
            'mahasiswa-tahun-akademik.create',
            compact(
                'tahunAkademik',
                'mahasiswas'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'semester' => 'required|integer|min:1|max:14',
            'status' => 'required',
        ]);

        MahasiswaTahunAkademik::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester' => $request->semester,
            'status' => $request->status,
        ]);

        return redirect()
            ->route(
                'tahun-akademik.mahasiswa.index',
                $tahunAkademik->id
            )
            ->with(
                'success',
                'Mahasiswa berhasil ditambahkan.'
            );
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAkademik $tahunAkademik, MahasiswaTahunAkademik $mahasiswaTahunAkademik)
    {
        $mahasiswaTahunAkademik->delete();

        return back()->with(
            'success',
            'Data berhasil dihapus.'
        );
    }
}
