<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kurikulum;
use App\Models\MetodeBobotPenilaian;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait BuildsRumusanNilaiAkhir
{
    use BuildsPemetaanCplCpmkMk;

    protected function rumusanFormViewData(Kurikulum $kurikulum, $item = null): array
    {
        $data = $this->pemetaanViewData($kurikulum);

        $data['metode'] = $kurikulum->metodeBobotPenilaians()
            ->get()
            ->mapWithKeys(fn (MetodeBobotPenilaian $m) => [
                $m->mata_kuliah_id.'-'.$m->cpmk_id.'-'.$m->cpl_id => [
                    'total' => (float) $m->total,
                    'komponen' => collect(MetodeBobotPenilaian::KOMPONEN)
                        ->map(fn ($label, $field) => ['label' => $label, 'nilai' => (float) $m->{$field}])
                        ->values()
                        ->all(),
                ],
            ])
            ->all();

        return [
            'data' => $data,
            'initial' => [
                'mk' => (string) old('mata_kuliah_id', $item->mata_kuliah_id ?? ''),
                'cpmk' => (string) old('cpmk_id', $item->cpmk_id ?? ''),
                'cpl' => (string) old('cpl_id', $item->cpl_id ?? ''),
            ],
        ];
    }

    protected function validatedRumusanIds(Request $request, Kurikulum $kurikulum): array
    {
        return $this->validatedPemetaanIds($request, $kurikulum);
    }

    protected function validasiRelasiRumusan(Kurikulum $kurikulum, array $ids): void
    {
        $this->validasiRelasiPemetaan($kurikulum, $ids);
    }

    protected function metodeUntukRumusan(Kurikulum $kurikulum, array $ids): MetodeBobotPenilaian
    {
        $metode = $kurikulum->metodeBobotPenilaians()
            ->where('mata_kuliah_id', $ids['mata_kuliah_id'])
            ->where('cpmk_id', $ids['cpmk_id'])
            ->where('cpl_id', $ids['cpl_id'])
            ->first();

        if (! $metode) {
            throw ValidationException::withMessages([
                'cpl_id' => 'Metode & bobot penilaian untuk kombinasi MK, CPMK, dan CPL ini belum tersedia.',
            ]);
        }

        return $metode;
    }
}
