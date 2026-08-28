<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAbsensi extends Model
{
    public const STATUS = ['hadir', 'sakit', 'izin', 'alpa'];

    protected $fillable = [
        'sesi_id',
        'mahasiswa_id',
        'status',
    ];

    public function sesi()
    {
        return $this->belongsTo(LmsSesiAbsensi::class, 'sesi_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
