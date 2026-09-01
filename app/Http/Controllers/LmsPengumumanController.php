<?php

namespace App\Http\Controllers;

use App\Models\LmsPengumuman;
use App\Models\Pengampu;
use App\Notifications\PengumumanBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsPengumumanController extends Controller
{
    public function index(Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $pengampu->load(['mataKuliah', 'tahunAkademik', 'dosen.user']);

        $pengumumans = $pengampu->lmsPengumumans()->with('pengampu.dosen.user')->latest()->paginate(10);

        return view('lms.pengumuman.index', compact('pengampu', 'pengumumans'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $pengumuman = LmsPengumuman::create([
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'published_at' => now(),
        ]);

        foreach ($pengampu->mahasiswas as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->notify(new PengumumanBaru($pengampu, $pengumuman));
            }
        }

        return back()->with('toast_success', 'Pengumuman berhasil dikirim.');
    }

    public function edit(Pengampu $pengampu, LmsPengumuman $pengumuman)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($pengumuman->pengampu_id !== $pengampu->id, 404);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        return view('lms.pengumuman.edit', compact('pengampu', 'pengumuman'));
    }

    public function update(Request $request, Pengampu $pengampu, LmsPengumuman $pengumuman)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($pengumuman->pengampu_id !== $pengampu->id, 404);

        if ($pengumuman->created_at->diffInMinutes(now()) > 30) {
            return redirect()
                ->route('lms.pengumuman.index', $pengampu->id)
                ->with('toast_error', 'Batas waktu edit pengumuman sudah lewat (30 menit).');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        return redirect()
            ->route('lms.pengumuman.index', $pengampu->id)
            ->with('toast_success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengampu $pengampu, LmsPengumuman $pengumuman)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
        abort_if($pengumuman->pengampu_id !== $pengampu->id, 404);

        if ($pengumuman->created_at->diffInMinutes(now()) > 30) {
            return back()->with('toast_error', 'Batas waktu hapus pengumuman sudah lewat (30 menit).');
        }

        $pengumuman->delete();

        return back()->with('toast_success', 'Pengumuman berhasil dihapus.');
    }
}
