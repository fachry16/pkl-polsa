<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rps extends Model
{
    protected $fillable = [
        'mata_kuliah_id',
        'kode_rps',
        'semester',
        'dosen_pengampu',
        'deskripsi_mata_kuliah',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_revisi',
    ];

    protected $casts = [
        'tanggal_disetujui' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function pertemuans()
    {
        return $this->hasMany(RpsPertemuan::class);
    }

    public function penilaians()
    {
        return $this->hasMany(RpsPenilaian::class);
    }

    public function penilaian()
    {
        return $this->hasOne(RpsPenilaian::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
