<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhsApproval extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'status',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }
}
