<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RpsPertemuanController extends Controller
{
    use AuthorizesRps;

    /**
     * Menampilkan daftar pertemuan.
     */
    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $pertemuans = $rps->pertemuans()
            ->orderBy('minggu')
            ->paginate(16);

        return view(
            'rps-pertemuan.index',
            compact('rps', 'pertemuans')
        );
    }

    /**
     * Form tambah pertemuan.
     */
    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return view(
            'rps-pertemuan.create',
            compact('rps')
        );
    }

    /**
     * Simpan pertemuan.
     */
    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu' => [
                'required',
                'integer',
                'between:1,16',
                Rule::unique('rps_pertemuans')
                    ->where(function ($query) use ($rps) {
                        return $query->where('rps_id', $rps->id);
                    }),
            ],

            'sub_cpmk' => 'required',
            'materi' => 'required',
            'metode' => 'nullable|max:255',
            'pengalaman_belajar' => 'nullable',
            'indikator' => 'nullable',
            'bobot' => 'nullable|numeric|min:0|max:100',
        ]);

        RpsPertemuan::create([
            'rps_id' => $rps->id,
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode,
            'pengalaman_belajar' => $request->pengalaman_belajar,
            'indikator' => $request->indikator,
            'bobot' => $request->bobot,
        ]);

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil ditambahkan.'
            );
    }

    /**
     * Form edit.
     */
    public function edit(Rps $rps, RpsPertemuan $pertemuan)
    {
        $this->authorizeRpsModel($rps);

        return view(
            'rps-pertemuan.edit',
            compact('rps', 'pertemuan')
        );
    }

    /**
     * Update pertemuan.
     */
    public function update(
        Request $request,
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'minggu' => [
                'required',
                'integer',
                'between:1,16',
                Rule::unique('rps_pertemuans')
                    ->ignore($pertemuan->id)
                    ->where(function ($query) use ($rps) {
                        return $query->where('rps_id', $rps->id);
                    }),
            ],

            'sub_cpmk' => 'required',
            'materi' => 'required',
            'metode' => 'nullable|max:255',
            'pengalaman_belajar' => 'nullable',
            'indikator' => 'nullable',
            'bobot' => 'nullable|numeric|min:0|max:100',
        ]);

        $pertemuan->update([
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode,
            'pengalaman_belajar' => $request->pengalaman_belajar,
            'indikator' => $request->indikator,
            'bobot' => $request->bobot,
        ]);

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil diperbarui.'
            );
    }

    /**
     * Hapus pertemuan.
     */
    public function destroy(
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        $pertemuan->delete();

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil dihapus.'
            );
    }
}
