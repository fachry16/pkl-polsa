<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use Illuminate\Http\Request;

class RpsDaftarPustakaController extends Controller
{
    use AuthorizesRps;

    public function edit(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        return view('rps-daftar-pustaka.edit', compact('rps'));
    }

    public function update(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $request->validate([
            'daftar_pustaka' => 'nullable|string',
        ]);

        $rps->update([
            'daftar_pustaka' => $request->daftar_pustaka,
        ]);

        return redirect()
            ->route('rps.daftar-pustaka.edit', $rps)
            ->with('success', 'Daftar pustaka berhasil disimpan.');
    }
}
