<?php

namespace App\Http\Controllers;

use App\Models\LmsMateri;
use App\Models\Pengampu;
use App\Models\RpsPertemuan;
use App\Notifications\MateriBaru;
use App\Rules\LmsFileMime;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsMateriController extends Controller
{
    public function index(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $pengampu->load('mataKuliah', 'tahunAkademik');
        $materis = $pengampu->lmsMateris()->latest()->paginate(10);
        $pertemuans = $pengampu->rpsPertemuans();

        return view('lms.materi.index', compact('pengampu', 'materis', 'pertemuans'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('lms/materi', 'public');
        }

        $materi = LmsMateri::create($data);

        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new MateriBaru($pengampu, $materi));
            }
        }

        return back()->with('toast_success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Pengampu $pengampu, LmsMateri $materi)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($materi->pengampu_id !== $pengampu->id, 404);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $pertemuans = $pengampu->rpsPertemuans();

        return view('lms.materi.edit', compact('pengampu', 'materi', 'pertemuans'));
    }

    public function update(Request $request, Pengampu $pengampu, LmsMateri $materi)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($materi->pengampu_id !== $pengampu->id, 404);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'rps_pertemuan_id' => ['nullable', 'exists:rps_pertemuans,id', $this->pertemuanMilikKelas($pengampu)],
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'rps_pertemuan_id' => $request->rps_pertemuan_id,
        ];

        if ($request->hasFile('file')) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }

            $data['file_path'] = $request->file('file')->store('lms/materi', 'public');
        }

        $materi->update($data);

        return redirect()
            ->route('lms.materi.index', $pengampu->id)
            ->with('toast_success', 'Materi berhasil diperbarui.');
    }

    public function destroy(LmsMateri $materi)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $materi->pengampu->dosen_id !== $dosen->id, 403);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('toast_success', 'Materi berhasil dihapus.');
    }

    private function pertemuanMilikKelas(Pengampu $pengampu): Closure
    {
        return function ($attribute, $value, $fail) use ($pengampu) {
            if ($value === null) {
                return;
            }

            $rpsId = $pengampu->mataKuliah?->rps?->id;

            if (! $rpsId || ! RpsPertemuan::where('id', $value)->where('rps_id', $rpsId)->exists()) {
                $fail('Pertemuan tidak valid untuk mata kuliah ini.');
            }
        };
    }
}
