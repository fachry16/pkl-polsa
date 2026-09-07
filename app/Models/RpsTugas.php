<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpsTugas extends Model
{
    protected $fillable = [
        'rps_id',
        'minggu_topik',
        'nama_tugas',
        'sub_cpmk',
        'penugasan',
        'ruang_lingkup',
        'cara_pengerjaan',
        'batas_waktu',
        'luaran_tugas',
        'deadline',
        'bobot_nilai',
        'file_soal',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class);
    }

    public function lmsTugas()
    {
        return $this->hasMany(LmsTugas::class, 'rps_tugas_id');
    }
}
