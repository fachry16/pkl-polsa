<?php

namespace App\Http\Controllers;

use App\Models\LmsTopikKomentar;
use App\Models\Pengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsTopikKomentarController extends Controller
{
    public function store(Request $request, Pengampu $pengampu)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        $mahasiswa = $user->mahasiswa;

        $isDosen = $dosen && $pengampu->dosen_id === $dosen->id;
        $isMhs = $mahasiswa && $pengampu->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists();

        abort_unless($isDosen || $isMhs, 403);

        $validated = $request->validate([
            'tipe_topik' => 'required|in:tugas,materi',
            'topik_id' => 'required|integer',
            'pesan' => 'required|string|max:5000',
            'is_private' => 'nullable|boolean',
            'mahasiswa_id' => 'nullable|exists:mahasiswas,id',
        ]);

        $isPrivate = $request->boolean('is_private');
        $targetMahasiswaId = null;

        if ($isPrivate) {
            if ($isMhs) {
                $targetMahasiswaId = $mahasiswa->id;
            } elseif ($isDosen) {
                $targetMahasiswaId = $validated['mahasiswa_id'] ?? null;
            }
        }

        LmsTopikKomentar::create([
            'tipe_topik' => $validated['tipe_topik'],
            'topik_id' => $validated['topik_id'],
            'user_id' => $user->id,
            'mahasiswa_id' => $targetMahasiswaId,
            'is_private' => $isPrivate,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('toast_success', 'Komentar berhasil dikirim.');
    }

    public function destroy(Pengampu $pengampu, LmsTopikKomentar $komentar)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        $isDosen = $dosen && $pengampu->dosen_id === $dosen->id;

        if ($isDosen) {
            $komentar->delete();
            return back()->with('toast_success', 'Komentar berhasil dihapus.');
        }

        abort_unless($komentar->user_id === $user->id, 403);
        abort_unless($komentar->isWithinTimeLimit(15), 403, 'Batas waktu 15 menit untuk menghapus komentar telah berakhir.');

        $komentar->delete();

        return back()->with('toast_success', 'Komentar berhasil dihapus.');
    }
}
