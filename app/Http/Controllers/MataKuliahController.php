<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    use AuthorizesKurikulum;

    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        $mataKuliahs = $kurikulum->mataKuliahs()
            ->latest()
            ->paginate(10);

        return view('mata-kuliah.index', compact('kurikulum', 'mataKuliahs'));
    }

    public function create(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('mata-kuliah.create', compact('kurikulum'));
    }

    public function store(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'kode' => 'required|max:20',
            'nama' => 'required',
            'sks_teori' => 'required|integer|min:0|max:6',
            'sks_praktikum' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        $cek = MataKuliah::where('kurikulum_id', $kurikulum->id)
            ->where('kode', $request->kode)
            ->exists();

        if ($cek) {
            return back()
                ->withInput()
                ->withErrors(['kode' => 'Kode mata kuliah sudah digunakan pada kurikulum ini.']);
        }

        MataKuliah::create([
            'kurikulum_id' => $kurikulum->id,
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'sks_teori' => $request->sks_teori,
            'sks_praktikum' => $request->sks_praktikum,
            'semester' => $request->semester,
            'jenis' => $request->jenis,
        ]);

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function show(MataKuliah $mataKuliah)
    {
        //
    }

    public function edit(Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);

        return view('mata-kuliah.edit', compact('mataKuliah', 'kurikulum'));
    }

    public function update(Request $request, Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);
        $request->validate([
            'nama' => 'required',
            'sks_teori' => 'required|integer|min:0|max:6',
            'sks_praktikum' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
        ]);

        $mataKuliah->update([
            'nama' => $request->nama,
            'sks_teori' => $request->sks_teori,
            'sks_praktikum' => $request->sks_praktikum,
            'semester' => $request->semester,
            'jenis' => $request->jenis,
        ]);

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum, MataKuliah $mataKuliah)
    {
        $this->authorizeKurikulum($kurikulum);
        $mataKuliah->delete();

        return redirect()
            ->route('kurikulum.mata-kuliah.index', $kurikulum->id)
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function struktur(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);

        $mataKuliahs = $kurikulum->mataKuliahs()
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        return view('mata-kuliah.struktur', compact('kurikulum', 'mataKuliahs'));
    }
}
