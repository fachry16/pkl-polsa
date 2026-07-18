<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    public function index()
    {
        return redirect()->route('program-studi.index');
    }

    public function indexByProgramStudi(ProgramStudi $programStudi)
    {
        $user = auth()->user();

        if (
            $user->role !== 'admin'
            && !$this->isKaprodi()
            && $user->dosen->program_studi_id != $programStudi->id
        ) {
            abort(403);
        }

        $kurikulums = Kurikulum::where(
                'program_studi_id',
                $programStudi->id
            )
            ->latest()
            ->paginate(10);

        return view(
            'kurikulum.index',
            compact('programStudi', 'kurikulums')
        );
    }

    public function create()
    {
        if (auth()->user()->role === 'admin' || $this->isKaprodi()) {

            $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        } else {

            $programStudis = ProgramStudi::where(
                'id',
                auth()->user()->dosen->program_studi_id
            )->get();
        }

        return view(
            'kurikulum.create',
            compact('programStudis')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'nama_kurikulum'   => 'required',
            'tahun_berlaku'    => 'required|digits:4',
            'beban_studi'      => 'required',
            'deskripsi'        => 'required',
        ]);

        $programStudiId = (auth()->user()->role === 'admin' || $this->isKaprodi())
            ? $request->program_studi_id
            : auth()->user()->dosen->program_studi_id;

        Kurikulum::create([
            'program_studi_id' => $programStudiId,
            'nama_kurikulum'   => $request->nama_kurikulum,
            'tahun_berlaku'    => $request->tahun_berlaku,
            'beban_studi'      => $request->beban_studi,
            'deskripsi'        => $request->deskripsi,
            'status'           => 'Draft',
        ]);

        return redirect()
            ->route('program-studi.kurikulum', $programStudiId)
            ->with(
                'success',
                'Data kurikulum berhasil ditambahkan.'
            );
    }

    public function show(Kurikulum $kurikulum)
    {
        //
    }

    public function edit(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        if (auth()->user()->role === 'admin' || $this->isKaprodi()) {

            $programStudis = ProgramStudi::orderBy('nama_prodi')->get();

        } else {

            $programStudis = ProgramStudi::where(
                'id',
                auth()->user()->dosen->program_studi_id
            )->get();
        }

        return view(
            'kurikulum.edit',
            compact('kurikulum', 'programStudis')
        );
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'nama_kurikulum'   => 'required',
            'tahun_berlaku'    => 'required|digits:4',
            'beban_studi'      => 'required',
            'deskripsi'        => 'required',
        ]);

        $programStudiId = (auth()->user()->role === 'admin' || $this->isKaprodi())
            ? $request->program_studi_id
            : $kurikulum->program_studi_id;

        $kurikulum->update([
            'program_studi_id' => $programStudiId,
            'nama_kurikulum'   => $request->nama_kurikulum,
            'tahun_berlaku'    => $request->tahun_berlaku,
            'beban_studi'      => $request->beban_studi,
            'deskripsi'        => $request->deskripsi,
        ]);

        return redirect()
            ->route(
                'program-studi.kurikulum',
                $kurikulum->program_studi_id
            )
            ->with(
                'success',
                'Data kurikulum berhasil diperbarui.'
            );
    }

    public function destroy(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        if ($kurikulum->status === 'Aktif') {
            return back()->with(
                'error',
                'Kurikulum aktif tidak dapat dihapus.'
            );
        }

        $programStudiId = $kurikulum->program_studi_id;

        $kurikulum->delete();

        return redirect()
            ->route(
                'program-studi.kurikulum',
                $programStudiId
            )
            ->with(
                'success',
                'Data kurikulum berhasil dihapus.'
            );
    }

    public function aktifkan(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        Kurikulum::where(
            'program_studi_id',
            $kurikulum->program_studi_id
        )
        ->where('status', 'Aktif')
        ->update([
            'status' => 'Arsip'
        ]);

        $kurikulum->update([
            'status' => 'Aktif'
        ]);

        return back()->with(
            'success',
            'Kurikulum berhasil diaktifkan.'
        );
    }

    public function detail(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        if ($kurikulum->status === 'Arsip') {
            return back()->with(
                'error',
                'Kurikulum harus diaktifkan terlebih dahulu.'
            );
        }

        return view(
            'kurikulum.detail',
            compact('kurikulum')
        );
    }

    private function isKaprodi()
    {
        $user = auth()->user();

        return $user->dosen
            && strtolower($user->dosen->jabatan) === 'kaprodi';
    }

    private function authorizeKurikulum(Kurikulum $kurikulum)
    {
        if (auth()->user()->role === 'admin' || $this->isKaprodi()) {
            return;
        }

        if (
            auth()->user()->dosen->program_studi_id
            != $kurikulum->program_studi_id
        ) {
            abort(403);
        }
    }
}