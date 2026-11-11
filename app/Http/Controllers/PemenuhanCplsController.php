<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesKurikulum;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemenuhanCplsController extends Controller
{
    use AuthorizesKurikulum;

    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $this->authorizeKurikulumRead($kurikulum);
        $cpls = $kurikulum->cpls()
            ->orderBy('kode_cpl')
            ->get();

        $rows = DB::table('cpl_cpmk_semesters as ccs')
            ->join('cpmk_mata_kuliah as cmk', 'cmk.cpmk_id', '=', 'ccs.cpmk_id')
            ->join('mata_kuliahs as mk', 'mk.id', '=', 'cmk.mata_kuliah_id')
            ->where('ccs.kurikulum_id', $kurikulum->id)
            ->where('mk.kurikulum_id', $kurikulum->id)
            ->select('ccs.cpl_id', 'ccs.semester', 'mk.kode')
            ->orderBy('ccs.semester')
            ->orderBy('mk.kode')
            ->get();

        $matrix = [];

        foreach ($rows as $row) {
            $matrix[$row->cpl_id][$row->semester][$row->kode] = true;
        }

        foreach ($matrix as $cplId => $semesters) {
            foreach ($semesters as $semester => $kodes) {
                $matrix[$cplId][$semester] = array_keys($kodes);
            }
        }

        return view(
            'pemenuhan-cpl.index',
            compact(
                'kurikulum',
                'cpls',
                'matrix'
            )
        );
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
        $this->authorizeKurikulum($kurikulum);
        DB::table('pemenuhan_cpls')
            ->where('kurikulum_id', $kurikulum->id)
            ->delete();

        foreach ($request->mapping ?? [] as $key => $value) {

            [$cplId, $semester] = explode('-', $key);

            DB::table('pemenuhan_cpls')->insert([
                'kurikulum_id' => $kurikulum->id,
                'cpl_id' => $cplId,
                'semester' => $semester,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with(
            'success',
            'Pemenuhan CPL berhasil disimpan.'
        );
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
