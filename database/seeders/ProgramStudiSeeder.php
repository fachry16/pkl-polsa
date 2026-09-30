<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_prodi' => '11',  'nama_prodi' => 'Teknik Informatika',             'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali'],
            ['kode_prodi' => '12',  'nama_prodi' => 'Administrasi Bisnis',             'jenjang' => 'D3', 'akreditasi' => 'Baik Sekali'],
            ['kode_prodi' => '13',  'nama_prodi' => 'Bisnis Digital',                  'jenjang' => 'D4', 'akreditasi' => 'Baik'],
            ['kode_prodi' => '14',  'nama_prodi' => 'Teknik Rekayasa Perangkat Lunak', 'jenjang' => 'D4', 'akreditasi' => 'Baik'],
            ['kode_prodi' => '15',  'nama_prodi' => 'Akuntansi',                       'jenjang' => 'D3', 'akreditasi' => 'Baik'],
        ];

        foreach ($data as $item) {
            ProgramStudi::firstOrCreate(['kode_prodi' => $item['kode_prodi']], $item);
        }
    }
}
