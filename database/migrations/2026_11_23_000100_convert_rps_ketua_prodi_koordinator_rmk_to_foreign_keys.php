<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $textColumns = collect([
            'ketua_prodi',
            'koordinator_rmk',
        ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

        $foreignKeys = collect([
            'ketua_prodi_id',
            'koordinator_rmk_id',
        ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

        if ($textColumns === [] && count($foreignKeys) === 2) {
            return;
        }

        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'ketua_prodi_id')) {
                $column = $table->foreignId('ketua_prodi_id')->nullable();
                if (Schema::hasColumn('rps', 'ketua_prodi')) {
                    $column->after('ketua_prodi');
                }
                $column->constrained('dosens')->nullOnDelete();
            }

            if (! Schema::hasColumn('rps', 'koordinator_rmk_id')) {
                $column = $table->foreignId('koordinator_rmk_id')->nullable();
                if (Schema::hasColumn('rps', 'koordinator_rmk')) {
                    $column->after('koordinator_rmk');
                }
                $column->constrained('dosens')->nullOnDelete();
            }
        });

        $dosenByNama = [];
        foreach (DB::table('dosens')
            ->join('users', 'users.id', '=', 'dosens.user_id')
            ->select('dosens.id', 'users.name')
            ->get() as $dosen) {
            $dosenByNama[$this->normalizeNama($dosen->name)] ??= $dosen->id;
        }

        $kaprodiIdsByProdi = DB::table('dosens')
            ->select('program_studi_id', 'id')
            ->where('jabatan', 'Kaprodi')
            ->whereNotNull('program_studi_id')
            ->get()
            ->groupBy('program_studi_id')
            ->map(fn ($rows) => $rows->pluck('id'));

        $updates = [];
        $unmatched = [];

        foreach (DB::table('rps')
            ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mata_kuliah_id')
            ->join('kurikulums', 'kurikulums.id', '=', 'mata_kuliahs.kurikulum_id')
            ->select(
                'rps.id',
                'rps.ketua_prodi',
                'rps.koordinator_rmk',
                'kurikulums.program_studi_id'
            )
            ->get() as $rps) {
            $update = [];
            $kaprodiIds = $kaprodiIdsByProdi->get($rps->program_studi_id, collect());

            $namaKetua = trim((string) $rps->ketua_prodi);
            $ketuaId = $namaKetua !== '' ? ($dosenByNama[$this->normalizeNama($namaKetua)] ?? null) : null;

            if ($ketuaId !== null && $kaprodiIds->contains($ketuaId)) {
                $update['ketua_prodi_id'] = $ketuaId;
            } elseif ($namaKetua !== '') {
                $unmatched[] = "rps {$rps->id}: ketua_prodi = {$namaKetua}";
            }

            $namaKoordinator = trim((string) $rps->koordinator_rmk);
            $koordinatorId = $namaKoordinator !== ''
                ? ($dosenByNama[$this->normalizeNama($namaKoordinator)] ?? null)
                : null;

            if ($koordinatorId !== null) {
                $update['koordinator_rmk_id'] = $koordinatorId;
            } elseif ($namaKoordinator !== '') {
                $unmatched[] = "rps {$rps->id}: koordinator_rmk = {$namaKoordinator}";
            }

            if ($update !== []) {
                $updates[$rps->id] = $update;
            }
        }

        foreach ($updates as $id => $update) {
            DB::table('rps')->where('id', $id)->update($update);
        }

        if ($unmatched !== []) {
            Log::warning('Backfill RPS ketua prodi dan koordinator RMK melewati data tidak cocok: '.implode('; ', $unmatched));
        }

        Schema::table('rps', function (Blueprint $table) use ($textColumns) {
            if ($textColumns !== []) {
                $table->dropColumn($textColumns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'koordinator_rmk')) {
                $table->string('koordinator_rmk')->nullable()->after('dosen_pengembang_rps_id');
            }

            if (! Schema::hasColumn('rps', 'ketua_prodi')) {
                $table->string('ketua_prodi')->nullable()->after('koordinator_rmk');
            }
        });

        foreach (DB::table('rps')->select('id', 'ketua_prodi_id', 'koordinator_rmk_id')->get() as $rps) {
            $update = [];

            if ($rps->ketua_prodi_id) {
                $update['ketua_prodi'] = DB::table('dosens')
                    ->join('users', 'users.id', '=', 'dosens.user_id')
                    ->where('dosens.id', $rps->ketua_prodi_id)
                    ->value('users.name');
            }

            if ($rps->koordinator_rmk_id) {
                $update['koordinator_rmk'] = DB::table('dosens')
                    ->join('users', 'users.id', '=', 'dosens.user_id')
                    ->where('dosens.id', $rps->koordinator_rmk_id)
                    ->value('users.name');
            }

            if ($update !== []) {
                DB::table('rps')->where('id', $rps->id)->update($update);
            }
        }

        Schema::table('rps', function (Blueprint $table) {
            $foreignKeys = collect([
                'ketua_prodi_id',
                'koordinator_rmk_id',
            ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign([$foreignKey]);
            }

            $table->dropColumn($foreignKeys);
        });
    }

    private function normalizeNama(string $nama): string
    {
        return preg_replace('/\s+/u', ' ', mb_strtolower(trim($nama)));
    }
};
