<?php

namespace App\Http\Controllers;

use App\Models\LmsAbsensi;
use App\Models\LmsSesiAbsensi;
use App\Models\Pengampu;
use App\Models\RpsPertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsAbsensiController extends Controller
{
    private function authorizeDosen(Pengampu $pengampu): void
    {
        $dosen = Auth::user()->dosen;

        abort_if(! $dosen || $pengampu->dosen_id !== $dosen->id, 403);
    }

    public function index(Pengampu $pengampu)
    {
        $this->authorizeDosen($pengampu);

        $pengampu->load('mataKuliah', 'tahunAkademik');

        $pertemuans = $pengampu->rpsPertemuans();

        $sesis = $pengampu->lmsSesiAbsensis()
            ->with('absensis')
            ->get()
            ->keyBy('rps_pertemuan_id');

        return view('lms.absensi.index', compact('pengampu', 'pertemuans', 'sesis'));
    }

    public function bukaSesi(Request $request, Pengampu $pengampu)
    {
        $this->authorizeDosen($pengampu);

        $valid = $request->validate([
            'rps_pertemuan_id' => 'required|exists:rps_pertemuans,id',
        ]);

        $pertemuan = RpsPertemuan::findOrFail($valid['rps_pertemuan_id']);

        abort_if($pertemuan->rps_id !== $pengampu->mataKuliah?->rps?->id, 403);

        $sudahDibuka = LmsSesiAbsensi::where('pengampu_id', $pengampu->id)
            ->where('rps_pertemuan_id', $pertemuan->id)
            ->exists();

        if ($sudahDibuka) {
            return back()->with('toast_error', 'Sesi pertemuan ini sudah dibuka.');
        }

        $sesi = LmsSesiAbsensi::create([
            'pengampu_id' => $pengampu->id,
            'rps_pertemuan_id' => $pertemuan->id,
            'tanggal_aktual' => now()->toDateString(),
        ]);

        return redirect()
            ->route('lms.absensi.show', [$pengampu->id, $sesi->id])
            ->with('toast_success', 'Sesi presensi berhasil dibuka.');
    }

    public function show(Pengampu $pengampu, LmsSesiAbsensi $sesi)
    {
        $this->authorizeDosen($pengampu);
        abort_if($sesi->pengampu_id !== $pengampu->id, 404);

        $mahasiswas = $pengampu->mahasiswas()->orderBy('nim')->get();
        $absensis = $sesi->absensis()->get()->keyBy('mahasiswa_id');

        $editable = $sesi->canEdit();

        return view('lms.absensi.show', compact('pengampu', 'sesi', 'mahasiswas', 'absensis', 'editable'));
    }

    public function simpan(Request $request, Pengampu $pengampu, LmsSesiAbsensi $sesi)
    {
        $this->authorizeDosen($pengampu);
        abort_if($sesi->pengampu_id !== $pengampu->id, 404);

        if (! $sesi->canEdit()) {
            return back()->with('toast_error', 'Sesi presensi terkunci karena sesi berikutnya sudah dibuka.');
        }

        $request->validate([
            'status' => 'required|array',
            'status.*' => 'required|in:hadir,sakit,izin,alpa',
        ]);

        $mahasiswaIds = $pengampu->mahasiswas()->pluck('mahasiswas.id');

        foreach ($request->input('status') as $mahasiswaId => $status) {
            if (! $mahasiswaIds->contains($mahasiswaId)) {
                continue;
            }

            LmsAbsensi::updateOrCreate(
                ['sesi_id' => $sesi->id, 'mahasiswa_id' => $mahasiswaId],
                ['status' => $status]
            );
        }

        return back()->with('toast_success', 'Presensi berhasil disimpan.');
    }
}
