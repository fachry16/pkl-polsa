<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained()->cascadeOnDelete();
            $table->string('kelas', 10);
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'tahun_akademik_id', 'dosen_id', 'kelas'], 'krs_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
