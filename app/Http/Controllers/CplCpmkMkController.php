<?php

namespace App\Http\Controllers;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CplCpmkMkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $cpls = $kurikulum->cpls()->orderBy('kode_cpl')->get();
        $cpmks = $kurikulum->cpmks()->orderBy('kode_cpmk')->get();
        $checked = DB::table('cpl_cpmk_semesters')->where('kurikulum_id', $kurikulum->id)->get()->mapWithKeys(function ($item) {
            return [
                $item->cpl_id.'-'.$item->cpmk_id.'-'.$item->semester => true
            ];
        })->toArray();
        return view('cpl-cpmk-mk.index', compact('kurikulum', 'cpls', 'cpmks', 'checked'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kurikulum $kurikulum)
    {
        DB::table('cpl_cpmk_semesters')->where('kurikulum_id', $kurikulum->id)->delete();
        foreach ($request->mapping ?? [] as $key => $value) {
            [$cplId, $cpmkId, $semester] = explode('-', $key);
            DB::table('cpl_cpmk_semesters')->insert([
                'kurikulum_id' => $kurikulum->id,
                'cpl_id' => $cplId,
                'cpmk_id' => $cpmkId,
                'semester' => $semester,
            ]);
        }
        return back()->with('success', 'Pemetaan CPL-CPMK-MK berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
