<?php

namespace App\Console\Commands;

use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use Illuminate\Console\Command;

class SplitKrsClasses extends Command
{
    protected $signature = 'krs:split-classes';

    protected $description = 'Pisahkan kelas A/B berdasarkan digit ke-5 NIM dan seimbangkan data KRS yang sudah ada';

    public function handle(): int
    {
        $krsList = Krs::with(['pengampu', 'mahasiswas'])->orderBy('id')->get();

        $groups = [];
        foreach ($krsList as $krs) {
            $key = $this->grupKey($krs);
            $groups[$key][] = $krs;
        }

        $diproses = 0;
        $dilewati = 0;

        foreach ($groups as $key => $group) {
            $kelasKrs = array_filter(
                $group,
                fn (Krs $k) => str_contains($k->kelas, '/') || in_array($this->classLetter($k->kelas), ['A', 'B'])
            );

            if (empty($kelasKrs)) {
                $dilewati++;

                continue;
            }

            try {
                $this->seimbangkan($kelasKrs);
                $diproses++;
            } catch (\Throwable $e) {
                $this->error("Grup {$key} gagal: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Selesai. Grup kelas diproses: {$diproses}, dilewati: {$dilewati}.");

        return self::SUCCESS;
    }

    private function seimbangkan(array $members): void
    {
        $krsA = null;
        $krsB = null;

        foreach ($members as $krs) {
            if (str_contains($krs->kelas, '/')) {
                $krsA = $krs;
            } else {
                $letter = $this->classLetter($krs->kelas);

                if ($letter === 'A') {
                    $krsA = $krs;
                } elseif ($letter === 'B') {
                    $krsB = $krs;
                }
            }
        }

        if (! $krsA && ! $krsB) {
            return;
        }

        $ids = [];
        foreach ($members as $krs) {
            $ids = array_merge($ids, $krs->mahasiswas()->pluck('mahasiswas.id')->all());
        }
        $ids = array_values(array_unique($ids));

        if (empty($ids)) {
            $this->warn("Grup ({$this->labelGrup($krsA ?: $krsB)}) dilewati: tidak punya mahasiswa.");

            return;
        }

        $mahasiswas = Mahasiswa::whereIn('id', $ids)->get();
        $idA = [];
        $idB = [];
        $lain = [];

        foreach ($mahasiswas as $m) {
            $kelas = $this->kelasDariNim($m->nim);

            if ($kelas === 'A') {
                $idA[] = $m->id;
            } elseif ($kelas === 'B') {
                $idB[] = $m->id;
            } else {
                $lain[] = $m->id;
            }
        }

        $base = $this->baseClass(($krsA ?: $krsB)->kelas);
        $kelasA = $base.'A';
        $kelasB = $base.'B';

        $pengampuA = $krsA ? $krsA->pengampu : null;

        if (! $krsA) {
            $krsA = $this->buatKrs($krsB, $kelasA);
            $pengampuA = $krsB->pengampu
                ? $this->buatPengampu($krsA, $krsB->pengampu, $kelasA)
                : null;
        } else {
            $krsA->update(['kelas' => $kelasA]);

            if ($pengampuA) {
                $pengampuA->update(['kelas' => $kelasA]);
            }
        }

        if (! $krsB) {
            $krsB = $this->buatKrs($krsA, $kelasB);

            if ($pengampuA) {
                $this->buatPengampu($krsB, $pengampuA, $kelasB);
            }
        } else {
            $krsB->update(['kelas' => $kelasB]);

            if ($krsB->pengampu) {
                $krsB->pengampu->update(['kelas' => $kelasB]);
            }
        }

        $pengampuA = $krsA->pengampu;
        $pengampuB = $krsB->pengampu;

        $krsA->mahasiswas()->sync($idA);
        $krsB->mahasiswas()->sync($idB);

        if ($pengampuA) {
            $pengampuA->mahasiswas()->sync($idA);
        }

        if ($pengampuB) {
            $pengampuB->mahasiswas()->sync($idB);
        }

        foreach ($lain as $id) {
            foreach ($members as $krs) {
                if ($krs->mahasiswas()->where('mahasiswas.id', $id)->exists()) {
                    $krs->mahasiswas()->syncWithoutDetaching([$id]);

                    if ($krs->pengampu) {
                        $krs->pengampu->mahasiswas()->syncWithoutDetaching([$id]);
                    }
                }
            }
        }

        $this->info(
            "KRS {$krsA->id} => {$kelasA} (".count($idA)." mhs) + KRS {$krsB->id} => {$kelasB} (".count($idB).' mhs)'
        );
    }

    private function buatKrs(Krs $sumber, string $kelas): Krs
    {
        return Krs::create([
            'program_studi_id' => $sumber->program_studi_id,
            'mata_kuliah_id' => $sumber->mata_kuliah_id,
            'dosen_id' => $sumber->dosen_id,
            'tahun_akademik_id' => $sumber->tahun_akademik_id,
            'kelas' => $kelas,
        ]);
    }

    private function buatPengampu(Krs $krs, Pengampu $sumber, string $kelas): Pengampu
    {
        return Pengampu::create([
            'krs_id' => $krs->id,
            'dosen_id' => $sumber->dosen_id,
            'mata_kuliah_id' => $sumber->mata_kuliah_id,
            'tahun_akademik_id' => $sumber->tahun_akademik_id,
            'semester_akademik' => $sumber->semester_akademik,
            'kelas' => $kelas,
        ]);
    }

    private function grupKey(Krs $krs): string
    {
        return $krs->program_studi_id
            .'|'.$krs->mata_kuliah_id
            .'|'.$krs->dosen_id
            .'|'.$krs->tahun_akademik_id
            .'|'.$this->baseClass($krs->kelas);
    }

    private function labelGrup(Krs $krs): string
    {
        return $krs->program_studi_id
            .'|'.$krs->mata_kuliah_id
            .'|'.$krs->dosen_id
            .'|'.$krs->tahun_akademik_id
            .'|'.$krs->kelas;
    }

    private function baseClass(string $kelas): string
    {
        if (str_contains($kelas, '/')) {
            $part = trim(explode('/', $kelas)[0]);

            return substr($part, 0, -1);
        }

        $last = strtoupper(substr($kelas, -1));

        if (in_array($last, ['A', 'B', 'C', 'D'])) {
            return substr($kelas, 0, -1);
        }

        return $kelas;
    }

    private function classLetter(string $kelas): ?string
    {
        if (str_contains($kelas, '/')) {
            return null;
        }

        $last = strtoupper(substr($kelas, -1));

        return in_array($last, ['A', 'B', 'C', 'D']) ? $last : null;
    }

    private function kelasDariNim(string $nim): ?string
    {
        if (strlen($nim) < 5) {
            return null;
        }

        return match ($nim[4]) {
            '1' => 'A',
            '2' => 'B',
            '3' => 'C',
            '4' => 'D',
            default => null,
        };
    }
}
