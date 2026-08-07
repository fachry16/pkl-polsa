<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    public function index()
    {
        $krsList = Krs::with(['programStudi', 'mataKuliah', 'dosen.user', 'tahunAkademik', 'mahasiswas'])
            ->latest()
            ->paginate(10);

        return view('krs.index', compact('krsList'));
    }

    public function create()
    {
        $programStudis = ProgramStudi::orderBy('nama_prodi')->get();
        $mataKuliahs = MataKuliah::orderBy('kode')->get();
        $dosens = Dosen::with('user')->orderBy('user_id')->get();
        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('krs.create', compact('programStudis', 'mataKuliahs', 'dosens', 'tahunAkademiks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'kelas' => 'required|max:10',
        ]);

        $krs = Krs::create($request->all());

        $tahunAkademik = TahunAkademik::find($request->tahun_akademik_id);

        Pengampu::create([
            'krs_id' => $krs->id,
            'dosen_id' => $request->dosen_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'semester_akademik' => $tahunAkademik->semester,
            'kelas' => $request->kelas,
        ]);

        return redirect()
            ->route('krs.show', $krs)
            ->with('success', 'KRS berhasil dibuat dan muncul di menu Pengampu.');
    }

    public function show(Krs $krs)
    {
        $krs->load(['programStudi', 'mataKuliah', 'dosen.user', 'tahunAkademik', 'mahasiswas' => function ($q) {
            $q->with('programStudi')->orderBy('nim');
        }]);

        $mahasiswaIds = $krs->mahasiswas->pluck('id');

        $semuaMahasiswa = Mahasiswa::with('programStudi')
            ->whereNotIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        return view('krs.show', compact('krs', 'semuaMahasiswa'));
    }

    public function storeMahasiswa(Request $request, Krs $krs)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        $krs->mahasiswas()->syncWithoutDetaching($request->mahasiswa_id);

        if ($pengampu = $krs->pengampu) {
            $pengampu->mahasiswas()->syncWithoutDetaching($request->mahasiswa_id);
        }

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke KRS.');
    }

    public function destroyMahasiswa(Krs $krs, Mahasiswa $mahasiswa)
    {
        $krs->mahasiswas()->detach($mahasiswa->id);

        if ($pengampu = $krs->pengampu) {
            $pengampu->mahasiswas()->detach($mahasiswa->id);
        }

        return back()->with('success', 'Mahasiswa berhasil dihapus dari KRS.');
    }

    public function destroy(Krs $krs)
    {
        $krs->delete();

        return redirect()
            ->route('krs.index')
            ->with('success', 'Data KRS berhasil dihapus.');
    }
}
