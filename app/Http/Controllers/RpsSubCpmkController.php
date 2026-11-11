<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRps;
use App\Models\Rps;
use Illuminate\Http\Request;

class RpsSubCpmkController extends Controller
{
    use AuthorizesRps;

    public function index(Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $subCpmks = $rps->subCpmkList();

        return view('rps-sub-cpmk.index', compact('rps', 'subCpmks'));
    }

    public function store(Request $request, Rps $rps)
    {
        $this->authorizeRpsModel($rps);

        $validated = $request->validate([
            'sub_cpmk' => 'required|string',
        ]);

        $items = $rps->subCpmkList();
        $items[] = $this->bersihkan($validated['sub_cpmk']);

        $rps->setSubCpmkList($items);

        return redirect()
            ->route('rps.sub-cpmk.index', $rps)
            ->with('success', 'Sub-CPMK berhasil ditambahkan.');
    }

    public function update(Request $request, Rps $rps, int $index)
    {
        $this->authorizeRpsModel($rps);

        $validated = $request->validate([
            'sub_cpmk' => 'required|string',
        ]);

        $items = $rps->subCpmkList();

        abort_unless(array_key_exists($index, $items), 404);

        $items[$index] = $this->bersihkan($validated['sub_cpmk']);

        $rps->setSubCpmkList($items);

        return redirect()
            ->route('rps.sub-cpmk.index', $rps)
            ->with('success', 'Sub-CPMK berhasil diperbarui.');
    }

    public function destroy(Rps $rps, int $index)
    {
        $this->authorizeRpsModel($rps);

        $items = $rps->subCpmkList();

        abort_unless(array_key_exists($index, $items), 404);

        unset($items[$index]);

        $rps->setSubCpmkList($items);

        return redirect()
            ->route('rps.sub-cpmk.index', $rps)
            ->with('success', 'Sub-CPMK berhasil dihapus.');
    }

    private function bersihkan(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value));
    }
}
