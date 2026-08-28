<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_sesi_absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('rps_pertemuan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('tanggal_aktual');

            $table->timestamps();

            $table->unique(['pengampu_id', 'rps_pertemuan_id']);
        });

        Schema::create('lms_absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesi_id')
                ->constrained('lms_sesi_absensis')
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])
                ->default('alpa');

            $table->timestamps();

            $table->unique(['sesi_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_absensis');
        Schema::dropIfExists('lms_sesi_absensis');
    }
};
