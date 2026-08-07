<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lms_tugas_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('file_jawaban')->nullable();
            $table->text('catatan_mahasiswa')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->dateTime('dikumpulkan_pada');

            $table->timestamps();

            $table->unique(['lms_tugas_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_submissions');
    }
};
