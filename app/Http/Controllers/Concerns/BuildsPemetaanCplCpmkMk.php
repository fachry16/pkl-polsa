<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait BuildsPemetaanCplCpmkMk
{
    protected function pemetaanViewData(Kurikulum $kurikulum): array
    {
        $mataKuliahOptions = [];
        $cpmksByMk = [];
        $cplsByCpmk = [];

        $mataKuliahs = $kurikulum->mataKuliahs()
            ->with(['cpmks.cpls'])
            ->orderBy('kode')
            ->get();

        foreach ($mataKuliahs as $mk) {
            $mataKuliahOptions[] = ['id' => $mk->id, 'label' => $mk->kode.' - '.$mk->nama];

            $cpmksByMk[$mk->id] = $mk->cpmks
                ->unique('id')
                ->sortBy('kode_cpmk')
                ->map(fn ($cpmk) => [
                    'id' => $cpmk->id,
                    'label' => $cpmk->kode_cpmk.($cpmk->deskripsi ? ' - '.Str::limit($cpmk->deskripsi, 70) : ''),
                ])
                ->values()
                ->all();

            foreach ($mk->cpmks as $cpmk) {
                $cplsByCpmk[$cpmk->id] = $cpmk->cpls
                    ->unique('id')
                    ->sortBy('kode_cpl')
                    ->map(fn ($cpl) => [
                        'id' => $cpl->id,
                        'label' => $cpl->kode_cpl.($cpl->deskripsi ? ' - '.Str::limit($cpl->deskripsi, 70) : ''),
                    ])
                    ->values()
                    ->all();
            }
        }

        return [
            'mataKuliahs' => $mataKuliahOptions,
            'cpmksByMk' => $cpmksByMk,
            'cplsByCpmk' => $cplsByCpmk,
        ];
    }

    protected function validatedPemetaanIds(Request $request, Kurikulum $kurikulum): array
    {
        return $request->validate([
            'mata_kuliah_id' => [
                'required',
                Rule::exists('mata_kuliahs', 'id')->where('kurikulum_id', $kurikulum->id),
            ],
            'cpmk_id' => [
                'required',
                Rule::exists('cpmks', 'id')->where('kurikulum_id', $kurikulum->id),
            ],
            'cpl_id' => [
                'required',
                Rule::exists('cpls', 'id')->where('kurikulum_id', $kurikulum->id),
            ],
        ]);
    }

    protected function validasiRelasiPemetaan(Kurikulum $kurikulum, array $ids): void
    {
        $mataKuliah = $kurikulum->mataKuliahs()->whereKey($ids['mata_kuliah_id'])->first();
        $cpmk = $kurikulum->cpmks()->whereKey($ids['cpmk_id'])->first();
        $cpl = $kurikulum->cpls()->whereKey($ids['cpl_id'])->first();

        if (! $mataKuliah || ! $cpmk || ! $cpl) {
            throw ValidationException::withMessages([
                'mata_kuliah_id' => 'Data MK, CPMK, atau CPL tidak sesuai kurikulum ini.',
            ]);
        }

        if (! $mataKuliah->cpmks()->whereKey($cpmk->id)->exists()) {
            throw ValidationException::withMessages([
                'cpmk_id' => 'CPMK tidak terhubung dengan mata kuliah yang dipilih.',
            ]);
        }

        if (! $cpmk->cpls()->whereKey($cpl->id)->exists()) {
            throw ValidationException::withMessages([
                'cpl_id' => 'CPL tidak terhubung dengan CPMK yang dipilih.',
            ]);
        }
    }
}
