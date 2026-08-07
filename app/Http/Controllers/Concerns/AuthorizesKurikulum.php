<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kurikulum;

trait AuthorizesKurikulum
{
    protected function authorizeKurikulumManage()
    {
        $user = auth()->user();

        abort_unless($user->isAdmin() || $user->isKaprodi(), 403);
    }

    protected function authorizeKurikulum(Kurikulum $kurikulum)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if (
            $user->isKaprodi()
            && (int) $user->dosen->program_studi_id === (int) $kurikulum->program_studi_id
        ) {
            return;
        }

        abort(403);
    }

    protected function authorizeKurikulumRead(Kurikulum $kurikulum)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isDirektur()) {
            return;
        }

        if ($user->isKaprodi() || $user->isDosen()) {
            abort_unless(
                (int) $user->dosen->program_studi_id === (int) $kurikulum->program_studi_id,
                403
            );

            return;
        }

        abort(403);
    }
}
