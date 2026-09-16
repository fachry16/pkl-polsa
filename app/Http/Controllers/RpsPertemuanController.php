<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use App\Models\RpsPertemuan;
use App\Rules\LmsFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $cpmks = $rps->mataKuliah->cpmks()
            ->orderBy('kode_cpmk')
            ->get();

        return view(
            'rps-pertemuan.create',
            compact('rps', 'cpmks')
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
            'cpmk_induk' => 'nullable|string|max:255',
            'teknik_kriteria' => 'nullable|string',
            'metode_daring' => 'nullable|string',
            'metode_luring' => 'nullable|string',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'rps_id' => $rps->id,
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode ?? '',
            'pengalaman_belajar' => $request->pengalaman_belajar ?? '',
            'indikator' => $request->indikator ?? '',
            'bobot' => $request->bobot,
            'cpmk_induk' => $request->cpmk_induk,
            'teknik_kriteria' => $request->teknik_kriteria,
            'metode_daring' => $request->metode_daring,
            'metode_luring' => $request->metode_luring,
        ];

        if ($request->hasFile('file')) {
            $data['file_materi'] = $request->file('file')->store('lms/materi', 'public');
        }

        RpsPertemuan::create($data);

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

        $cpmks = $rps->mataKuliah->cpmks()
            ->orderBy('kode_cpmk')
            ->get();

        return view(
            'rps-pertemuan.edit',
            compact('rps', 'pertemuan', 'cpmks')
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
            'cpmk_induk' => 'nullable|string|max:255',
            'teknik_kriteria' => 'nullable|string',
            'metode_daring' => 'nullable|string',
            'metode_luring' => 'nullable|string',
            'file' => ['nullable', 'file', 'max:51200', new LmsFileMime],
        ]);

        $data = [
            'minggu' => $request->minggu,
            'sub_cpmk' => $request->sub_cpmk,
            'materi' => $request->materi,
            'metode' => $request->metode ?? '',
            'pengalaman_belajar' => $request->pengalaman_belajar ?? '',
            'indikator' => $request->indikator ?? '',
            'bobot' => $request->bobot,
            'cpmk_induk' => $request->cpmk_induk,
            'teknik_kriteria' => $request->teknik_kriteria,
            'metode_daring' => $request->metode_daring,
            'metode_luring' => $request->metode_luring,
        ];

        if ($request->hasFile('file')) {
            if ($pertemuan->file_materi) {
                Storage::disk('public')->delete($pertemuan->file_materi);
            }
            $data['file_materi'] = $request->file('file')->store('lms/materi', 'public');
        }

        $pertemuan->update($data);

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

        if ($pertemuan->file_materi) {
            Storage::disk('public')->delete($pertemuan->file_materi);
        }

        $pertemuan->delete();

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'Pertemuan berhasil dihapus.'
            );
    }

    /**
     * Upload materi file ke pertemuan.
     */
    public function uploadMateri(
        Request $request,
        Rps $rps,
        RpsPertemuan $pertemuan
    ) {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'file' => ['required', 'file', 'max:51200', new LmsFileMime],
        ]);

        if ($pertemuan->file_materi) {
            Storage::disk('public')->delete($pertemuan->file_materi);
        }

        $pertemuan->update([
            'file_materi' => $request->file('file')->store('lms/materi', 'public'),
        ]);

        return redirect()
            ->route('rps.pertemuan.index', $rps->id)
            ->with(
                'success',
                'File materi untuk pertemuan minggu '.$pertemuan->minggu.' berhasil diunggah.'
            );
    }
}
