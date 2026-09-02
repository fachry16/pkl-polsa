<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Rps;
use App\Notifications\RpsDiajukan;
use App\Notifications\RpsDirevisi;
use App\Notifications\RpsDisetujui;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RpsController extends Controller
{
    use AuthorizesRps;

    public function index(MataKuliah $mataKuliah)
    {
        $this->authorizeRps($mataKuliah);
        $rps = $mataKuliah->rps;

        if ($rps && auth()->check()) {
            auth()->user()->unreadNotifications()
                ->whereIn('type', [RpsDisetujui::class, RpsDirevisi::class])
                ->where('data->rps_id', $rps->id)
                ->update(['read_at' => now()]);
        }

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
            'rumpun_mk' => 'nullable|string|max:255',
            'mk_prasyarat' => 'nullable|string',
            'prasyarat_untuk' => 'nullable|string',
            'integrasi_antar_mk' => 'nullable|string',
            'tautan_daring' => 'nullable|string|max:255',
            'daftar_pustaka' => 'nullable|string',
            'dosen_pengembang_rps' => 'nullable|string|max:255',
            'koordinator_rmk' => 'nullable|string|max:255',
            'ketua_prodi' => 'nullable|string|max:255',
        ]);

        Rps::create([
            'mata_kuliah_id' => $mataKuliah->id,
            'kode_rps' => $request->kode_rps,
            'semester' => $request->semester,
            'dosen_pengampu' => $request->dosen_pengampu,
            'deskripsi_mata_kuliah' => $request->deskripsi_mata_kuliah,
            'rumpun_mk' => $request->rumpun_mk,
            'mk_prasyarat' => $request->mk_prasyarat,
            'prasyarat_untuk' => $request->prasyarat_untuk,
            'integrasi_antar_mk' => $request->integrasi_antar_mk,
            'tautan_daring' => $request->tautan_daring,
            'daftar_pustaka' => $request->daftar_pustaka,
            'dosen_pengembang_rps' => $request->dosen_pengembang_rps,
            'koordinator_rmk' => $request->koordinator_rmk,
            'ketua_prodi' => $request->ketua_prodi,
        ]);

        return redirect()
            ->route('mata-kuliah.rps.index', $mataKuliah)
            ->with('success', 'RPS berhasil dibuat.');
    }

    public function show(MataKuliah $mataKuliah, Rps $rps)
    {
        $this->authorizeRps($mataKuliah);

        return redirect()->route('mata-kuliah.rps.index', $mataKuliah);
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
            'rumpun_mk' => 'nullable|string|max:255',
            'mk_prasyarat' => 'nullable|string',
            'prasyarat_untuk' => 'nullable|string',
            'integrasi_antar_mk' => 'nullable|string',
            'tautan_daring' => 'nullable|string|max:255',
            'daftar_pustaka' => 'nullable|string',
            'dosen_pengembang_rps' => 'nullable|string|max:255',
            'koordinator_rmk' => 'nullable|string|max:255',
            'ketua_prodi' => 'nullable|string|max:255',
        ]);

        $rps->update([
            'kode_rps' => $request->kode_rps,
            'semester' => $request->semester,
            'dosen_pengampu' => $request->dosen_pengampu,
            'deskripsi_mata_kuliah' => $request->deskripsi_mata_kuliah,
            'rumpun_mk' => $request->rumpun_mk,
            'mk_prasyarat' => $request->mk_prasyarat,
            'prasyarat_untuk' => $request->prasyarat_untuk,
            'integrasi_antar_mk' => $request->integrasi_antar_mk,
            'tautan_daring' => $request->tautan_daring,
            'daftar_pustaka' => $request->daftar_pustaka,
            'dosen_pengembang_rps' => $request->dosen_pengembang_rps,
            'koordinator_rmk' => $request->koordinator_rmk,
            'ketua_prodi' => $request->ketua_prodi,
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

    public function ajukan(Rps $rps)
    {
        $this->authorizeRps($rps->mataKuliah);

        if (! in_array($rps->status, ['Draft', 'Revisi'])) {
            return back()->with('error', 'RPS tidak dapat diajukan kembali.');
        }

        if ($rps->pertemuans()->count() == 0) {
            return back()->with('error', 'Pertemuan belum diisi.');
        }

        if (! $rps->penilaian) {
            return back()->with('error', 'Penilaian belum diisi.');
        }

        $rps->update([
            'status' => 'Diajukan',
            'catatan_revisi' => null,
        ]);

        $pengaju = auth()->user()->name ?? 'Dosen';

        $kaprodis = Dosen::where('jabatan', 'Kaprodi')
            ->where('program_studi_id', $rps->mataKuliah->kurikulum->program_studi_id)
            ->with('user')
            ->get();

        foreach ($kaprodis as $kaprodi) {
            if ($kaprodi->user) {
                $kaprodi->user->notify(new RpsDiajukan($rps, $pengaju));
            }
        }

        return back()->with('success', 'RPS berhasil diajukan ke Kaprodi.');
    }

    public function pengajuan()
    {
        $user = auth()->user();

        if ($user) {
            $user->unreadNotifications()
                ->where('type', RpsDiajukan::class)
                ->update(['read_at' => now()]);
        }

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

        $peninjau = auth()->user()->name ?? 'Kaprodi';
        foreach ($rps->mataKuliah->pengampus as $pengampu) {
            if ($pengampu->dosen?->user) {
                $pengampu->dosen->user->notify(new RpsDirevisi($rps, $request->catatan_revisi, $peninjau));
            }
        }

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

        $penyetuju = auth()->user()->name ?? 'Kaprodi';
        foreach ($rps->mataKuliah->pengampus as $pengampu) {
            if ($pengampu->dosen?->user) {
                $pengampu->dosen->user->notify(new RpsDisetujui($rps, $penyetuju));
            }
        }

        $adminUsers = \App\Models\User::where('role', 'admin')->orWhereJsonContains('roles', 'admin')->get();
        foreach ($adminUsers as $admin) {
            if ($admin->id !== auth()->id()) {
                $admin->notify(new RpsDisetujui($rps, $penyetuju));
            }
        }

        return back()->with('success', 'RPS berhasil disetujui.');
    }

    public function ekstrakPdf(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        if ($rps->status !== 'Disetujui') {
            return back()->with('error', 'Hanya RPS yang sudah disetujui yang dapat diekstrak.');
        }

        $rps->load([
            'mataKuliah.kurikulum.programStudi',
            'mataKuliah.cpls',
            'mataKuliah.bahanKajians',
            'pertemuans' => fn ($q) => $q->orderBy('minggu'),
            'penilaians',
            'tugas' => fn ($q) => $q->orderBy('minggu_topik'),
            'disetujuiOleh',
        ]);

        if (Schema::hasTable('cpmk_mata_kuliah')) {
            $rps->load('mataKuliah.cpmks');
        }

        if (request()->has('download')) {
            $pdf = Pdf::loadView('rps.pdf', compact('rps'));

            $filename = 'RPS-'.$rps->mataKuliah->kode.'-'.$rps->mataKuliah->nama.'.pdf';

            return $pdf->download($filename);
        }

        return view('rps.preview-pdf', compact('rps'));
    }
}
