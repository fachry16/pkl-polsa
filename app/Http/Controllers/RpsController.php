<?php

namespace App\Http\Controllers;

use App\Models\Rps;
use App\Models\MataKuliah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RpsController extends Controller
{
    public function index(MataKuliah $mataKuliah)
    {
        $this->authorizeRps($mataKuliah);
        $rps = $mataKuliah->rps;
        return view('rps.index', compact('mataKuliah', 'rps'));
    }

    public function create(MataKuliah $mataKuliah)
    {
        $this->authorizeRps($mataKuliah);

        if ($mataKuliah->rps) {
            return redirect()
                ->route('mata-kuliah.rps.index', $mataKuliah)
                ->with('error', 'RPS sudah tersedia.');
        }

        return view('rps.create', compact('mataKuliah'));
    }

    public function store(Request $request, MataKuliah $mataKuliah)
    {
        $this->authorizeRps($mataKuliah);

        if ($mataKuliah->rps) {
            return back()->with('error', 'RPS sudah ada.');
        }

        $request->validate([
            'kode_rps' => 'nullable|string|max:50',
            'semester' => 'required|integer',
            'dosen_pengampu' => 'required|string|max:255',
            'deskripsi_mata_kuliah' => 'nullable|string',
        ]);

        Rps::create([
            'mata_kuliah_id' => $mataKuliah->id,
            'kode_rps' => $request->kode_rps,
            'semester' => $request->semester,
            'dosen_pengampu' => $request->dosen_pengampu,
            'deskripsi_mata_kuliah' => $request->deskripsi_mata_kuliah,
        ]);

        return redirect()
            ->route('mata-kuliah.rps.index', $mataKuliah)
            ->with('success', 'RPS berhasil dibuat.');
    }

    public function show(MataKuliah $mataKuliah, Rps $rps)
    {
        $this->authorizeRps($mataKuliah);
        return view('rps.show', compact('mataKuliah', 'rps'));
    }

    public function edit(MataKuliah $mataKuliah, Rps $rps)
    {
        $this->authorizeRps($mataKuliah);
        return view('rps.edit', compact('mataKuliah', 'rps'));
    }

    public function update(Request $request, MataKuliah $mataKuliah, Rps $rps)
    {
        $this->authorizeRps($mataKuliah);

        $request->validate([
            'kode_rps' => 'nullable|string|max:50',
            'semester' => 'required|integer',
            'dosen_pengampu' => 'required|string|max:255',
            'deskripsi_mata_kuliah' => 'nullable|string',
        ]);

        $rps->update([
            'kode_rps' => $request->kode_rps,
            'semester' => $request->semester,
            'dosen_pengampu' => $request->dosen_pengampu,
            'deskripsi_mata_kuliah' => $request->deskripsi_mata_kuliah,
        ]);

        return redirect()
            ->route('mata-kuliah.rps.index', $mataKuliah)
            ->with('success', 'RPS berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah, Rps $rps)
    {
        $this->authorizeRps($mataKuliah);
        $rps->delete();

        return redirect()
            ->route('mata-kuliah.rps.index', $mataKuliah)
            ->with('success', 'RPS berhasil dihapus.');
    }

    private function authorizeRps(MataKuliah $mataKuliah)
    {
        $user = auth()->user();

        if ($user->role === 'admin' || ($user->dosen && strtolower($user->dosen->jabatan) === 'kaprodi')) {
            return;
        }

        $boleh = $mataKuliah->pengampus()
            ->where('dosen_id', $user->dosen->id)
            ->exists();

        abort_unless($boleh, 403);
    }

    public function ajukan(Rps $rps)
    {
        if (!in_array($rps->status, ['Draft', 'Revisi'])) {
            return back()->with('error', 'RPS tidak dapat diajukan kembali.');
        }

        if ($rps->pertemuans()->count() == 0) {
            return back()->with('error', 'Pertemuan belum diisi.');
        }

        if (!$rps->penilaian) {
            return back()->with('error', 'Penilaian belum diisi.');
        }

        $rps->update([
            'status' => 'Diajukan',
            'catatan_revisi' => null,
        ]);

        return back()->with('success', 'RPS berhasil diajukan ke Kaprodi.');
    }

    public function pengajuan()
    {
        $user = auth()->user();

        $rpss = Rps::whereIn('status', ['Diajukan', 'Revisi', 'Disetujui'])
            ->whereHas('mataKuliah.kurikulum', function ($q) use ($user) {
                $q->where('program_studi_id', $user->dosen->program_studi_id);
            })
            ->with(['mataKuliah', 'disetujuiOleh'])
            ->latest()
            ->get();

        return view('rps.pengajuan', compact('rpss'));
    }

    public function revisi(Request $request, Rps $rps)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        $rps->update([
            'status' => 'Revisi',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        return back()->with('success', 'RPS dikembalikan untuk direvisi.');
    }

    public function setujui(Rps $rps)
    {
        if ($rps->status !== 'Diajukan') {
            return back()->with('error', 'Hanya RPS dengan status Diajukan yang dapat disetujui.');
        }

        $rps->update([
            'status' => 'Disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        return back()->with('success', 'RPS berhasil disetujui.');
    }

    public function ekstrakPdf(Rps $rps)
    {
        if ($rps->status !== 'Disetujui') {
            return back()->with('error', 'Hanya RPS yang sudah disetujui yang dapat diekstrak.');
        }

        $rps->load([
            'mataKuliah',
            'pertemuans' => fn ($q) => $q->orderBy('minggu'),
            'penilaians',
            'disetujuiOleh',
        ]);

        if (request()->has('download')) {
            $pdf = Pdf::loadView('rps.pdf', compact('rps'));

            $filename = 'RPS-' . $rps->mataKuliah->kode . '-' . $rps->mataKuliah->nama . '.pdf';

            return $pdf->download($filename);
        }

        return view('rps.preview-pdf', compact('rps'));
    }
}
