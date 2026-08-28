<?php

namespace App\Services;

use App\Models\LmsNilaiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use App\Models\Rps;

class PenilaianService
{
    public const KOMPONEN = ['tugas', 'quiz', 'uts', 'uas', 'praktikum', 'project'];

    /**
     * Hitung nilai komponen "tugas" seorang mahasiswa di sebuah kelas.
     * Nilai tugas = Σ(nilai × bobot) / Σ(bobot), hanya tugas yang sudah dinilai.
     */
    public function hitungTugas(Pengampu $pengampu, Mahasiswa $mahasiswa): ?float
    {
        $tugasList = $pengampu->lmsTugas()->with('submissions')->get();

        $total = 0;
        $bobot = 0;

        foreach ($tugasList as $tugas) {
            $submission = $tugas->submissions
                ->where('mahasiswa_id', $mahasiswa->id)
                ->first();

            if ($submission && $submission->nilai !== null) {
                $total += (float) $submission->nilai * (float) $tugas->bobot_nilai;
                $bobot += (float) $tugas->bobot_nilai;
            }
        }

        return $bobot > 0 ? round($total / $bobot, 2) : null;
    }

    /**
     * Ambil bobot komponen penilaian dari RPS mata kuliah.
     * Bobot diambil dari rps_penilaians (persen, total harus 100).
     */
    public function bobotKomponen(Pengampu $pengampu): array
    {
        $default = array_fill_keys(self::KOMPONEN, 0);

        $rps = Rps::where('mata_kuliah_id', $pengampu->mata_kuliah_id)->first();

        if (! $rps || ! $rps->penilaian) {
            return $default;
        }

        foreach (self::KOMPONEN as $komponen) {
            $default[$komponen] = (float) $rps->penilaian->{$komponen};
        }

        return $default;
    }

    /**
     * Hitung nilai akhir seorang mahasiswa.
     * Nilai akhir = Σ(komponen_nilai × bobot) / Σ(bobot_yang_terisi),
     * dinormalisasi ke komponen yang sudah terisi agar rekap parsial tetap adil.
     */
    public function hitungNilaiAkhir(Pengampu $pengampu, Mahasiswa $mahasiswa): ?float
    {
        $bobot = $this->bobotKomponen($pengampu);

        $total = 0;
        $bobotTerisi = 0;

        foreach (self::KOMPONEN as $komponen) {
            if ($komponen === 'tugas') {
                $nilai = $this->hitungTugas($pengampu, $mahasiswa);
            } else {
                $nilai = LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('komponen', $komponen)
                    ->value('nilai');
            }

            if ($nilai !== null) {
                $total += (float) $nilai * (float) $bobot[$komponen];
                $bobotTerisi += (float) $bobot[$komponen];
            }
        }

        if ($bobotTerisi <= 0) {
            return null;
        }

        return round($total / $bobotTerisi, 2);
    }

    /**
     * Simpan nilai per-komponen untuk semua mahasiswa di kelas ke tabel lms_nilai_mahasiswas.
     * Dipanggil saat nilai tugas berubah.
     */
    public function simpanNilaiKelas(Pengampu $pengampu): void
    {
        $mahasiswas = $pengampu->mahasiswas()->get();

        foreach ($mahasiswas as $mahasiswa) {
            $this->simpanNilaiMahasiswa($pengampu, $mahasiswa);
        }
    }

    public function simpanNilaiMahasiswa(Pengampu $pengampu, Mahasiswa $mahasiswa): void
    {
        $this->updateKomponen($pengampu, $mahasiswa, 'tugas', $this->hitungTugas($pengampu, $mahasiswa));
        $this->updateKomponen($pengampu, $mahasiswa, 'akhir', $this->hitungNilaiAkhir($pengampu, $mahasiswa));
    }

    public function updateKomponen(Pengampu $pengampu, Mahasiswa $mahasiswa, string $komponen, ?float $nilai): void
    {
        if ($nilai === null) {
            LmsNilaiMahasiswa::where('pengampu_id', $pengampu->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('komponen', $komponen)
                ->delete();

            return;
        }

        LmsNilaiMahasiswa::updateOrCreate(
            ['pengampu_id' => $pengampu->id, 'mahasiswa_id' => $mahasiswa->id, 'komponen' => $komponen],
            ['nilai' => $nilai]
        );
    }
}
