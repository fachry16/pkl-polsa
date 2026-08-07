<?php

namespace App\Http\Controllers;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PemenuhanCplsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kurikulum $kurikulum)
    {
        $cpls = $kurikulum->cpls()
            ->orderBy('kode_cpl')
            ->get();

        $checked = DB::table('pemenuhan_cpls')
            ->where('kurikulum_id', $kurikulum->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->cpl_id . '-' . $item->semester => true
                ];
            })
            ->toArray();

        return view(
            'pemenuhan-cpl.index',
            compact(
                'kurikulum',
                'cpls',
                'checked'
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
