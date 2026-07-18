<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_prodi' => 'TI',  'nama_prodi' => 'Teknik Informatika',             'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali'],
            ['kode_prodi' => 'AB',  'nama_prodi' => 'Administrasi Bisnis',             'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali'],
            ['kode_prodi' => 'BD',  'nama_prodi' => 'Bisnis Digital',                  'jenjang' => 'D4', 'akreditasi' => 'Baik'],
            ['kode_prodi' => 'TRPL','nama_prodi' => 'Teknik Rekayasa Perangkat Lunak', 'jenjang' => 'D4', 'akreditasi' => 'Baik'],
            ['kode_prodi' => 'AK',  'nama_prodi' => 'Akuntansi',                       'jenjang' => 'D3', 'akreditasi' => 'Baik'],
        ];

        foreach ($data as $item) {
            ProgramStudi::create($item);
        }
    }
}
