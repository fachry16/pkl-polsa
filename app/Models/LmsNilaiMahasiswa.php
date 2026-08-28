<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsNilaiMahasiswa extends Model
{
    protected $fillable = [
        'pengampu_id',
        'mahasiswa_id',
        'komponen',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
