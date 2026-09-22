<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rps extends Model
{
    public const JUMLAH_PERTEMUAN = 14;

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
        'rumpun_mk',
        'mk_prasyarat',
        'prasyarat_untuk',
        'integrasi_antar_mk',
        'tautan_daring',
        'daftar_pustaka',
        'dosen_pengembang_rps',
        'koordinator_rmk',
        'ketua_prodi',
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

    public function bentukEvaluasis()
    {
        return $this->hasMany(RpsBentukEvaluasi::class);
    }

    public function tugas()
    {
        return $this->hasMany(RpsTugas::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function mingguKosong(): array
    {
        $terisi = $this->pertemuans()->pluck('minggu');

        return array_values(array_diff(range(1, self::JUMLAH_PERTEMUAN), $terisi->all()));
    }

    public function kelengkapanAjukan(): array
    {
        $mingguKosong = $this->mingguKosong();
        $tugasLengkap = $this->tugas()->count() > 0;
        $penilaianLengkap = (bool) $this->penilaian;

        return [
            'pertemuan' => $this->pertemuans()->count(),
            'tugas' => $this->tugas()->count(),
            'minggu_kosong' => $mingguKosong,
            'tugas_lengkap' => $tugasLengkap,
            'penilaian_lengkap' => $penilaianLengkap,
            'siap' => empty($mingguKosong) && $tugasLengkap && $penilaianLengkap,
        ];
    }
}
