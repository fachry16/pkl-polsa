<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Notifications\KurikulumBaruAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KurikulumController extends Controller
{
    use AuthorizesKurikulum;

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return redirect()->route('program-studi.index');
        }

        if ($user->isKaprodi()) {
            return redirect()->route('program-studi.kurikulum', $user->dosen->program_studi_id);
        }

        return redirect()->route('dosen.self');
    }

    public function indexByProgramStudi(ProgramStudi $programStudi)
    {
        $user = auth()->user();

        $boleh = $user->isAdmin()
            || $user->isDirektur()
            || (($user->isKaprodi() || $user->isDosen())
                && (int) $user->dosen->program_studi_id === (int) $programStudi->id);

        abort_unless($boleh, 403);

        if ($user->isAdmin()) {
            $user->unreadNotifications()
                ->where('type', KurikulumBaruAdmin::class)
                ->where('data->url', route('program-studi.kurikulum', $programStudi->id))
                ->update(['read_at' => now()]);
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
        $this->authorizeKurikulumManage();

        $programStudis = $this->programStudiOptions();

        return view(
            'kurikulum.create',
            compact('programStudis')
        );
    }

    public function store(Request $request)
    {
        $this->authorizeKurikulumManage();

        $request->validate([
            'program_studi_id' => 'required|exists:program_studis,id',
            'nama_kurikulum' => 'required',
            'tahun_berlaku' => 'required|digits:4',
            'beban_studi' => 'required',
            'deskripsi' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $programStudiId = $this->resolveProgramStudiId($request->program_studi_id);

        $kurikulum = Kurikulum::create([
            'program_studi_id' => $programStudiId,
            'nama_kurikulum' => $request->nama_kurikulum,
            'tahun_berlaku' => $request->tahun_berlaku,
            'beban_studi' => $request->beban_studi,
            'deskripsi' => $request->deskripsi,
            'status' => 'Draft',
            'lampiran' => $request->file('lampiran')
                ? $request->file('lampiran')->store('kurikulum/lampiran', 'public')
                : null,
        ]);

        $pembuat = auth()->user()->name ?? 'User';
        $adminUsers = User::where('role', 'admin')->orWhereJsonContains('roles', 'admin')->get();
        foreach ($adminUsers as $admin) {
            if ($admin->id !== auth()->id()) {
                $admin->notify(new KurikulumBaruAdmin($kurikulum, $pembuat));
            }
        }

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

        $programStudis = $this->programStudiOptions();

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
            'nama_kurikulum' => 'required',
            'tahun_berlaku' => 'required|digits:4',
            'beban_studi' => 'required',
            'deskripsi' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($request->file('lampiran')) {
            if ($kurikulum->lampiran) {
                Storage::disk('public')->delete($kurikulum->lampiran);
            }
            $kurikulum->lampiran = $request->file('lampiran')->store('kurikulum/lampiran', 'public');
        }

        $kurikulum->update([
            'program_studi_id' => $this->resolveProgramStudiId($request->program_studi_id),
            'nama_kurikulum' => $request->nama_kurikulum,
            'tahun_berlaku' => $request->tahun_berlaku,
            'beban_studi' => $request->beban_studi,
            'deskripsi' => $request->deskripsi,
            'lampiran' => $kurikulum->lampiran,
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

        if ($kurikulum->lampiran) {
            Storage::disk('public')->delete($kurikulum->lampiran);
        }

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
                'status' => 'Arsip',
            ]);

        $kurikulum->update([
            'status' => 'Aktif',
        ]);

        return back()->with(
            'success',
            'Kurikulum berhasil diaktifkan.'
        );
    }

    public function detail(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        if ($kurikulum->status === 'Arsip' && ! auth()->user()->isDirektur()) {
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

    private function programStudiOptions()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return ProgramStudi::orderBy('nama_prodi')->get();
        }

        return ProgramStudi::where(
            'id',
            $user->dosen->program_studi_id
        )->get();
    }

    private function resolveProgramStudiId($requestedId)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $requestedId;
        }

        return $user->dosen->program_studi_id;
    }
}
