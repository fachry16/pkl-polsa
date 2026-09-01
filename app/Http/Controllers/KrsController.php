<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $krsList = Krs::with(['programStudi', 'mataKuliah', 'dosen.user', 'tahunAkademik', 'mahasiswas'])
            ->when($kaprodiProdiId, function ($q) use ($kaprodiProdiId) {
                $q->where('program_studi_id', $kaprodiProdiId);
            })
            ->latest()
            ->paginate(10);

        return view('krs.index', compact('krsList'));
    }

    public function create()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $mataKuliahs = $kaprodiProdiId
            ? MataKuliah::whereHas('kurikulum', fn ($q) => $q->where('program_studi_id', $kaprodiProdiId))->orderBy('kode')->get()
            : MataKuliah::orderBy('kode')->get();

        $dosens = $kaprodiProdiId
            ? Dosen::where('program_studi_id', $kaprodiProdiId)->with('user')->orderBy('user_id')->get()
            : Dosen::with('user')->orderBy('user_id')->get();

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('krs.create', compact('programStudis', 'mataKuliahs', 'dosens', 'tahunAkademiks'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'kelas' => 'required|max:10',
        ]);

        $programStudiId = $kaprodiProdiId ?: $request->program_studi_id;

        $krs = Krs::create(
            array_merge($request->all(), ['program_studi_id' => $programStudiId])
        );

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
        $this->authorizeKrsRead($krs);

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
        $this->authorizeKrs($krs);

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
        $this->authorizeKrs($krs);

        $krs->mahasiswas()->detach($mahasiswa->id);

        if ($pengampu = $krs->pengampu) {
            $pengampu->mahasiswas()->detach($mahasiswa->id);
        }

        return back()->with('success', 'Mahasiswa berhasil dihapus dari KRS.');
    }

    public function destroy(Krs $krs)
    {
        $this->authorizeKrs($krs);

        $krs->delete();

        return redirect()
            ->route('krs.index')
            ->with('success', 'Data KRS berhasil dihapus.');
    }

    public function cetakPilih()
    {
        $user = auth()->user();
        $kaprodiProdiId = $user->isKaprodi()
            ? (int) $user->dosen->program_studi_id
            : null;

        $programStudis = $kaprodiProdiId
            ? ProgramStudi::where('id', $kaprodiProdiId)->orderBy('nama_prodi')->get()
            : ProgramStudi::orderBy('nama_prodi')->get();

        $tahunAkademiks = TahunAkademik::orderByDesc('tahun')->get();

        return view('krs.cetak-pilih', compact('programStudis', 'tahunAkademiks'));
    }

    public function pilihMahasiswa(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        return redirect()->route('krs.cetak', [
            $request->mahasiswa_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
        ]);
    }

    public function cetak(Mahasiswa $mahasiswa)
    {
        $this->authorizeCetak($mahasiswa);

        $tahunAkademikId = request('tahun_akademik_id');

        $kelas = $this->kelasMahasiswa($mahasiswa, $tahunAkademikId);

        $tahunAkademik = $tahunAkademikId
            ? TahunAkademik::find($tahunAkademikId)
            : null;

        return view('krs.cetak', compact('mahasiswa', 'kelas', 'tahunAkademik'));
    }

    public function cetakPdf(Mahasiswa $mahasiswa)
    {
        $this->authorizeCetak($mahasiswa);

        $tahunAkademikId = request('tahun_akademik_id');

        $kelas = $this->kelasMahasiswa($mahasiswa, $tahunAkademikId);

        $tahunAkademik = $tahunAkademikId
            ? TahunAkademik::find($tahunAkademikId)
            : null;

        $pdf = Pdf::loadView('krs.pdf', compact('mahasiswa', 'kelas', 'tahunAkademik'));

        $filename = 'KRS-'.$mahasiswa->nim.'-'.$mahasiswa->nama.'.pdf';

        return $pdf->download($filename);
    }

    public function mahasiswaOptions(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
        ]);

        $mahasiswas = Mahasiswa::with('programStudi')
            ->where('program_studi_id', $request->program_studi_id)
            ->orderBy('nim')
            ->get();

        return response()->json($mahasiswas->map(fn ($m) => [
            'id' => $m->id,
            'nim' => $m->nim,
            'nama' => $m->nama,
            'label' => $m->nim.' - '.$m->nama,
        ]));
    }

    private function kelasMahasiswa(Mahasiswa $mahasiswa, $tahunAkademikId = null)
    {
        return $mahasiswa->pengampus()
            ->with([
                'mataKuliah',
                'dosen.user',
                'tahunAkademik',
                'krs',
            ])
            ->when($tahunAkademikId, function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->orderBy('mata_kuliah_id')
            ->get();
    }

    private function authorizeCetak(Mahasiswa $mahasiswa)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $mahasiswa->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }

    private function authorizeKrsRead(Krs $krs)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $krs->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }

    private function authorizeKrs(Krs $krs)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $krs->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }
}
