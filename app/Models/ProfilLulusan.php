<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilLulusan extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'kode_pl',
        'nama_pl',
        'profesi',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'profil_lulusan_cpl');
    }
}
