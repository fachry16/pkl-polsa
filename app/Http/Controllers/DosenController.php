<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dosens = Dosen::with([
            'user',
            'programStudi',
        ])
            ->when($request->program_studi_id, function ($q) use ($request) {
                $q->where('program_studi_id', $request->program_studi_id);
            })
            ->when($request->jabatan, function ($q) use ($request) {
                $q->where('jabatan', $request->jabatan);
            })
            ->latest()->paginate(10);

        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('dosen.index', compact('dosens', 'programStudis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('dosen.create', compact('programStudis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'nidn' => 'required|unique:dosens,nidn',
            'program_studi_id' => 'required|exists:program_studis,id',
            'jabatan' => 'required|in:dosen,kaprodi',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'dosen',
            'password' => Hash::make($request->nidn),
        ]);
        Dosen::create([
            'user_id' => $user->id,
            'program_studi_id' => $request->program_studi_id,
            'nidn' => $request->nidn,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('dosen.index')->with('success', 'Dosen berhasil ditambahkan. Password default menggunakan NIDN.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function self()
    {
        $dosen = auth()->user()->dosen;

        if (! $dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan.');
        }

        $dosen->load(['user', 'programStudi']);

        return view('dosen.show', compact('dosen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dosen $dosen)
    {
        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('dosen.edit', compact('dosen', 'programStudis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$dosen->user_id,
            'nidn' => 'required|unique:dosens,nidn,'.$dosen->id,
            'program_studi_id' => 'required|exists:program_studis,id',
            'jabatan' => 'required|in:dosen,kaprodi',
        ]);
        $dosen->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        $dosen->update([
            'program_studi_id' => $request->program_studi_id,
            'nidn' => $request->nidn,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('dosen.index')->with('succcess', 'Data dosen berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dosen $dosen)
    {
        $dosen->user->delete();

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus');
    }

    public function riwayat(Dosen $dosen)
    {
        $riwayat = Pengampu::with([
            'mataKuliah.rps',
            'tahunAkademik',
        ])
            ->where('dosen_id', $dosen->id)
            ->orderByDesc('tahun_akademik_id')
            ->orderBy('semester_akademik')
            ->get();

        return view(
            'dosen.riwayat',
            compact(
                'dosen',
                'riwayat'
            )
        );
    }

    public function riwayatSelf()
    {
        $dosen = auth()->user()->dosen;

        if (! $dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan.');
        }

        return $this->riwayat($dosen);
    }
}
