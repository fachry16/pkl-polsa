<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use App\Models\RpsBentukEvaluasi;
use Illuminate\Http\Request;

class RpsBentukEvaluasiController extends Controller
{
    use AuthorizesRps;

    /**
     * Menampilkan daftar rancangan evaluasi.
     */
    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $bentukEvaluasis = $rps->bentukEvaluasis()
            ->orderBy('id')
            ->get();

        $totalBobot = $bentukEvaluasis->sum('bobot');

        return view('rps-bentuk-evaluasi.index', compact('rps', 'bentukEvaluasis', 'totalBobot'));
    }

    /**
     * Form tambah bentuk evaluasi.
     */
    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-bentuk-evaluasi.create', compact('rps'));
    }

    /**
     * Simpan bentuk evaluasi.
     */
    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $data = $this->validated($request);

        RpsBentukEvaluasi::create($data + ['rps_id' => $rps->id]);

        return redirect()
            ->route('rps.bentuk-evaluasi.index', $rps->id)
            ->with('success', 'Bentuk evaluasi berhasil ditambahkan.');
    }

    /**
     * Form edit.
     */
    public function edit(Rps $rps, RpsBentukEvaluasi $bentukEvaluasi)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-bentuk-evaluasi.edit', compact('rps', 'bentukEvaluasi'));
    }

    /**
     * Update bentuk evaluasi.
     */
    public function update(Request $request, Rps $rps, RpsBentukEvaluasi $bentukEvaluasi)
    {
        $this->authorizeRpsModel($rps);

        $data = $this->validated($request);

        $bentukEvaluasi->update($data);

        return redirect()
            ->route('rps.bentuk-evaluasi.index', $rps->id)
            ->with('success', 'Bentuk evaluasi berhasil diperbarui.');
    }

    /**
     * Hapus bentuk evaluasi.
     */
    public function destroy(Rps $rps, RpsBentukEvaluasi $bentukEvaluasi)
    {
        $this->authorizeRpsModel($rps);

        $bentukEvaluasi->delete();

        return redirect()
            ->route('rps.bentuk-evaluasi.index', $rps->id)
            ->with('success', 'Bentuk evaluasi berhasil dihapus.');
    }

    /**
     * Validasi input dan bangun data bersih.
     */
    protected function validated(Request $request)
    {
        $data = $request->validate([
            'bentuk_evaluasi' => 'required|string|max:255',
            'sub_cpmk' => 'nullable|string|max:255',
            'instrumen' => 'nullable|string',
            'frekuensi' => 'nullable|string|max:255',
            'tagihan' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'formatif' => 'nullable',
            'sumatif' => 'nullable',
        ]);

        $data['bobot'] = (float) $request->bobot;
        $data['formatif'] = $request->boolean('formatif');
        $data['sumatif'] = $request->boolean('sumatif');

        return $data;
    }
}
