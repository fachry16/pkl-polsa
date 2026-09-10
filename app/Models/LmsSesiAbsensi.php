<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsSesiAbsensi extends Model
{
    protected $fillable = [
        'pengampu_id',
        'rps_pertemuan_id',
        'tanggal_aktual',
        'waktu_buka',
    ];

    protected $casts = [
        'tanggal_aktual' => 'date',
        'waktu_buka' => 'datetime',
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
        $nextSession = self::where('pengampu_id', $this->pengampu_id)
            ->where('id', '!=', $this->id)
            ->where('tanggal_aktual', '>', $this->tanggal_aktual)
            ->exists();

        return ! $nextSession;
    }
}
