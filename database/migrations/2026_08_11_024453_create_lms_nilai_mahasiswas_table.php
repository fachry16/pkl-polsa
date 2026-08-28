<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_nilai_mahasiswas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('komponen', [
                'tugas',
                'quiz',
                'uts',
                'uas',
                'praktikum',
                'project',
                'akhir',
            ]);

            $table->decimal('nilai', 5, 2)->default(0);

            $table->timestamps();

            $table->unique(['pengampu_id', 'mahasiswa_id', 'komponen'], 'lms_nilai_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_nilai_mahasiswas');
    }
};
