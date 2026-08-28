<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_materi_mahasiswas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('materi_id')
                ->constrained('lms_materis')
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('dibaca_pada')->nullable();

            $table->timestamps();

            $table->unique(['materi_id', 'mahasiswa_id'], 'lms_materi_mahasiswa_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_materi_mahasiswas');
    }
};
