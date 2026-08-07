<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\LmsMateri;
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
        $materis = $pengampu->lmsMateris()->latest()->get();

        return view('lms.materi.index', compact('pengampu', 'materis'));
    }

    public function store(Request $request, Pengampu $pengampu)
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
            'link_external' => 'nullable|string|url|max:500',
        ]);

        $data = [
            'pengampu_id' => $pengampu->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'link_external' => $request->link_external,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('lms/materi', 'public');
        }

        LmsMateri::create($data);

        return back()->with('toast_success', 'Materi berhasil ditambahkan.');
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
}
