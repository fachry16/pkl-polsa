<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMateri extends Model
{
    protected $fillable = [
        'pengampu_id',
        'rps_pertemuan_id',
        'judul',
        'deskripsi',
        'file_path',
        'link_external',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function rpsPertemuan()
    {
        return $this->belongsTo(RpsPertemuan::class);
    }
}
