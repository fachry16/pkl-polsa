<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('rps_pertemuan_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('judul');
            $table->text('instruksi');
            $table->string('file_lampiran')->nullable();
            $table->dateTime('deadline');
            $table->integer('bobot_nilai')->default(100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_tugas');
    }
};
