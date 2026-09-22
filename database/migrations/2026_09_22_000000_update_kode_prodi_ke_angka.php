<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'TI' => '11',
            'AB' => '12',
            'BD' => '13',
            'TRPL' => '14',
            'AK' => '15',
        ];

        foreach ($mapping as $lama => $baru) {
            DB::table('program_studis')->where('kode_prodi', $lama)->update(['kode_prodi' => $baru]);
        }
    }

    public function down(): void
    {
        $mapping = [
            '11' => 'TI',
            '12' => 'AB',
            '13' => 'BD',
            '14' => 'TRPL',
            '15' => 'AK',
        ];

        foreach ($mapping as $baru => $lama) {
            DB::table('program_studis')->where('kode_prodi', $baru)->update(['kode_prodi' => $lama]);
        }
    }
};
