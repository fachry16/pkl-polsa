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
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class);
    }
}
