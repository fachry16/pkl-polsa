<?php

namespace App\Http\Controllers\Concerns;

use App\Models\MataKuliah;
use App\Models\Rps;

trait AuthorizesRps
{
    protected function authorizeRps(MataKuliah $mataKuliah)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isKaprodi()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $mataKuliah->kurikulum->program_studi_id,
                403
            );

            return;
        }

        if ($user->isDosen()) {
            $boleh = $mataKuliah->pengampus()
                ->where('dosen_id', $user->dosen->id)
                ->exists();

            abort_unless($boleh, 403);

            return;
        }

        abort(403);
    }

    protected function authorizeRpsModel(Rps $rps)
    {
        $this->authorizeRps($rps->mataKuliah);
    }
}
