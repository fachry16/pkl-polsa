<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_nilai_mahasiswas', function (Blueprint $table) {
            $table->string('komponen', 50)->change();
        });

        Schema::table('lms_instrumen_cpmk', function (Blueprint $table) {
            $table->string('komponen', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('lms_nilai_mahasiswas', function (Blueprint $table) {
            $table->enum('komponen', [
                'tugas',
                'quiz',
                'uts',
                'uas',
                'praktikum',
                'project',
                'akhir',
            ])->change();
        });

        Schema::table('lms_instrumen_cpmk', function (Blueprint $table) {
            $table->enum('komponen', ['tugas', 'quiz', 'uts', 'uas', 'praktikum', 'project'])->change();
        });
    }
};
