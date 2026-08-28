<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'jenjang',
        'akreditasi',
    ];

    public function dosens()
    {
        return $this->hasMany(Dosen::class);
    }

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function kurikulums()
    {
        return $this->hasMany(Kurikulum::class);
    }
}
