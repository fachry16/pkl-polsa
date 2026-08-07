<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use App\Models\RpsPenilaian;
use Illuminate\Http\Request;

class RpsPenilaianController extends Controller
{
    use AuthorizesRps;

    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $penilaian = $rps->penilaian;

        return view('rps-penilaian.index', compact('rps', 'penilaian'));
    }

    public function create(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        if ($rps->penilaian) {
            return redirect()
                ->route('rps.penilaian.index', $rps)
                ->with('error', 'Penilaian sudah ada. Silakan edit.');
        }

        return view('rps-penilaian.create', compact('rps'));
    }

    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'tugas' => 'required|numeric|min:0|max:100',
            'quiz' => 'required|numeric|min:0|max:100',
            'uts' => 'required|numeric|min:0|max:100',
            'uas' => 'required|numeric|min:0|max:100',
            'praktikum' => 'required|numeric|min:0|max:100',
            'project' => 'required|numeric|min:0|max:100',
        ]);

        $total = (float) $request->tugas + (float) $request->quiz + (float) $request->uts + (float) $request->uas + (float) $request->praktikum + (float) $request->project;

        if ($total != 100) {
            return back()->withInput()->with('error', "Total bobot penilaian harus tepat 100% (saat ini $total%).");
        }

        RpsPenilaian::updateOrCreate(
            ['rps_id' => $rps->id],
            [
                'tugas' => $request->tugas,
                'quiz' => $request->quiz,
                'uts' => $request->uts,
                'uas' => $request->uas,
                'praktikum' => $request->praktikum,
                'project' => $request->project,
            ]
        );

        return redirect()
            ->route('rps.penilaian.index', $rps)
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    public function edit(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $penilaian = $rps->penilaian;

        if (! $penilaian) {
            return redirect()
                ->route('rps.penilaian.create', $rps)
                ->with('error', 'Belum ada penilaian. Silakan buat terlebih dahulu.');
        }

        return view('rps-penilaian.edit', compact('rps', 'penilaian'));
    }

    public function update(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return $this->store($request, $rps);
    }
}
