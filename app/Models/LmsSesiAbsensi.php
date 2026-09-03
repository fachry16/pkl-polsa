<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsSesiAbsensi extends Model
{
    protected $fillable = [
        'pengampu_id',
        'rps_pertemuan_id',
        'tanggal_aktual',
    ];

    protected $casts = [
        'tanggal_aktual' => 'date',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function rpsPertemuan()
    {
        return $this->belongsTo(RpsPertemuan::class);
    }

    public function absensis()
    {
        return $this->hasMany(LmsAbsensi::class, 'sesi_id');
    }

    public function canEdit(): bool
    {
        return true;
    }
}
