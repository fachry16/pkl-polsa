<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class PengampuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $query = Pengampu::with(['dosen', 'mataKuliah', 'tahunAkademik']);

        if ($kaprodiProdiId) {
            $query->whereHas('dosen', function ($q) use ($kaprodiProdiId) {
                $q->where('program_studi_id', $kaprodiProdiId);
            });
        }

        if ($dosenId = request('dosen_id')) {
            $query->where('dosen_id', $dosenId);
        }

        if ($mataKuliahId = request('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $mataKuliahId);
        }

        if ($tahunAkademikId = request('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $tahunAkademikId);
        }

        if ($semesterAkademik = request('semester_akademik')) {
            $query->where('semester_akademik', $semesterAkademik);
        }

        $pengampus = $query->latest()->paginate(10);

        if ($kaprodiProdiId) {
            $dosens = Dosen::where('program_studi_id', $kaprodiProdiId)
                ->with('user')
                ->orderBy('user_id')
                ->get();
            $mataKuliahs = MataKuliah::whereHas('kurikulum', function ($q) use ($kaprodiProdiId) {
                $q->where('program_studi_id', $kaprodiProdiId);
            })->orderBy('kode')->get();
        } else {
            $dosens = Dosen::with('user')->orderBy('user_id')->get();
            $mataKuliahs = MataKuliah::orderBy('kode')->get();
        }

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('pengampu.index', compact('pengampus', 'dosens', 'mataKuliahs', 'tahunAkademiks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dosens = Dosen::orderBy('user_id')->get();
        $mataKuliahs = MataKuliah::orderBy('kode')->get();
        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('pengampu.create', compact('dosens', 'mataKuliahs', 'tahunAkademiks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester_akademik' => 'required|in:Ganjil,Genap',
            'kelas' => 'nullable|max:10',
        ]);

        Pengampu::create($request->all());

        return redirect()
            ->route('pengampu.index')
            ->with(
                'success',
                'Dosen pengampu berhasil ditambahkan.'
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
    public function destroy(Pengampu $pengampu)
    {
        $pengampu->delete();

        return back()->with(
            'success',
            'Dosen pengampu berhasil dihapus.'
        );
    }

    public function lihatKelas(Pengampu $pengampu)
    {
        $this->authorizeLihatKelas($pengampu);

        $pengampu->load(['dosen.user', 'mataKuliah', 'tahunAkademik', 'mahasiswas' => function ($q) {
            $q->with('programStudi')->orderBy('nim');
        }]);

        $mahasiswaIds = $pengampu->mahasiswas->pluck('id');

        $semuaMahasiswa = Mahasiswa::with('programStudi')
            ->whereNotIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        return view('pengampu.lihat-kelas', compact('pengampu', 'semuaMahasiswa'));
    }

    public function storeMahasiswa(Request $request, Pengampu $pengampu)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        $pengampu->mahasiswas()->syncWithoutDetaching($request->mahasiswa_id);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    public function destroyMahasiswa(Pengampu $pengampu, Mahasiswa $mahasiswa)
    {
        $pengampu->mahasiswas()->detach($mahasiswa->id);

        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelas.');
    }

    private function authorizeLihatKelas(Pengampu $pengampu)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $pengampu->dosen->program_studi_id,
                403
            );

            return;
        }

        if ($user->isDosen()) {
            abort_unless((int) $pengampu->dosen_id === (int) $user->dosen->id, 403);

            return;
        }

        abort(403);
    }
}
