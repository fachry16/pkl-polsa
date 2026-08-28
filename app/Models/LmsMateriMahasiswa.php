<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMateriMahasiswa extends Model
{
    protected $fillable = [
        'materi_id',
        'mahasiswa_id',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function materi()
    {
        return $this->belongsTo(LmsMateri::class, 'materi_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
