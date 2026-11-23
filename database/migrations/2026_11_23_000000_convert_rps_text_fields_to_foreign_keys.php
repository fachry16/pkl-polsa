<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $textColumns = collect([
            'dosen_pengampu',
            'rumpun_mk',
            'dosen_pengembang_rps',
        ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

        $foreignKeys = collect([
            'dosen_pengampu_id',
            'rumpun_mk_id',
            'dosen_pengembang_rps_id',
        ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

        if ($textColumns === [] && count($foreignKeys) === 3) {
            return;
        }

        if (! Schema::hasTable('rumpun_mks')) {
            Schema::create('rumpun_mks', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->timestamps();
            });
        }

        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'dosen_pengampu_id')) {
                $column = $table->foreignId('dosen_pengampu_id')->nullable();
                if (Schema::hasColumn('rps', 'dosen_pengampu')) {
                    $column->after('dosen_pengampu');
                }
                $column->constrained('dosens')->nullOnDelete();
            }

            if (! Schema::hasColumn('rps', 'rumpun_mk_id')) {
                $column = $table->foreignId('rumpun_mk_id')->nullable();
                if (Schema::hasColumn('rps', 'rumpun_mk')) {
                    $column->after('rumpun_mk');
                }
                $column->constrained('rumpun_mks')->nullOnDelete();
            }

            if (! Schema::hasColumn('rps', 'dosen_pengembang_rps_id')) {
                $column = $table->foreignId('dosen_pengembang_rps_id')->nullable();
                if (Schema::hasColumn('rps', 'dosen_pengembang_rps')) {
                    $column->after('dosen_pengembang_rps');
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

        $pengampusByMk = DB::table('pengampus')
            ->select('mata_kuliah_id', 'dosen_id')
            ->get()
            ->groupBy('mata_kuliah_id')
            ->map(fn ($rows) => $rows->pluck('dosen_id')->unique()->values());

        $rumpunNames = [];
        $updates = [];
        $unmatched = [];

        foreach (DB::table('rps')->select(
            'id',
            'mata_kuliah_id',
            'dosen_pengampu',
            'rumpun_mk',
            'dosen_pengembang_rps'
        )->get() as $rps) {
            $update = [];
            $pengampuIds = $pengampusByMk->get($rps->mata_kuliah_id, collect());
            $namaPengampu = $this->normalizeNama((string) $rps->dosen_pengampu);

            foreach ([
                'dosen_pengampu' => 'dosen_pengampu_id',
                'dosen_pengembang_rps' => 'dosen_pengembang_rps_id',
            ] as $column => $foreignKey) {
                $nama = trim((string) $rps->{$column});
                $key = $nama === '' ? '' : $this->normalizeNama($nama);
                $matchedDosenId = $nama !== '' ? ($dosenByNama[$key] ?? null) : null;

                if ($matchedDosenId !== null
                    && ($pengampuIds->isEmpty() || $pengampuIds->contains($matchedDosenId))) {
                    $update[$foreignKey] = $matchedDosenId;

                    continue;
                }

                $pakaiPengampus = $pengampuIds->count() === 1
                    && ($column === 'dosen_pengampu' || $key === $namaPengampu);

                if ($pakaiPengampus) {
                    $update[$foreignKey] = $pengampuIds->first();

                    continue;
                }

                if ($column === 'dosen_pengampu') {
                    $unmatched[] = $nama === ''
                        ? "rps {$rps->id}: dosen_pengampu kosong"
                        : "rps {$rps->id}: {$column} = {$nama}";
                }
            }

            $rumpun = trim((string) $rps->rumpun_mk);

            if ($rumpun !== '') {
                $rumpunNames[$this->normalizeNama($rumpun)] = $rumpun;
            }

            if ($update !== []) {
                $updates[$rps->id] = $update;
            }
        }

        if ($unmatched !== []) {
            throw new RuntimeException('Backfill RPS gagal. Data tidak cocok: '.implode('; ', $unmatched));
        }

        foreach ($rumpunNames as $rumpun) {
            DB::table('rumpun_mks')->insertOrIgnore([
                'nama' => $rumpun,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $rumpunIds = [];
        foreach (DB::table('rumpun_mks')->select('id', 'nama')->get() as $rumpun) {
            $rumpunIds[$this->normalizeNama($rumpun->nama)] = $rumpun->id;
        }

        foreach (DB::table('rps')->select('id', 'rumpun_mk')->get() as $rps) {
            $rumpun = trim((string) $rps->rumpun_mk);

            if ($rumpun === '') {
                continue;
            }

            $updates[$rps->id]['rumpun_mk_id'] = $rumpunIds[$this->normalizeNama($rumpun)];
        }

        foreach ($updates as $id => $update) {
            DB::table('rps')->where('id', $id)->update($update);
        }

        $textColumns = collect([
            'dosen_pengampu',
            'rumpun_mk',
            'dosen_pengembang_rps',
        ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

        Schema::table('rps', function (Blueprint $table) use ($textColumns) {
            if (Schema::hasColumn('rps', 'dosen_pengampu_id')) {
                $table->dropForeign(['dosen_pengampu_id']);
                $table->unsignedBigInteger('dosen_pengampu_id')->nullable(false)->change();
                $table->foreign('dosen_pengampu_id')
                    ->references('id')
                    ->on('dosens')
                    ->restrictOnDelete();
            }

            if ($textColumns !== []) {
                $table->dropColumn($textColumns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('rps', function (Blueprint $table) {
            if (! Schema::hasColumn('rps', 'dosen_pengampu')) {
                $table->string('dosen_pengampu')->nullable()->after('kode_rps');
            }

            if (! Schema::hasColumn('rps', 'rumpun_mk')) {
                $table->string('rumpun_mk')->nullable()->after('dosen_pengampu_id');
            }

            if (! Schema::hasColumn('rps', 'dosen_pengembang_rps')) {
                $table->string('dosen_pengembang_rps')->nullable()->after('rumpun_mk_id');
            }
        });

        foreach (DB::table('rps')->select(
            'id',
            'dosen_pengampu_id',
            'rumpun_mk_id',
            'dosen_pengembang_rps_id'
        )->get() as $rps) {
            $update = [];

            if ($rps->dosen_pengampu_id) {
                $update['dosen_pengampu'] = DB::table('dosens')
                    ->join('users', 'users.id', '=', 'dosens.user_id')
                    ->where('dosens.id', $rps->dosen_pengampu_id)
                    ->value('users.name');
            }

            if ($rps->rumpun_mk_id) {
                $update['rumpun_mk'] = DB::table('rumpun_mks')
                    ->where('id', $rps->rumpun_mk_id)
                    ->value('nama');
            }

            if ($rps->dosen_pengembang_rps_id) {
                $update['dosen_pengembang_rps'] = DB::table('dosens')
                    ->join('users', 'users.id', '=', 'dosens.user_id')
                    ->where('dosens.id', $rps->dosen_pengembang_rps_id)
                    ->value('users.name');
            }

            if ($update !== []) {
                DB::table('rps')->where('id', $rps->id)->update($update);
            }
        }

        Schema::table('rps', function (Blueprint $table) {
            $foreignKeys = collect([
                'dosen_pengampu_id',
                'rumpun_mk_id',
                'dosen_pengembang_rps_id',
            ])->filter(fn ($column) => Schema::hasColumn('rps', $column))->values()->all();

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign([$foreignKey]);
            }

            $table->dropColumn($foreignKeys);
            $table->string('dosen_pengampu')->nullable(false)->change();
        });

        Schema::dropIfExists('rumpun_mks');
    }

    private function normalizeNama(string $nama): string
    {
        return preg_replace('/\s+/u', ' ', mb_strtolower(trim($nama)));
    }
};
