<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsSubmission extends Model
{
    protected $fillable = [
        'lms_tugas_id',
        'mahasiswa_id',
        'file_jawaban',
        'catatan_mahasiswa',
        'nilai',
        'catatan_dosen',
        'dikumpulkan_pada',
    ];

    public function lmsTugas()
    {
        return $this->belongsTo(LmsTugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function isTerlambat(): bool
    {
        if (! $this->lmsTugas || ! $this->lmsTugas->deadline) {
            return false;
        }

        return $this->dikumpulkan_pada->gt($this->lmsTugas->deadline);
    }
}
