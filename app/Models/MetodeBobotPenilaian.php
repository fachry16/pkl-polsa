<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodeBobotPenilaian extends Model
{
    public const KOMPONEN = [
        'partisipasi' => 'Partisipasi',
        'kuis' => 'Kuis',
        'tugas_teori_individu' => 'Tugas Teori (Individu)',
        'unjuk_kerja_presentasi' => 'Unjuk Kerja (Presentasi)',
        'tes_tulis_uts' => 'Tes Tulis (UTS)',
        'tes_tulis_uas' => 'Tes Tulis (UAS)',
        'tugas_teori_kelompok' => 'Tugas Teori (Kelompok)',
        'tugas_praktikum' => 'Tugas Praktikum',
        'responsi' => 'Responsi',
    ];

    protected $fillable = [
        'kurikulum_id',
        'cpl_id',
        'mata_kuliah_id',
        'cpmk_id',
        'partisipasi',
        'kuis',
        'tugas_teori_individu',
        'unjuk_kerja_presentasi',
        'tes_tulis_uts',
        'tes_tulis_uas',
        'tugas_teori_kelompok',
        'tugas_praktikum',
        'responsi',
        'total',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function cpl()
    {
        return $this->belongsTo(Cpl::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }
}
